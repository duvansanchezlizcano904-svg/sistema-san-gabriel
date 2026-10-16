<?php
/**
 * Modelo de Venta
 * Gestiona el registro de ventas con transacción atómica, descuento de inventario
 * y consulta de historial y reportes.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Producto.php';

class Venta {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Genera el siguiente número consecutivo de venta (Ej: VNT-00004)
     */
    public function generarNumeroVenta(): string {
        $sql = "SELECT id FROM ventas ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->query($sql);
        $ultimoId = $stmt->fetchColumn();
        $siguiente = ($ultimoId ? (int)$ultimoId : 0) + 1;
        return 'VNT-' . str_pad((string)$siguiente, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Registra una venta completa con múltiples o un solo producto.
     * Utiliza una transacción PDO para asegurar integridad referencial y descuento seguro de stock.
     */
    public function registrarVenta(array $datosVenta, array $items): int {
        if (empty($items)) {
            throw new Exception("La venta debe contener al menos un producto.");
        }

        $this->db->beginTransaction();

        try {
            $modeloProducto = new Producto();

            // 1. Validar disponibilidad de stock de todos los productos primero
            $totalCalculado = 0.0;
            $itemsValidados = [];

            foreach ($items as $item) {
                $productoId = (int)$item['producto_id'];
                $cantidad = (int)$item['cantidad'];

                if ($cantidad <= 0) {
                    throw new Exception("La cantidad solicitada debe ser mayor a cero.");
                }

                $producto = $modeloProducto->obtenerPorId($productoId);
                if (!$producto) {
                    throw new Exception("El producto con ID #{$productoId} no fue encontrado.");
                }

                if ($producto['estado'] !== 'activo') {
                    throw new Exception("El producto '{$producto['nombre']}' se encuentra inactivo y no puede ser vendido.");
                }

                if ($producto['cantidad'] < $cantidad) {
                    throw new Exception("Stock insuficiente para '{$producto['nombre']}'. Disponible: {$producto['cantidad']} {$producto['unidad_medida']}(s), solicitado: {$cantidad}.");
                }

                $precioUnitario = (float)$producto['precio_venta'];
                $subtotal = $precioUnitario * $cantidad;
                $totalCalculado += $subtotal;

                $itemsValidados[] = [
                    'producto_id'     => $productoId,
                    'nombre'          => $producto['nombre'],
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal'        => $subtotal
                ];
            }

            // 2. Insertar cabecera de la venta
            $numeroVenta = $this->generarNumeroVenta();
            $sqlVenta = "INSERT INTO ventas (numero_venta, fecha, total, cliente, notas, usuario_id) 
                         VALUES (:numero_venta, :fecha, :total, :cliente, :notas, :usuario_id)";
            $stmtVenta = $this->db->prepare($sqlVenta);
            $stmtVenta->execute([
                ':numero_venta' => $numeroVenta,
                ':fecha'        => $datosVenta['fecha'] ?? date('Y-m-d H:i:s'),
                ':total'        => $totalCalculado,
                ':cliente'      => !empty($datosVenta['cliente']) ? $datosVenta['cliente'] : 'Cliente Mostrador',
                ':notas'        => $datosVenta['notas'] ?? null,
                ':usuario_id'   => $datosVenta['usuario_id'] ?? null
            ]);

            $ventaId = (int)$this->db->lastInsertId();

            // 3. Insertar detalles y descontar inventario
            $sqlDetalle = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal) 
                           VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario, :subtotal)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);

            foreach ($itemsValidados as $item) {
                // Insertar detalle
                $stmtDetalle->execute([
                    ':venta_id'        => $ventaId,
                    ':producto_id'     => $item['producto_id'],
                    ':cantidad'        => $item['cantidad'],
                    ':precio_unitario' => $item['precio_unitario'],
                    ':subtotal'        => $item['subtotal']
                ]);

                // Descontar inventario usando la misma conexión de transacción
                $descontado = $modeloProducto->descontarStock($item['producto_id'], $item['cantidad'], $this->db);
                if (!$descontado) {
                    throw new Exception("Error al actualizar el inventario del producto '{$item['nombre']}'.");
                }
            }

            // Confirmar transacción
            $this->db->commit();
            return $ventaId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene el listado de ventas para el historial con filtros de fecha y producto
     */
    public function listarHistorial(string $fechaInicio = '', string $fechaFin = '', ?int $productoId = null): array {
        $sql = "SELECT DISTINCT 
                    v.id, 
                    v.numero_venta, 
                    v.fecha, 
                    v.total, 
                    v.cliente,
                    v.notas,
                    u.nombre as usuario_nombre,
                    (SELECT COUNT(*) FROM detalle_ventas dv WHERE dv.venta_id = v.id) as total_items,
                    (SELECT SUM(dv.cantidad) FROM detalle_ventas dv WHERE dv.venta_id = v.id) as unidades_vendidas
                FROM ventas v
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN detalle_ventas dv_filter ON v.id = dv_filter.venta_id
                WHERE 1=1";

        $params = [];

        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(v.fecha) >= :fechaInicio";
            $params[':fechaInicio'] = $fechaInicio;
        }

        if (!empty($fechaFin)) {
            $sql .= " AND DATE(v.fecha) <= :fechaFin";
            $params[':fechaFin'] = $fechaFin;
        }

        if (!empty($productoId)) {
            $sql .= " AND dv_filter.producto_id = :productoId";
            $params[':productoId'] = $productoId;
        }

        $sql .= " ORDER BY v.fecha DESC, v.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene una venta completa por ID junto con todos sus productos detallados
     */
    public function obtenerPorId(int $id): ?array {
        $sqlVenta = "SELECT v.*, u.nombre as usuario_nombre 
                     FROM ventas v 
                     LEFT JOIN usuarios u ON v.usuario_id = u.id 
                     WHERE v.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sqlVenta);
        $stmt->execute([':id' => $id]);
        $venta = $stmt->fetch();

        if (!$venta) {
            return null;
        }

        $sqlDetalles = "SELECT dv.*, p.nombre as producto_nombre, p.codigo as producto_codigo, p.unidad_medida 
                        FROM detalle_ventas dv
                        INNER JOIN productos p ON dv.producto_id = p.id
                        WHERE dv.venta_id = :id";
        $stmtDetalles = $this->db->prepare($sqlDetalles);
        $stmtDetalles->execute([':id' => $id]);
        $venta['detalles'] = $stmtDetalles->fetchAll();

        return $venta;
    }

    /**
     * Obtiene las últimas N ventas para el Dashboard
     */
    public function obtenerUltimasVentas(int $limite = 5): array {
        $sql = "SELECT v.id, v.numero_venta, v.fecha, v.total, v.cliente,
                       COUNT(dv.id) as cantidad_lineas,
                       SUM(dv.cantidad) as total_unidades
                FROM ventas v
                LEFT JOIN detalle_ventas dv ON v.id = dv.venta_id
                GROUP BY v.id
                ORDER BY v.fecha DESC, v.id DESC
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene métricas financieras globales de ventas
     */
    public function obtenerEstadisticas(): array {
        $sql = "SELECT 
                    COUNT(id) as total_ventas,
                    COALESCE(SUM(total), 0) as total_ingresos,
                    COALESCE(SUM(CASE WHEN DATE(fecha) = CURDATE() THEN total ELSE 0 END), 0) as ingresos_hoy,
                    COUNT(CASE WHEN DATE(fecha) = CURDATE() THEN 1 ELSE NULL END) as ventas_hoy,
                    COALESCE(SUM(CASE WHEN MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) THEN total ELSE 0 END), 0) as ingresos_mes
                FROM ventas";
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
}

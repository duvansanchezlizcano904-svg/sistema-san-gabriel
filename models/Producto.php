<?php
/**
 * Modelo de Producto
 * Gestiona el catálogo de productos lácteos y el inventario.
 */

require_once __DIR__ . '/../config/database.php';

class Producto {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Lista todos los productos con soporte para búsqueda y filtros avanzados
     */
    public function listarTodos(
        string $busqueda = '', 
        string $tipo = '', 
        string $estado = '',
        string $categoria = '',
        string $tipoLeche = '',
        string $intensidad = ''
    ): array {
        $sql = "SELECT * FROM productos WHERE 1=1";
        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (nombre LIKE :busqueda OR codigo LIKE :busqueda OR descripcion LIKE :busqueda)";
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        if (!empty($tipo)) {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $tipo;
        }

        if (!empty($estado)) {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        if (!empty($categoria)) {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        if (!empty($tipoLeche)) {
            $sql .= " AND tipo_leche = :tipo_leche";
            $params[':tipo_leche'] = $tipoLeche;
        }

        if (!empty($intensidad)) {
            $sql .= " AND intensidad_sabor = :intensidad";
            $params[':intensidad'] = $intensidad;
        }

        $sql .= " ORDER BY categoria ASC, nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un producto por su ID
     */
    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT * FROM productos WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Verifica si un código ya está en uso (opcionalmente ignorando un ID en caso de edición)
     */
    public function existeCodigo(string $codigo, ?int $ignorarId = null): bool {
        $sql = "SELECT id FROM productos WHERE codigo = :codigo";
        $params = [':codigo' => $codigo];

        if ($ignorarId !== null) {
            $sql .= " AND id != :ignorarId";
            $params[':ignorarId'] = $ignorarId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    /**
     * Registra un nuevo producto lácteo
     */
    public function crear(array $datos): bool {
        $sql = "INSERT INTO productos 
                (codigo, nombre, tipo, categoria, tipo_leche, intensidad_sabor, tiempo_maduracion, 
                 peso_presentacion, origen, descripcion, precio_compra, precio_venta, cantidad, 
                 stock_minimo, unidad_medida, estado) 
                VALUES 
                (:codigo, :nombre, :tipo, :categoria, :tipo_leche, :intensidad_sabor, :tiempo_maduracion,
                 :peso_presentacion, :origen, :descripcion, :precio_compra, :precio_venta, :cantidad, 
                 :stock_minimo, :unidad_medida, :estado)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':codigo'             => $datos['codigo'],
            ':nombre'             => $datos['nombre'],
            ':tipo'               => $datos['tipo'],
            ':categoria'          => $datos['categoria'] ?? null,
            ':tipo_leche'         => $datos['tipo_leche'] ?? null,
            ':intensidad_sabor'   => $datos['intensidad_sabor'] ?? 'medio',
            ':tiempo_maduracion'  => $datos['tiempo_maduracion'] ?? null,
            ':peso_presentacion'  => $datos['peso_presentacion'] ?? null,
            ':origen'             => $datos['origen'] ?? null,
            ':descripcion'        => $datos['descripcion'] ?? '',
            ':precio_compra'      => $datos['precio_compra'],
            ':precio_venta'       => $datos['precio_venta'],
            ':cantidad'           => $datos['cantidad'],
            ':stock_minimo'       => $datos['stock_minimo'],
            ':unidad_medida'      => $datos['unidad_medida'],
            ':estado'             => $datos['estado'] ?? 'activo'
        ]);
    }

    /**
     * Actualiza un producto existente
     */
    public function actualizar(int $id, array $datos): bool {
        $sql = "UPDATE productos SET 
                codigo = :codigo,
                nombre = :nombre,
                tipo = :tipo,
                categoria = :categoria,
                tipo_leche = :tipo_leche,
                intensidad_sabor = :intensidad_sabor,
                tiempo_maduracion = :tiempo_maduracion,
                peso_presentacion = :peso_presentacion,
                origen = :origen,
                descripcion = :descripcion,
                precio_compra = :precio_compra,
                precio_venta = :precio_venta,
                cantidad = :cantidad,
                stock_minimo = :stock_minimo,
                unidad_medida = :unidad_medida,
                estado = :estado
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'                 => $id,
            ':codigo'             => $datos['codigo'],
            ':nombre'             => $datos['nombre'],
            ':tipo'               => $datos['tipo'],
            ':categoria'          => $datos['categoria'] ?? null,
            ':tipo_leche'         => $datos['tipo_leche'] ?? null,
            ':intensidad_sabor'   => $datos['intensidad_sabor'] ?? 'medio',
            ':tiempo_maduracion'  => $datos['tiempo_maduracion'] ?? null,
            ':peso_presentacion'  => $datos['peso_presentacion'] ?? null,
            ':origen'             => $datos['origen'] ?? null,
            ':descripcion'        => $datos['descripcion'] ?? '',
            ':precio_compra'      => $datos['precio_compra'],
            ':precio_venta'       => $datos['precio_venta'],
            ':cantidad'           => $datos['cantidad'],
            ':stock_minimo'       => $datos['stock_minimo'],
            ':unidad_medida'      => $datos['unidad_medida'],
            ':estado'             => $datos['estado'] ?? 'activo'
        ]);
    }

    /**
     * Elimina un producto. Si tiene ventas históricas, evita la eliminación o marca error controlado
     */
    public function eliminar(int $id): bool {
        // Verificar si tiene ventas asociadas
        $sqlCheck = "SELECT COUNT(*) FROM detalle_ventas WHERE producto_id = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $id]);
        $tieneVentas = $stmtCheck->fetchColumn();

        if ($tieneVentas > 0) {
            throw new Exception("No se puede eliminar el producto porque tiene ventas registradas en el historial. Puedes cambiar su estado a 'Inactivo' para ocultarlo.");
        }

        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Descuenta del inventario la cantidad vendida dentro de una transacción
     */
    public function descontarStock(int $id, int $cantidadVendida, ?PDO $conn = null): bool {
        $db = $conn ?: $this->db;
        $sql = "UPDATE productos SET cantidad = cantidad - :cantidad WHERE id = :id AND cantidad >= :cantidad";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':cantidad' => $cantidadVendida,
            ':id'       => $id
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Obtiene estadísticas globales de productos e inventario
     */
    public function obtenerEstadisticas(): array {
        $sql = "SELECT 
                    COUNT(*) as total_productos,
                    SUM(CASE WHEN cantidad > stock_minimo THEN 1 ELSE 0 END) as disponibles,
                    SUM(CASE WHEN cantidad <= stock_minimo AND cantidad > 0 THEN 1 ELSE 0 END) as stock_bajo,
                    SUM(CASE WHEN cantidad <= 0 THEN 1 ELSE 0 END) as agotados,
                    SUM(cantidad * precio_venta) as valor_inventario_venta,
                    SUM(cantidad * precio_compra) as valor_inventario_costo
                FROM productos 
                WHERE estado = 'activo'";
        $stmt = $this->db->query($sql);
        $stats = $stmt->fetch();
        
        return [
            'total_productos' => (int)($stats['total_productos'] ?? 0),
            'disponibles'     => (int)($stats['disponibles'] ?? 0),
            'stock_bajo'      => (int)($stats['stock_bajo'] ?? 0),
            'agotados'        => (int)($stats['agotados'] ?? 0),
            'valor_venta'     => (float)($stats['valor_inventario_venta'] ?? 0),
            'valor_costo'     => (float)($stats['valor_inventario_costo'] ?? 0),
        ];
    }

    /**
     * Obtiene los productos con stock bajo o agotados para alertas rápidas
     */
    public function obtenerProductosCriticos(): array {
        $sql = "SELECT * FROM productos 
                WHERE estado = 'activo' AND cantidad <= stock_minimo 
                ORDER BY cantidad ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los tipos de productos registrados para filtros dinámicos
     */
    public function obtenerTipos(): array {
        $sql = "SELECT DISTINCT tipo FROM productos WHERE tipo IS NOT NULL ORDER BY tipo ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtiene las categorías de productos registradas para filtros dinámicos
     */
    public function obtenerCategorias(): array {
        $sql = "SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL ORDER BY categoria ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtiene los tipos de leche registrados para filtros dinámicos
     */
    public function obtenerTiposLeche(): array {
        $sql = "SELECT DISTINCT tipo_leche FROM productos WHERE tipo_leche IS NOT NULL ORDER BY tipo_leche ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtiene las intensidades de sabor disponibles
     */
    public function obtenerIntensidades(): array {
        return ['suave', 'medio', 'fuerte', 'muy fuerte'];
    }

    /**
     * Obtiene estadísticas por categoría
     */
    public function obtenerEstadisticasPorCategoria(): array {
        $sql = "SELECT 
                    categoria,
                    COUNT(*) as total,
                    SUM(cantidad) as stock_total,
                    AVG(precio_venta) as precio_promedio
                FROM productos 
                WHERE estado = 'activo' AND categoria IS NOT NULL
                GROUP BY categoria
                ORDER BY categoria ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}

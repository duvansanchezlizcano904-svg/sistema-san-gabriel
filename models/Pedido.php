<?php
/**
 * Modelo de Pedido
 * Gestiona los pedidos realizados por clientes desde el carrito.
 */
require_once __DIR__ . '/../config/database.php';

class Pedido {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /** Genera número consecutivo: PED-00001 */
    public function generarNumeroPedido(): string {
        $stmt = $this->db->query("SELECT id FROM pedidos ORDER BY id DESC LIMIT 1");
        $ultimo = $stmt->fetchColumn();
        $siguiente = ($ultimo ? (int)$ultimo : 0) + 1;
        return 'PED-' . str_pad((string)$siguiente, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Registra un pedido completo desde el carrito de sesión.
     * Descuenta stock usando una transacción atómica.
     */
    public function registrarPedido(array $datosPedido, array $items): int {
        if (empty($items)) {
            throw new Exception('El carrito está vacío.');
        }

        $this->db->beginTransaction();
        try {
            $total = 0.0;
            foreach ($items as $item) {
                // Verificar stock real en BD
                $stmt = $this->db->prepare(
                    "SELECT id, nombre, cantidad, precio_venta, unidad_medida, estado
                     FROM productos WHERE id = :id LIMIT 1"
                );
                $stmt->execute([':id' => $item['producto_id']]);
                $prod = $stmt->fetch();

                if (!$prod || $prod['estado'] !== 'activo') {
                    throw new Exception("El producto '{$item['nombre']}' ya no está disponible.");
                }
                if ($prod['cantidad'] < $item['cantidad']) {
                    throw new Exception(
                        "Stock insuficiente para '{$prod['nombre']}'. " .
                        "Disponible: {$prod['cantidad']} {$prod['unidad_medida']}."
                    );
                }
                $total += $item['subtotal'];
            }

            // Insertar cabecera
            $numero = $this->generarNumeroPedido();
            $stmtP = $this->db->prepare(
                "INSERT INTO pedidos (numero_pedido, fecha, total, cliente_id, notas, estado)
                 VALUES (:numero, :fecha, :total, :cliente_id, :notas, 'pendiente')"
            );
            $stmtP->execute([
                ':numero'     => $numero,
                ':fecha'      => date('Y-m-d H:i:s'),
                ':total'      => $total,
                ':cliente_id' => $datosPedido['cliente_id'],
                ':notas'      => $datosPedido['notas'] ?? null,
            ]);
            $pedidoId = (int)$this->db->lastInsertId();

            // Insertar detalles y descontar stock
            $stmtD = $this->db->prepare(
                "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
                 VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario, :subtotal)"
            );
            $stmtStock = $this->db->prepare(
                "UPDATE productos SET cantidad = cantidad - :qty WHERE id = :id AND cantidad >= :qty"
            );
            foreach ($items as $item) {
                $stmtD->execute([
                    ':pedido_id'      => $pedidoId,
                    ':producto_id'    => $item['producto_id'],
                    ':cantidad'       => $item['cantidad'],
                    ':precio_unitario'=> $item['precio_unitario'],
                    ':subtotal'       => $item['subtotal'],
                ]);
                $stmtStock->execute([':qty' => $item['cantidad'], ':id' => $item['producto_id']]);
                if ($stmtStock->rowCount() === 0) {
                    throw new Exception("No se pudo descontar stock de '{$item['nombre']}'.");
                }
            }

            $this->db->commit();
            return $pedidoId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Historial de pedidos de un cliente */
    public function listarPorCliente(int $clienteId): array {
        $stmt = $this->db->prepare(
            "SELECT p.*, COUNT(dp.id) as total_items
             FROM pedidos p
             LEFT JOIN detalle_pedidos dp ON p.id = dp.pedido_id
             WHERE p.cliente_id = :id
             GROUP BY p.id
             ORDER BY p.fecha DESC"
        );
        $stmt->execute([':id' => $clienteId]);
        return $stmt->fetchAll();
    }

    /** Detalle completo de un pedido */
    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM pedidos WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $pedido = $stmt->fetch();
        if (!$pedido) return null;

        $stmtD = $this->db->prepare(
            "SELECT dp.*, pr.nombre as producto_nombre, pr.unidad_medida
             FROM detalle_pedidos dp
             INNER JOIN productos pr ON dp.producto_id = pr.id
             WHERE dp.pedido_id = :id"
        );
        $stmtD->execute([':id' => $id]);
        $pedido['detalles'] = $stmtD->fetchAll();
        return $pedido;
    }
}

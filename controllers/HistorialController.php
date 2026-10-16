<?php
/**
 * Controlador de Historial de Ventas
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Producto.php';

class HistorialController {
    private Venta $modeloVenta;
    private Producto $modeloProducto;

    public function __construct() {
        require_admin();
        $this->modeloVenta = new Venta();
        $this->modeloProducto = new Producto();
    }

    /**
     * Lista el historial con filtros de fecha y producto
     */
    public function index(): void {
        $fechaInicio = sanitize($_GET['fecha_inicio'] ?? '');
        $fechaFin = sanitize($_GET['fecha_fin'] ?? '');
        $productoId = !empty($_GET['producto_id']) ? intval($_GET['producto_id']) : null;

        $ventas = $this->modeloVenta->listarHistorial($fechaInicio, $fechaFin, $productoId);
        $productos = $this->modeloProducto->listarTodos();

        // Calcular total acumulado de la búsqueda filtrada
        $totalFiltrado = array_sum(array_column($ventas, 'total'));

        $tituloPagina = 'Historial de Ventas - San Gabriel';
        $menuActivo = 'historial';

        require_once __DIR__ . '/../views/historial/index.php';
    }

    /**
     * Muestra el detalle completo de una venta específica (recibo/factura simple)
     */
    public function detalle(): void {
        $id = intval($_GET['id'] ?? 0);
        $venta = $this->modeloVenta->obtenerPorId($id);

        if (!$venta) {
            set_flash('danger', 'La venta solicitada no existe.');
            redirect('index.php?c=historial');
        }

        $tituloPagina = 'Detalle de Venta #' . htmlspecialchars($venta['numero_venta']);
        $menuActivo = 'historial';

        require_once __DIR__ . '/../views/historial/detalle.php';
    }

    /**
     * Tiquete térmico con QR de una venta (admin)
     */
    public function factura(): void {
        $id    = (int)($_GET['id'] ?? 0);
        $venta = $this->modeloVenta->obtenerPorId($id);

        if (!$venta) {
            set_flash('danger', 'La venta no existe.');
            redirect('index.php?c=historial');
        }

        // Variables que espera views/factura/tiquete.php
        $esPedido  = false;
        $numero    = $venta['numero_venta'];
        $fecha     = $venta['fecha'];
        $cliente   = $venta['cliente'];
        $vendedor  = $venta['usuario_nombre'] ?? 'Administrador';
        $notas     = $venta['notas'] ?? '';
        $estado    = '';
        $total     = (float)$venta['total'];
        $subtotal  = $total;
        $urlVolver = 'index.php?c=historial&a=detalle&id=' . $id;

        $items = array_map(fn($d) => [
            'nombre'          => $d['producto_nombre'],
            'cantidad'        => $d['cantidad'],
            'precio_unitario' => (float)$d['precio_unitario'],
            'subtotal'        => (float)$d['subtotal'],
        ], $venta['detalles']);

        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $qrData = "https://{$host}/index.php?c=historial&a=factura&id={$id}\n"
                . "Venta: {$numero}\n"
                . "Cliente: {$cliente}\n"
                . "Total: " . number_format($total, 2, ',', '.') . "\n"
                . "Quesos San Gabriel";

        require_once __DIR__ . '/../views/factura/tiquete.php';
    }
}

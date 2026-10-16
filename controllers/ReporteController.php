<?php
/**
 * Controlador de Reportes
 * Genera reportes ejecutivos de inventario y ventas con vista lista para imprimir.
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Venta.php';

class ReporteController {
    private Producto $modeloProducto;
    private Venta $modeloVenta;

    public function __construct() {
        require_admin();
        $this->modeloProducto = new Producto();
        $this->modeloVenta = new Venta();
    }

    public function index(): void {
        $tipoReporte = sanitize($_GET['tipo'] ?? 'general'); // general, stock_bajo, agotados, ventas
        $fechaInicio = sanitize($_GET['fecha_inicio'] ?? date('Y-m-01')); // Primer día del mes
        $fechaFin = sanitize($_GET['fecha_fin'] ?? date('Y-m-d'));       // Hoy

        $statsProductos = $this->modeloProducto->obtenerEstadisticas();
        $statsVentas = $this->modeloVenta->obtenerEstadisticas();

        // Datos según el reporte seleccionado
        $todosProductos = $this->modeloProducto->listarTodos('', '', 'activo');

        $productosDisponibles = array_filter($todosProductos, fn($p) => (int)$p['cantidad'] > (int)$p['stock_minimo']);
        $productosStockBajo = array_filter($todosProductos, fn($p) => (int)$p['cantidad'] <= (int)$p['stock_minimo'] && (int)$p['cantidad'] > 0);
        $productosAgotados = array_filter($todosProductos, fn($p) => (int)$p['cantidad'] <= 0);

        // Ventas del período seleccionado
        $ventasPeriodo = $this->modeloVenta->listarHistorial($fechaInicio, $fechaFin);
        $totalVentasPeriodo = array_sum(array_column($ventasPeriodo, 'total'));

        $tituloPagina = 'Reportes y Estadísticas - San Gabriel';
        $menuActivo = 'reportes';

        require_once __DIR__ . '/../views/reportes/index.php';
    }
}

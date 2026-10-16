<?php
/**
 * Controlador de Panel Principal (Dashboard)
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Venta.php';

class DashboardController {
    private Producto $modeloProducto;
    private Venta $modeloVenta;

    public function __construct() {
        require_admin();
        $this->modeloProducto = new Producto();
        $this->modeloVenta = new Venta();
    }

    public function index(): void {
        // Métricas de inventario
        $statsProductos = $this->modeloProducto->obtenerEstadisticas();
        
        // Métricas de ventas
        $statsVentas = $this->modeloVenta->obtenerEstadisticas();

        // Alertas de stock crítico
        $productosCriticos = $this->modeloProducto->obtenerProductosCriticos();

        // Últimas ventas registradas
        $ultimasVentas = $this->modeloVenta->obtenerUltimasVentas(6);

        // Título de la página
        $tituloPagina = 'Panel Principal - San Gabriel';
        $menuActivo = 'dashboard';

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}

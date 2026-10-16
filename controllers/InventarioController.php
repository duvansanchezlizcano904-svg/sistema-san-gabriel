<?php
/**
 * Controlador de Inventario
 * Sección especializada en el monitoreo y control de existencias de lácteos.
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';

class InventarioController {
    private Producto $modeloProducto;

    public function __construct() {
        require_admin();
        $this->modeloProducto = new Producto();
    }

    public function index(): void {
        $filtroEstado = sanitize($_GET['estado_stock'] ?? '');
        $busqueda = sanitize($_GET['q'] ?? '');

        // Obtener todos los productos activos
        $productos = $this->modeloProducto->listarTodos($busqueda, '', 'activo');

        // Filtrar en memoria por el estado calculado si se solicita
        if (!empty($filtroEstado)) {
            $productos = array_filter($productos, function($p) use ($filtroEstado) {
                $estado = calcularEstadoStock((int)$p['cantidad'], (int)$p['stock_minimo']);
                return $estado['codigo'] === $filtroEstado;
            });
        }

        // Estadísticas de inventario
        $stats = $this->modeloProducto->obtenerEstadisticas();

        $tituloPagina = 'Control de Inventario - San Gabriel';
        $menuActivo = 'inventario';

        require_once __DIR__ . '/../views/inventario/index.php';
    }
}

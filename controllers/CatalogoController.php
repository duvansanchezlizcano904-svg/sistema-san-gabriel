<?php
/**
 * Controlador de Catálogo para Clientes
 * Permite a los clientes explorar las variedades de quesos disponibles y sus precios de venta.
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';

class CatalogoController {
    private Producto $modeloProducto;

    public function __construct() {
        require_auth();
        $this->modeloProducto = new Producto();
    }

    /**
     * Muestra la vitrina / catálogo de quesos
     */
    public function index(): void {
        $busqueda   = sanitize($_GET['q']          ?? '');
        $tipo       = sanitize($_GET['tipo']       ?? '');
        $categoria  = sanitize($_GET['categoria']  ?? '');
        $tipoLeche  = sanitize($_GET['tipo_leche'] ?? '');
        $intensidad = sanitize($_GET['intensidad'] ?? '');

        // El cliente solo puede ver productos activos, con filtros avanzados
        $quesos = $this->modeloProducto->listarTodos(
            $busqueda, 
            $tipo, 
            'activo',
            $categoria,
            $tipoLeche,
            $intensidad
        );
        
        // Obtener opciones para los filtros
        $tiposDisponibles = $this->modeloProducto->obtenerTipos();
        $categorias       = $this->modeloProducto->obtenerCategorias();
        $tiposLeche       = $this->modeloProducto->obtenerTiposLeche();
        $intensidades     = $this->modeloProducto->obtenerIntensidades();

        $tituloPagina = 'Catálogo de Quesos - San Gabriel';
        $menuActivo = 'catalogo';

        require_once __DIR__ . '/../views/catalogo/index.php';
    }
}

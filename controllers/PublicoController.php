<?php
/**
 * Controlador de Página Pública
 * Accesible sin autenticación. Muestra el catálogo de quesos.
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';

class PublicoController {
    private Producto $modeloProducto;

    public function __construct() {
        // NO requiere autenticación
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->modeloProducto = new Producto();
    }

    /**
     * Página de inicio pública — muestra productos en index.php sin requerir sesión
     */
    public function inicio(): void {
        // Intentar cargar productos desde la BD; si falla, usar datos de ejemplo
        try {
            $quesos = $this->modeloProducto->listarTodos('', '', 'activo');
        } catch (Throwable $e) {
            $quesos = [];
        }

        // ── Productos de ejemplo (fallback o complemento) ──
        $productosFallback = [
            [
                'id'           => 0,
                'codigo'       => 'QC-001',
                'nombre'       => 'Queso Campesino',
                'tipo'         => 'Queso Fresco',
                'descripcion'  => 'Queso fresco de textura firme elaborado con leche entera de vaca. Ideal para acompañar arepas, sopas y platos típicos colombianos.',
                'precio_venta' => 12000,
                'unidad_medida'=> '500 g',
                'cantidad'     => 50,
            ],
            [
                'id'           => 0,
                'codigo'       => 'QDC-002',
                'nombre'       => 'Queso Doble Crema',
                'tipo'         => 'Queso Hilado',
                'descripcion'  => 'Queso suave y cremoso de pasta hilada, perfecto para derretir. Acompaña deliciosamente empanadas, pizza y buñuelos.',
                'precio_venta' => 15000,
                'unidad_medida'=> '500 g',
                'cantidad'     => 40,
            ],
            [
                'id'           => 0,
                'codigo'       => 'QCO-003',
                'nombre'       => 'Queso Costeño',
                'tipo'         => 'Queso Duro / Salado',
                'descripcion'  => 'Queso duro y salado típico de la Costa Atlántica colombiana. Rallado o en trozos, aporta un sabor intenso y auténtico.',
                'precio_venta' => 13500,
                'unidad_medida'=> '500 g',
                'cantidad'     => 35,
            ],
            [
                'id'           => 0,
                'codigo'       => 'QLL-004',
                'nombre'       => 'Quesillo',
                'tipo'         => 'Queso Hilado',
                'descripcion'  => 'Queso hilado en forma de espiral o rollo, suave y elástico. Especialidad del Valle del Cauca y el Huila, imprescindible con natilla.',
                'precio_venta' => 11000,
                'unidad_medida'=> '250 g',
                'cantidad'     => 60,
            ],
            [
                'id'           => 0,
                'codigo'       => 'CUJ-005',
                'nombre'       => 'Cuajada',
                'tipo'         => 'Cuajada',
                'descripcion'  => 'Cuajada fresca de leche recién ordeñada. Suave, húmeda y de sabor lácteo natural. Perfecta con melao de panela o sola.',
                'precio_venta' => 9000,
                'unidad_medida'=> '300 g',
                'cantidad'     => 45,
            ],
            [
                'id'           => 0,
                'codigo'       => 'QMZ-006',
                'nombre'       => 'Queso Mozzarella',
                'tipo'         => 'Queso Semigraso',
                'descripcion'  => 'Mozzarella artesanal de alto estiramiento, elaborada con leche fresca de la región. Ideal para pizzas, lasañas y ensaladas caprese.',
                'precio_venta' => 18000,
                'unidad_medida'=> '500 g',
                'cantidad'     => 30,
            ],
        ];

        // Si no hay productos en BD, usar el fallback
        if (empty($quesos)) {
            $quesos = $productosFallback;
        }

        $estaAutenticado = is_authenticated();
        $esAdmin         = is_admin();
        $carrito         = $_SESSION['carrito'] ?? [];
        $carritoTotal    = array_sum(array_column($carrito, 'cantidad'));
        $carritoSubtotal = array_sum(array_column($carrito, 'subtotal'));
        $usuarioNombre   = $_SESSION['usuario_nombre'] ?? '';
        $usuarioEmail    = $_SESSION['usuario_email'] ?? '';
        $flash           = get_flash();

        require_once __DIR__ . '/../views/inicio/home.php';
    }

    public function index(): void {
        $busqueda    = trim($_GET['q']          ?? '');
        $tipoFiltro  = trim($_GET['tipo']       ?? '');
        $categoria   = trim($_GET['categoria']  ?? '');
        $tipoLeche   = trim($_GET['tipo_leche'] ?? '');
        $intensidad  = trim($_GET['intensidad'] ?? '');

        // Obtener productos con filtros avanzados
        $quesos = $this->modeloProducto->listarTodos(
            $busqueda, 
            $tipoFiltro, 
            'activo',
            $categoria,
            $tipoLeche,
            $intensidad
        );
        
        // Obtener opciones para los filtros
        $tipos        = $this->modeloProducto->obtenerTipos();
        $categorias   = $this->modeloProducto->obtenerCategorias();
        $tiposLeche   = $this->modeloProducto->obtenerTiposLeche();
        $intensidades = $this->modeloProducto->obtenerIntensidades();
        
        $totalProductos = count($this->modeloProducto->listarTodos('', '', 'activo'));

        // Estado sesión para la vista
        $estaAutenticado = is_authenticated();
        $esAdmin         = is_admin();

        // Total ítems en carrito (sesión)
        $carrito      = $_SESSION['carrito'] ?? [];
        $carritoTotal = array_sum(array_column($carrito, 'cantidad'));

        require_once __DIR__ . '/../views/publico/catalogo.php';
    }
}

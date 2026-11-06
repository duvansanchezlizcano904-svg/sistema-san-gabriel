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
     * Usa "Cargar más" para mostrar muchos productos de forma fácil
     */
    public function inicio(): void {
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $porPagina = 20; // Muchos productos por página inicial
        
        // Cargar catálogo completo de productos activos
        try {
            $quesos = $this->modeloProducto->listarTodos('', '', 'activo');
            $totalProductos = count($quesos);
            $totalPaginas = 1;
            
            // Si no hay productos en BD, registrar advertencia
            if (empty($quesos)) {
                error_log("PublicoController::inicio() - ADVERTENCIA: No hay productos activos en la base de datos");
            }
        } catch (Throwable $e) {
            // Registrar error real para diagnóstico
            error_log("PublicoController::inicio() - Error al cargar productos: " . $e->getMessage());
            error_log("Traza: " . $e->getTraceAsString());
            $quesos = [];
            $totalProductos = 0;
            $totalPaginas = 0;
        }

        // ── Productos de ejemplo (SOLO si BD falla o está vacía) ────
        // NUNCA se mezclan con productos reales de la BD.
        if (empty($quesos)) {
            $todosQuesos = [
                // Quesos Frescos
                ['id' => 1, 'codigo' => 'LAC-001', 'nombre' => 'Queso Campesino', 'tipo' => 'Queso Fresco', 'categoria' => 'Quesos Frescos', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'suave', 'origen' => 'Cundinamarca', 'peso_presentacion' => '1 kg', 'tiempo_maduracion' => 'Sin maduración', 'descripcion' => 'Queso campesino fresco y bajo en sal.', 'precio_venta' => 15000, 'cantidad' => 24],
                ['id' => 2, 'codigo' => 'QF-001', 'nombre' => 'Queso Fresco Paipa', 'tipo' => 'Queso Fresco', 'categoria' => 'Quesos Frescos', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'suave', 'origen' => 'Boyacá', 'peso_presentacion' => '1 kg', 'tiempo_maduracion' => 'Sin maduración', 'descripcion' => 'Queso blanco tradicional de Paipa.', 'precio_venta' => 14500, 'cantidad' => 30],
                ['id' => 3, 'codigo' => 'QF-002', 'nombre' => 'Queso Costeño', 'tipo' => 'Queso Fresco', 'categoria' => 'Quesos Frescos', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'suave', 'origen' => 'Costa', 'peso_presentacion' => '500 g', 'tiempo_maduracion' => 'Sin maduración', 'descripcion' => 'Queso fresco de la costa colombiana.', 'precio_venta' => 12000, 'cantidad' => 25],
                ['id' => 4, 'codigo' => 'QF-003', 'nombre' => 'Quesillo de Hoja', 'tipo' => 'Queso Fresco', 'categoria' => 'Quesos Frescos', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'suave', 'origen' => 'Antioquia', 'peso_presentacion' => '400 g', 'tiempo_maduracion' => 'Sin maduración', 'descripcion' => 'Quesillo envuelto en hoja de plátano.', 'precio_venta' => 11000, 'cantidad' => 20],
                ['id' => 5, 'codigo' => 'QF-004', 'nombre' => 'Cuajada Fresca', 'tipo' => 'Queso Fresco', 'categoria' => 'Quesos Frescos', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'suave', 'origen' => 'Boyacá', 'peso_presentacion' => '500 g', 'tiempo_maduracion' => 'Sin maduración', 'descripcion' => 'Cuajada fresca artesanal.', 'precio_venta' => 13000, 'cantidad' => 18],
                ['id' => 6, 'codigo' => 'QF-005', 'nombre' => 'Mozzarella Fresca', 'tipo' => 'Queso Fresco', 'categoria' => 'Quesos Frescos', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'suave', 'origen' => 'Cundinamarca', 'peso_presentacion' => '400 g', 'tiempo_maduracion' => 'Sin maduración', 'descripcion' => 'Mozzarella fresca para pizza.', 'precio_venta' => 16000, 'cantidad' => 22],
                
                // Quesos Madurados
                ['id' => 7, 'codigo' => 'QM-001', 'nombre' => 'Queso Parmesano', 'tipo' => 'Queso Maduro', 'categoria' => 'Quesos Madurados', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'muy fuerte', 'origen' => 'Región Andina', 'peso_presentacion' => '500 g', 'tiempo_maduracion' => '12-24 meses', 'descripcion' => 'Queso duro madurado perfecto para rallar.', 'precio_venta' => 26000, 'cantidad' => 12],
                ['id' => 8, 'codigo' => 'QM-002', 'nombre' => 'Queso Sabanero', 'tipo' => 'Queso Maduro', 'categoria' => 'Quesos Madurados', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'fuerte', 'origen' => 'Casanare', 'peso_presentacion' => '1 kg', 'tiempo_maduracion' => '6 meses', 'descripcion' => 'Queso maduro llanero.', 'precio_venta' => 22000, 'cantidad' => 15],
                ['id' => 9, 'codigo' => 'QM-003', 'nombre' => 'Queso Semicurado', 'tipo' => 'Queso Maduro', 'categoria' => 'Quesos Madurados', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'medio', 'origen' => 'Boyacá', 'peso_presentacion' => '500 g', 'tiempo_maduracion' => '3 meses', 'descripcion' => 'Queso semicurado de textura firme.', 'precio_venta' => 18000, 'cantidad' => 16],
                ['id' => 10, 'codigo' => 'QM-004', 'nombre' => 'Queso Andino', 'tipo' => 'Queso Maduro', 'categoria' => 'Quesos Madurados', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'fuerte', 'origen' => 'Cundinamarca', 'peso_presentacion' => '600 g', 'tiempo_maduracion' => '4 meses', 'descripcion' => 'Queso madurado de altura.', 'precio_venta' => 21000, 'cantidad' => 14],
                
                // Quesos Artesanales
                ['id' => 11, 'codigo' => 'QAR-001', 'nombre' => 'Queso Doble Crema', 'tipo' => 'Queso Artesanal', 'categoria' => 'Quesos Artesanales', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'medio', 'origen' => 'Boyacá', 'peso_presentacion' => '400 g', 'tiempo_maduracion' => '2 meses', 'descripcion' => 'Queso cremoso de doble crema.', 'precio_venta' => 24000, 'cantidad' => 10],
                ['id' => 12, 'codigo' => 'QAR-002', 'nombre' => 'Queso Ahumado', 'tipo' => 'Queso Artesanal', 'categoria' => 'Quesos Artesanales', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'medio', 'origen' => 'Región Andina', 'peso_presentacion' => '1 kg', 'tiempo_maduracion' => '2-3 meses', 'descripcion' => 'Queso ahumado con leña de roble.', 'precio_venta' => 20500, 'cantidad' => 11],
                ['id' => 13, 'codigo' => 'QAR-003', 'nombre' => 'Queso con Hierbas', 'tipo' => 'Queso Artesanal', 'categoria' => 'Quesos Artesanales', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'medio', 'origen' => 'Cundinamarca', 'peso_presentacion' => '350 g', 'tiempo_maduracion' => '1 mes', 'descripcion' => 'Queso con mezcla de hierbas aromáticas.', 'precio_venta' => 19500, 'cantidad' => 8],
                ['id' => 14, 'codigo' => 'QAR-004', 'nombre' => 'Queso con Trufa', 'tipo' => 'Queso Premium', 'categoria' => 'Quesos Artesanales', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'muy fuerte', 'origen' => 'Nacional Premium', 'peso_presentacion' => '300 g', 'tiempo_maduracion' => '3-6 meses', 'descripcion' => 'Queso con láminas de trufa negra.', 'precio_venta' => 42000, 'cantidad' => 4],
                
                // Quesos Cremosos
                ['id' => 15, 'codigo' => 'QC-001', 'nombre' => 'Queso Brie', 'tipo' => 'Queso Cremoso', 'categoria' => 'Quesos Cremosos', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'medio', 'origen' => 'Importado', 'peso_presentacion' => '250 g', 'tiempo_maduracion' => '4-6 semanas', 'descripcion' => 'Queso francese de corteza enmohecida.', 'precio_venta' => 28000, 'cantidad' => 8],
                ['id' => 16, 'codigo' => 'QC-002', 'nombre' => 'Queso Camembert', 'tipo' => 'Queso Cremoso', 'categoria' => 'Quesos Cremosos', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'medio', 'origen' => 'Importado', 'peso_presentacion' => '250 g', 'tiempo_maduracion' => '3-4 semanas', 'descripcion' => 'Queso francés suave y cremoso.', 'precio_venta' => 26000, 'cantidad' => 7],
                ['id' => 17, 'codigo' => 'QC-003', 'nombre' => 'Queso Burrata', 'tipo' => 'Queso Cremoso', 'categoria' => 'Quesos Cremosos', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'suave', 'origen' => 'Magdalena Medio', 'peso_presentacion' => '300 g', 'tiempo_maduracion' => 'Sin maduración', 'descripcion' => 'Queso italiano fresco y cremoso.', 'precio_venta' => 25000, 'cantidad' => 12],
                
                // Quesos de Cabra
                ['id' => 18, 'codigo' => 'QG-001', 'nombre' => 'Queso de Cabra Fresco', 'tipo' => 'Queso de Cabra', 'categoria' => 'Quesos de Cabra', 'tipo_leche' => 'Cabra', 'intensidad_sabor' => 'medio', 'origen' => 'Santander', 'peso_presentacion' => '500 g', 'tiempo_maduracion' => 'Sin maduración', 'descripcion' => 'Queso fresco de leche de cabra.', 'precio_venta' => 21000, 'cantidad' => 12],
                ['id' => 19, 'codigo' => 'QG-002', 'nombre' => 'Queso de Cabra Madurado', 'tipo' => 'Queso de Cabra', 'categoria' => 'Quesos de Cabra', 'tipo_leche' => 'Cabra', 'intensidad_sabor' => 'fuerte', 'origen' => 'Boyacá', 'peso_presentacion' => '350 g', 'tiempo_maduracion' => '2 meses', 'descripcion' => 'Queso de cabra madurado.', 'precio_venta' => 23000, 'cantidad' => 9],
                
                // Quesos Azules
                ['id' => 20, 'codigo' => 'QA-001', 'nombre' => 'Queso Azul', 'tipo' => 'Queso Azul', 'categoria' => 'Quesos Azules', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'muy fuerte', 'origen' => 'Nacional', 'peso_presentacion' => '300 g', 'tiempo_maduracion' => '3-5 meses', 'descripcion' => 'Queso azul con vetas de penicilium.', 'precio_venta' => 32000, 'cantidad' => 5],
                ['id' => 21, 'codigo' => 'QA-002', 'nombre' => 'Queso Gorgonzola', 'tipo' => 'Queso Azul', 'categoria' => 'Quesos Azules', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'muy fuerte', 'origen' => 'Nacional', 'peso_presentacion' => '350 g', 'tiempo_maduracion' => '4 meses', 'descripcion' => 'Queso azul italiano estilo.', 'precio_venta' => 32500, 'cantidad' => 8],
                
                // Más variedades
                ['id' => 22, 'codigo' => 'QM-007', 'nombre' => 'Queso Pecorino', 'tipo' => 'Queso Maduro', 'categoria' => 'Quesos Madurados', 'tipo_leche' => 'Oveja', 'intensidad_sabor' => 'muy fuerte', 'origen' => 'Nariño', 'peso_presentacion' => '400 g', 'tiempo_maduracion' => '8 meses', 'descripcion' => 'Queso de leche de oveja.', 'precio_venta' => 31000, 'cantidad' => 10],
                ['id' => 23, 'codigo' => 'QM-008', 'nombre' => 'Queso Gruyère', 'tipo' => 'Queso Maduro', 'categoria' => 'Quesos Madurados', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'fuerte', 'origen' => 'Boyacá', 'peso_presentacion' => '500 g', 'tiempo_maduracion' => '6 meses', 'descripcion' => 'Queso suizo estilo montaña.', 'precio_venta' => 29000, 'cantidad' => 14],
                ['id' => 24, 'codigo' => 'QAR-005', 'nombre' => 'Queso Provolone', 'tipo' => 'Queso Artesanal', 'categoria' => 'Quesos Artesanales', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'medio', 'origen' => 'Cundinamarca', 'peso_presentacion' => '400 g', 'tiempo_maduracion' => '2 meses', 'descripcion' => 'Queso italiano para fundir.', 'precio_venta' => 20000, 'cantidad' => 13],
                ['id' => 25, 'codigo' => 'QF-007', 'nombre' => 'Requesón', 'tipo' => 'Queso Fresco', 'categoria' => 'Quesos Frescos', 'tipo_leche' => 'Vaca', 'intensidad_sabor' => 'suave', 'origen' => 'Cundinamarca', 'peso_presentacion' => '250 g', 'tiempo_maduracion' => 'Sin maduración', 'descripcion' => 'Queso ricotta colombiano.', 'precio_venta' => 8000, 'cantidad' => 30],
            ];
            
            $totalProductos = count($todosQuesos);
            $totalPaginas = ceil($totalProductos / $porPagina);
            $inicio = ($pagina - 1) * $porPagina;
            $quesos = array_slice($todosQuesos, $inicio, $porPagina);
        }

        $estaAutenticado = is_authenticated();
        $esAdmin         = is_admin();
        $carrito         = $_SESSION['carrito'] ?? [];
        $carritoTotal    = array_sum(array_column($carrito, 'cantidad'));
        $carritoSubtotal = array_sum(array_column($carrito, 'subtotal'));
        $usuarioNombre   = $_SESSION['usuario_nombre'] ?? '';
        $usuarioEmail    = $_SESSION['usuario_email'] ?? '';
        $flash           = get_flash();
        $paginaActual    = $pagina; // Para la vista
        
        // Calcular si hay más productos por cargar
        $productosCargados = is_array($quesos) ? count($quesos) : 0;
        $hayMas = $totalProductos > $productosCargados;

        require_once __DIR__ . '/../views/inicio/home.php';
    }

    /**
     * Carga más productos mediante AJAX
     */
    public function cargarMasProductos(): void {
        header('Content-Type: application/json');
        
        try {
            $pagina    = max(1, (int)($_GET['pagina'] ?? 1));
            $porPagina = max(1, (int)($_GET['por_pagina'] ?? 20)); // Cargar 20 productos por vez
            $categoria = trim($_GET['categoria'] ?? '');
            $tipo      = trim($_GET['tipo'] ?? '');
            $busqueda  = trim($_GET['busqueda'] ?? '');

            $resultado = $this->modeloProducto->listarConPaginacion(
                $pagina,
                $porPagina,
                $busqueda,
                $tipo,
                'activo',
                $categoria
            );

            // Generar HTML para los productos
            $htmlProductos = '';
            $modo = 'home';
            foreach ($resultado['productos'] as $q) {
                ob_start();
                require __DIR__ . '/../views/partials/tarjeta_producto.php';
                $htmlProductos .= ob_get_clean();
            }

            echo json_encode([
                'success' => true,
                'html' => $htmlProductos,
                'total' => $resultado['total'],
                'pagina' => $resultado['pagina'],
                'hayMas' => $resultado['hayMas'],
                'mostrados' => count($resultado['productos'])
            ]);
            
        } catch (Throwable $e) {
            error_log("PublicoController::cargarMasProductos() - Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al cargar más productos'
            ]);
        }
        exit;
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

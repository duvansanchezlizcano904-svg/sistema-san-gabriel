<?php
/**
 * Quesos San Gabriel — Enrutador Principal
 * ?c=controlador&a=accion
 * Si no hay sesión, muestra la página de inicio con productos.
 */
require_once __DIR__ . '/config/helpers.php';

// ── Permitir acciones del carrito sin autenticación (lista blanca) ──
if (!is_authenticated()) {
    $controlador = sanitize($_GET['c'] ?? '');
    $accion = sanitize($_GET['a'] ?? '');
    
    // Lista blanca de acciones permitidas sin sesión
    $accionesCarritoPermitidas = ['agregar', 'actualizar', 'quitar', 'vaciar', 'index'];
    
    if ($controlador === 'carrito' && in_array($accion, $accionesCarritoPermitidas, true)) {
        require_once __DIR__ . '/controllers/CarritoController.php';
        $instancia = new CarritoController();
        
        if (method_exists($instancia, $accion)) {
            $instancia->$accion();
            exit;
        }
    }
    
    // Si no es una acción de carrito permitida, mostrar página de inicio
    require_once __DIR__ . '/controllers/PublicoController.php';
    $ctrl = new PublicoController();
    $ctrl->inicio();
    exit;
}

// ── Cliente autenticado sin parámetros: mostrar página de inicio (home) ──
if (!is_admin() && !isset($_GET['c'])) {
    require_once __DIR__ . '/controllers/PublicoController.php';
    $ctrl = new PublicoController();
    $ctrl->inicio();
    exit;
}

// ── Con sesión: enrutador normal ──
$controladorPorDefecto = is_admin() ? 'dashboard' : 'catalogo';
$controlador = sanitize($_GET['c'] ?? $controladorPorDefecto);
$accion      = sanitize($_GET['a'] ?? 'index');

$controladoresValidos = [
    'dashboard'   => 'DashboardController',
    'productos'   => 'ProductoController',
    'inventario'  => 'InventarioController',
    'ventas'      => 'VentaController',
    'historial'   => 'HistorialController',
    'reportes'    => 'ReporteController',
    'catalogo'    => 'CatalogoController',
    'proveedores' => 'ProveedorController',
    'carrito'     => 'CarritoController',
];

if (!array_key_exists($controlador, $controladoresValidos)) {
    set_flash('danger', 'El módulo solicitado no existe.');
    redirect('index.php?c=' . $controladorPorDefecto);
}

$nombreClase        = $controladoresValidos[$controlador];
$archivoControlador = __DIR__ . '/controllers/' . $nombreClase . '.php';

if (!file_exists($archivoControlador)) {
    die("Error interno: No se encontró el archivo del controlador {$nombreClase}.");
}

require_once $archivoControlador;
$instancia = new $nombreClase();

if (!method_exists($instancia, $accion)) {
    die("Error interno: La acción '{$accion}' no existe en {$nombreClase}.");
}

$instancia->$accion();

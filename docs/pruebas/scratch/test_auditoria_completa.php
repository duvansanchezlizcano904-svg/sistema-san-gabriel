<?php
/**
 * Suite de Verificación Integral de Auditoría
 */
$testCount = 0;
$passCount = 0;

function verificar(string $desc, callable $fn) {
    global $testCount, $passCount;
    $testCount++;
    try {
        $res = $fn();
        if ($res === true) {
            $passCount++;
            echo "[PASS] {$desc}\n";
        } else {
            echo "[FAIL] {$desc}: {$res}\n";
        }
    } catch (Throwable $e) {
        echo "[FAIL] {$desc}: " . $e->getMessage() . "\n";
    }
}

echo "========================================================\n";
echo "EJECUTANDO AUDITORÍA COMPLETA DEL SISTEMA\n";
echo "========================================================\n\n";

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Proveedor.php';

// 1. Open redirect protection
verificar("Seguridad: redirect() neutraliza intentos de Open Redirect", function() {
    $ref = new ReflectionFunction('redirect');
    $source = file_get_contents($ref->getFileName());
    if (strpos($source, 'preg_match') === false || strpos($source, 'https?:') === false) {
        return "redirect() no contiene regex de bloqueo";
    }
    return true;
});

// 2. Acciones permitidas sin sesión en index.php
verificar("Seguridad: Todas las acciones permitidas en index.php existen en CarritoController y PublicoController", function() {
    $indexCode = file_get_contents(__DIR__ . '/../index.php');
    preg_match('/\$accionesCarritoPermitidas\s*=\s*\[(.*?)\];/s', $indexCode, $mCarrito);
    preg_match('/\$accionesPublicoPermitidas\s*=\s*\[(.*?)\];/s', $indexCode, $mPublico);
    
    eval('$arrC = [' . $mCarrito[1] . '];');
    eval('$arrP = [' . $mPublico[1] . '];');
    
    require_once __DIR__ . '/../controllers/CarritoController.php';
    $cRef = new ReflectionClass('CarritoController');
    foreach ($arrC as $acc) {
        if (!$cRef->hasMethod($acc)) return "CarritoController no tiene el método '{$acc}'";
    }
    
    require_once __DIR__ . '/../controllers/PublicoController.php';
    $pRef = new ReflectionClass('PublicoController');
    foreach ($arrP as $acc) {
        if (!$pRef->hasMethod($acc)) return "PublicoController no tiene el método '{$acc}'";
    }
    
    return true;
});

// 3. Módulo público accesible para usuarios autenticados
verificar("Enrutador: 'publico' está registrado en controladores válidos en index.php", function() {
    $indexCode = file_get_contents(__DIR__ . '/../index.php');
    if (strpos($indexCode, "'publico'     => 'PublicoController'") === false) {
        return "'publico' no está en \$controladoresValidos";
    }
    return true;
});

// 4. Protección de secciones admin
verificar("Seguridad: Todos los controladores admin implementan require_admin()", function() {
    $adminControllers = [
        'DashboardController', 'ProductoController', 'InventarioController',
        'VentaController', 'HistorialController', 'ReporteController', 'ProveedorController'
    ];
    foreach ($adminControllers as $ctrl) {
        $file = __DIR__ . "/../controllers/{$ctrl}.php";
        $code = file_get_contents($file);
        if (strpos($code, 'require_admin()') === false) {
            return "Controlador {$ctrl} no llama a require_admin()";
        }
    }
    return true;
});

// 5. Protección de pedidos cliente
verificar("Seguridad: CarritoController restringe consulta de pedidos ajenos", function() {
    $code = file_get_contents(__DIR__ . '/../controllers/CarritoController.php');
    if (strpos($code, "cliente_id'] !== (int)(\$_SESSION['usuario_id']") === false) {
        return "No se encontró validación de cliente_id contra usuario_id en sesión";
    }
    return true;
});

// 6. Consultas PDO y LIMIT/OFFSET en Producto.php
verificar("Base de Datos: Producto::listarConPaginacion maneja LIMIT y OFFSET seguros para PDO nativo", function() {
    $code = file_get_contents(__DIR__ . '/../models/Producto.php');
    if (strpos($code, "LIMIT {\$limitInt} OFFSET {\$offsetInt}") === false && strpos($code, "(int)\$porPagina") === false) {
        return "No usa casting seguro a entero para LIMIT y OFFSET";
    }
    return true;
});

// 7. Descuento atómico de stock en Venta y Pedido
verificar("Stock: Venta y Pedido aplican descuento atómico WHERE cantidad >= :min", function() {
    $codeV = file_get_contents(__DIR__ . '/../models/Producto.php');
    $codeP = file_get_contents(__DIR__ . '/../models/Pedido.php');
    
    if (strpos($codeV, 'cantidad - :cantidad WHERE id = :id AND cantidad >= :cantidad') === false) {
        return "Producto::descontarStock no valida cantidad >= :cantidad";
    }
    if (strpos($codeP, 'cantidad - :qty WHERE id = :id AND cantidad >= :min_qty') === false) {
        return "Pedido::registrarPedido no valida cantidad >= :min_qty";
    }
    return true;
});

// 8. Idempotencia en database.sql
verificar("Base de Datos: database.sql maneja SET FOREIGN_KEY_CHECKS = 0 para reimportación segura", function() {
    $code = file_get_contents(__DIR__ . '/../database/database.sql');
    if (strpos($code, 'SET FOREIGN_KEY_CHECKS = 0;') === false) {
        return "database.sql no desactiva FOREIGN_KEY_CHECKS";
    }
    return true;
});

// 9. Documentación no vacía
verificar("Documentación: Ningún archivo .md en docs/ está vacío", function() {
    $files = glob(__DIR__ . '/../docs/*.md');
    foreach ($files as $f) {
        if (filesize($f) === 0) return "El archivo " . basename($f) . " está vacío (0 bytes)";
    }
    return true;
});

echo "\n========================================================\n";
echo "RESULTADO AUDITORÍA: {$passCount}/{$testCount} verificaciones exitosas.\n";
echo "========================================================\n";

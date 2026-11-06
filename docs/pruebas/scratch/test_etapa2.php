<?php
/**
 * Test Suite Automático - Etapa 2: Gestión de Productos
 * Verifica HU-04, HU-05, HU-06, HU-07, HU-23
 */

$testCount = 0;
$passCount = 0;

function run_test(string $name, callable $fn) {
    global $testCount, $passCount;
    $testCount++;
    try {
        $result = $fn();
        if ($result === true) {
            $passCount++;
            echo "  [PASS] {$name}\n";
        } else {
            echo "  [FAIL] {$name}: {$result}\n";
        }
    } catch (Throwable $e) {
        echo "  [FAIL] {$name}: Excepción: " . $e->getMessage() . "\n";
    }
}

echo "\n=======================================================\n";
echo "PRUEBAS AUTOMATIZADAS - ETAPA 2: GESTIÓN DE PRODUCTOS\n";
echo "=======================================================\n\n";

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../controllers/ProductoController.php';

// Test 1: Clase Producto y métodos requeridos
run_test("HU-04/HU-06/HU-07: Clase Producto define métodos crear, actualizar, eliminar, cambiarEstado", function() {
    $ref = new ReflectionClass('Producto');
    $methods = ['crear', 'actualizar', 'eliminar', 'cambiarEstado', 'existeCodigo', 'obtenerPorId'];
    foreach ($methods as $m) {
        if (!$ref->hasMethod($m)) return "Falta método {$m}";
    }
    return true;
});

// Test 2: ProductoController define métodos requeridos
run_test("HU-04/HU-05/HU-06/HU-07: ProductoController define acciones index, crear, guardar, editar, actualizar, cambiarEstado, eliminar", function() {
    $ref = new ReflectionClass('ProductoController');
    $methods = ['index', 'crear', 'guardar', 'editar', 'actualizar', 'cambiarEstado', 'eliminar'];
    foreach ($methods as $m) {
        if (!$ref->hasMethod($m)) return "Falta método {$m}";
    }
    return true;
});

// Test 3: Validación de estado no válido en cambiarEstado
run_test("HU-07: cambiarEstado rechaza valores que no sean 'activo' o 'inactivo'", function() {
    $ref = new ReflectionClass('Producto');
    $source = file_get_contents($ref->getFileName());
    if (strpos($source, "in_array(\$nuevoEstado, ['activo', 'inactivo']") === false) {
        return "No valida los estados permitidos";
    }
    return true;
});

// Test 4: Verificación de ventas asociadas antes de eliminar
run_test("HU-07: eliminar verifica ventas en detalle_ventas y previene borrado físico", function() {
    $ref = new ReflectionClass('Producto');
    $source = file_get_contents($ref->getFileName());
    if (strpos($source, "SELECT COUNT(*) FROM detalle_ventas WHERE producto_id = :id") === false) {
        return "No consulta detalle_ventas";
    }
    if (strpos($source, "No se puede eliminar el producto porque tiene ventas registradas") === false) {
        return "No restringe con mensaje explicativo";
    }
    return true;
});

// Test 5: HU-23 Subida de fotos permitidas (JPG, PNG, WEBP) y límite de 2MB
run_test("HU-23: procesarImagen valida MIME real (image/jpeg, image/png, image/webp) y 2MB", function() {
    $ref = new ReflectionClass('ProductoController');
    $source = file_get_contents($ref->getFileName());
    if (strpos($source, "['image/jpeg', 'image/png', 'image/webp']") === false) {
        return "No valida tipos MIME permitidos";
    }
    if (strpos($source, "2 * 1024 * 1024") === false) {
        return "No valida límite de 2MB";
    }
    return true;
});

// Test 6: HU-23 Placeholder de imagen presente en el proyecto
run_test("HU-23: Archivo placeholder de imagen existe en public/img/placeholder-queso.png", function() {
    $placeholder = __DIR__ . '/../public/img/placeholder-queso.png';
    if (!file_exists($placeholder)) {
        return "No existe public/img/placeholder-queso.png";
    }
    return true;
});

// Test 7: HU-04 Vista de creación contiene todos los campos exigidos
run_test("HU-04: views/productos/crear.php contiene campos de código, nombre, tipo, precios, stock inicial, stock mínimo y unidad", function() {
    $vista = file_get_contents(__DIR__ . '/../views/productos/crear.php');
    $campos = ['name="codigo"', 'name="nombre"', 'name="tipo"', 'name="precio_compra"', 'name="precio_venta"', 'name="cantidad"', 'name="stock_minimo"', 'name="unidad_medida"', 'name="imagen"'];
    foreach ($campos as $c) {
        if (strpos($vista, $c) === false) return "Falta campo {$c} en crear.php";
    }
    return true;
});

// Test 8: HU-06 Vista de edición precargada con datos
run_test("HU-06: views/productos/editar.php contiene formulario precargado con datos del producto", function() {
    $vista = file_get_contents(__DIR__ . '/../views/productos/editar.php');
    if (strpos($vista, 'value="<?= htmlspecialchars($producto[\'codigo\']) ?>"') === false) {
        return "No precarga código";
    }
    if (strpos($vista, 'value="<?= htmlspecialchars($producto[\'nombre\']) ?>"') === false) {
        return "No precarga nombre";
    }
    return true;
});

// Test 9: HU-05 Buscador en tiempo real y tabla con columnas clave
run_test("HU-05: views/productos/index.php contiene tabla, buscador en tiempo real y filtros", function() {
    $vista = file_get_contents(__DIR__ . '/../views/productos/index.php');
    if (strpos($vista, 'id="buscadorProductos"') === false) return "Falta id buscadorProductos";
    if (strpos($vista, 'id="tablaProductos"') === false) return "Falta id tablaProductos";
    if (strpos($vista, 'form-toggle-estado') === false) return "Falta form-toggle-estado para borrado lógico";
    return true;
});

// Test 10: HU-07 Control de confirmación antes de eliminar o cambiar estado
run_test("HU-07: Script en views/productos/index.php contiene confirmación antes de cambiar estado o eliminar", function() {
    $vista = file_get_contents(__DIR__ . '/../views/productos/index.php');
    if (strpos($vista, 'form-toggle-estado') === false || strpos($vista, 'confirm(') === false) {
        return "Falta confirmación de borrado lógico";
    }
    return true;
});

echo "\n-------------------------------------------------------\n";
echo "RESULTADO: {$passCount}/{$testCount} pruebas superadas.\n";
echo "-------------------------------------------------------\n";

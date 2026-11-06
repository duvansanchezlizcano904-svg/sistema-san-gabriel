<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Proveedor.php';

$p = new Producto();
try {
    echo "Probando Producto::listarTodos('queso')...\n";
    $res = $p->listarTodos('queso');
    echo "Resultado: " . count($res) . " productos\n";
} catch (Throwable $e) {
    echo "ERROR en Producto::listarTodos: " . $e->getMessage() . "\n";
}

try {
    echo "Probando Producto::listarConPaginacion(1, 10, 'queso')...\n";
    $res = $p->listarConPaginacion(1, 10, 'queso');
    echo "Resultado: " . count($res['productos']) . " productos paginados\n";
} catch (Throwable $e) {
    echo "ERROR en Producto::listarConPaginacion: " . $e->getMessage() . "\n";
}

$pr = new Proveedor();
try {
    echo "Probando Proveedor::listarTodos('lacteos')...\n";
    $res = $pr->listarTodos('lacteos');
    echo "Resultado: " . count($res) . " proveedores\n";
} catch (Throwable $e) {
    echo "ERROR en Proveedor::listarTodos: " . $e->getMessage() . "\n";
}


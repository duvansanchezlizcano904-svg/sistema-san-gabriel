<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Venta.php';

$p = new Producto();
try {
    echo "Probando descontarStock(1, 1)...\n";
    $res = $p->descontarStock(1, 1);
    echo "Resultado: " . ($res ? 'OK' : 'FAIL') . "\n";
} catch (Throwable $e) {
    echo "ERROR en descontarStock: " . $e->getMessage() . "\n";
}


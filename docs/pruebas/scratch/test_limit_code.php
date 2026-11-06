<?php
// Mocking PDO connection to test statement parameter binding
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';

echo "Testing Producto::listarConPaginacion implementation...\n";
$ref = new ReflectionMethod('Producto', 'listarConPaginacion');
$source = file($ref->getFileName());
$start = $ref->getStartLine() - 1;
$length = $ref->getEndLine() - $start;
$code = implode('', array_slice($source, $start, $length));

if (strpos($code, '$params[\':limit\']') !== false && strpos($code, '$stmt->execute($params)') !== false) {
    echo "[POTENTIAL BUG DETECTED] \$params array contains :limit and is passed to \$stmt->execute(\$params)!\n";
    echo "When ATTR_EMULATE_PREPARES is false, PDO sends :limit as a string in \$stmt->execute(\$array), which breaks MySQL LIMIT syntax!\n";
} else {
    echo "[OK] No LIMIT string-bind issue detected.\n";
}


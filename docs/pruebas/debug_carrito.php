<?php
/**
 * Debug del Carrito - Página de Prueba
 */
session_start();

echo "<h1>Debug del Sistema de Carrito</h1>";
echo "<hr>";

// 1. Estado de la sesión
echo "<h2>1. Estado de la Sesión PHP</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVA' : 'INACTIVA') . "\n";
echo "</pre>";

// 2. Usuario autenticado
echo "<h2>2. Usuario Autenticado</h2>";
echo "<pre>";
if (isset($_SESSION['usuario_id'])) {
    echo "✅ Usuario autenticado\n";
    echo "ID: " . $_SESSION['usuario_id'] . "\n";
    echo "Nombre: " . ($_SESSION['usuario_nombre'] ?? 'N/A') . "\n";
    echo "Rol: " . ($_SESSION['usuario_rol'] ?? 'N/A') . "\n";
} else {
    echo "❌ Usuario NO autenticado (modo anónimo)\n";
}
echo "</pre>";

// 3. Contenido del carrito
echo "<h2>3. Contenido del Carrito</h2>";
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>";
    foreach ($_SESSION['carrito'] as $id => $item) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($id) . "</td>";
        echo "<td>" . htmlspecialchars($item['nombre'] ?? 'Sin nombre') . "</td>";
        echo "<td>" . ($item['cantidad'] ?? 0) . "</td>";
        echo "<td>$" . number_format($item['precio_unitario'] ?? 0, 0) . "</td>";
        echo "<td>$" . number_format($item['subtotal'] ?? 0, 0) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $total = array_sum(array_column($_SESSION['carrito'], 'subtotal'));
    echo "<p><strong>TOTAL: $" . number_format($total, 0) . "</strong></p>";
} else {
    echo "<p>❌ El carrito está vacío</p>";
}

// 4. Verificar archivos necesarios
echo "<h2>4. Archivos del Sistema</h2>";
echo "<pre>";
$archivos = [
    'index.php' => file_exists(__DIR__ . '/index.php'),
    'controllers/CarritoController.php' => file_exists(__DIR__ . '/controllers/CarritoController.php'),
    'models/Producto.php' => file_exists(__DIR__ . '/models/Producto.php'),
    'config/helpers.php' => file_exists(__DIR__ . '/config/helpers.php'),
];
foreach ($archivos as $archivo => $existe) {
    echo ($existe ? '✅' : '❌') . " $archivo\n";
}
echo "</pre>";

// 5. Probar agregar un producto de prueba
echo "<h2>5. Prueba de Agregar Producto</h2>";
echo "<form method='POST' action='index.php?c=carrito&a=agregar'>";
echo "<input type='hidden' name='producto_id' value='1'>";
echo "<input type='hidden' name='cantidad' value='1'>";
echo "<input type='hidden' name='redirect' value='debug_carrito.php'>";
echo "<button type='submit' style='padding:10px 20px; font-size:16px;'>Agregar Producto #1 al Carrito</button>";
echo "</form>";

// 6. Botones de navegación
echo "<h2>6. Navegación</h2>";
echo "<a href='index.php' style='padding:10px; background:#007bff; color:white; text-decoration:none; margin:5px; display:inline-block;'>Ir a Index</a>";
echo "<a href='publico.php' style='padding:10px; background:#28a745; color:white; text-decoration:none; margin:5px; display:inline-block;'>Ir a Público</a>";
echo "<a href='index.php?c=carrito' style='padding:10px; background:#ffc107; color:black; text-decoration:none; margin:5px; display:inline-block;'>Ver Carrito</a>";

// 7. Vaciar carrito
if (!empty($_SESSION['carrito'])) {
    echo "<form method='POST' action='index.php?c=carrito&a=vaciar' style='display:inline;'>";
    echo "<input type='hidden' name='redirect' value='debug_carrito.php'>";
    echo "<button type='submit' style='padding:10px 20px; background:#dc3545; color:white; border:none; margin:5px;'>Vaciar Carrito</button>";
    echo "</form>";
}

// 8. Mensajes flash
require_once __DIR__ . '/config/helpers.php';
$flash = get_flash();
if ($flash) {
    echo "<h2>7. Mensaje del Sistema</h2>";
    $color = [
        'success' => 'green',
        'danger' => 'red',
        'warning' => 'orange',
        'info' => 'blue'
    ];
    $bg = $color[$flash['tipo']] ?? 'gray';
    echo "<div style='padding:15px; background:" . $bg . "; color:white; margin:10px 0;'>";
    echo "<strong>" . strtoupper($flash['tipo']) . ":</strong> " . htmlspecialchars($flash['mensaje']);
    echo "</div>";
}

// 9. Volcado completo de $_SESSION
echo "<h2>8. Volcado Completo de \$_SESSION</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

?>
<style>
body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h2 { color: #555; margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 5px; }
table { border-collapse: collapse; width: 100%; }
th { background: #007bff; color: white; }
pre { background: #f4f4f4; padding: 10px; border-left: 4px solid #007bff; }
</style>

<?php
/**
 * Script de Prueba del Carrito
 * Verifica que el carrito funciona correctamente sin autenticación
 */

session_start();

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Prueba de Carrito - Quesos San Gabriel</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>
<div class='container py-5'>
    <div class='row justify-content-center'>
        <div class='col-lg-10'>
            <div class='card shadow-sm'>
                <div class='card-header bg-primary text-white'>
                    <h3 class='mb-0'><i class='bi bi-cart-check me-2'></i>Prueba del Sistema de Carrito</h3>
                </div>
                <div class='card-body'>";

// ═══════════════════════════════════════════════════════════════════════
// 1. Verificar estado de la sesión
// ═══════════════════════════════════════════════════════════════════════
echo "<div class='alert alert-info'>
    <h5><i class='bi bi-1-circle me-2'></i>Estado de la Sesión</h5>
    <ul class='mb-0'>";

if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<li>✅ Sesión PHP activa</li>";
} else {
    echo "<li>❌ Sesión PHP NO activa</li>";
}

echo "<li>ID de Sesión: <code>" . session_id() . "</code></li>";

if (isset($_SESSION['usuario_id'])) {
    echo "<li>👤 Usuario autenticado: ID " . $_SESSION['usuario_id'] . " (" . ($_SESSION['usuario_rol'] ?? 'sin rol') . ")</li>";
} else {
    echo "<li>👤 Usuario NO autenticado (modo anónimo)</li>";
}

echo "</ul></div>";

// ═══════════════════════════════════════════════════════════════════════
// 2. Verificar contenido del carrito
// ═══════════════════════════════════════════════════════════════════════
echo "<div class='alert alert-success'>
    <h5><i class='bi bi-2-circle me-2'></i>Contenido del Carrito</h5>";

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    $totalItems = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
    $totalDinero = array_sum(array_column($_SESSION['carrito'], 'subtotal'));
    
    echo "<p>✅ <strong>Carrito con productos</strong></p>";
    echo "<ul>
        <li>Total de ítems: <strong>$totalItems</strong></li>
        <li>Total en dinero: <strong>$ " . number_format($totalDinero, 2, ',', '.') . "</strong></li>
    </ul>";
    
    echo "<table class='table table-sm table-striped'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>";
    
    foreach ($_SESSION['carrito'] as $id => $item) {
        echo "<tr>
            <td><code>$id</code></td>
            <td>" . htmlspecialchars($item['nombre'] ?? 'Sin nombre') . "</td>
            <td>" . ($item['cantidad'] ?? 0) . "</td>
            <td>$ " . number_format($item['precio_unitario'] ?? 0, 2, ',', '.') . "</td>
            <td><strong>$ " . number_format($item['subtotal'] ?? 0, 2, ',', '.') . "</strong></td>
        </tr>";
    }
    
    echo "</tbody></table>";
} else {
    echo "<p>🛒 <strong>Carrito vacío</strong></p>";
    echo "<p class='text-muted mb-0'>No hay productos en el carrito de sesión.</p>";
}

echo "</div>";

// ═══════════════════════════════════════════════════════════════════════
// 3. Verificar acceso a controladores
// ═══════════════════════════════════════════════════════════════════════
echo "<div class='alert alert-warning'>
    <h5><i class='bi bi-3-circle me-2'></i>Verificación de Archivos del Sistema</h5>
    <ul class='mb-0'>";

$archivosRequeridos = [
    'controllers/CarritoController.php' => 'Controlador del Carrito',
    'models/Producto.php' => 'Modelo de Productos',
    'config/helpers.php' => 'Funciones Helper',
    'index.php' => 'Enrutador Principal',
];

foreach ($archivosRequeridos as $archivo => $descripcion) {
    $existe = file_exists(__DIR__ . '/' . $archivo);
    $icono = $existe ? '✅' : '❌';
    echo "<li>$icono <strong>$descripcion</strong>: <code>$archivo</code></li>";
}

echo "</ul></div>";

// ═══════════════════════════════════════════════════════════════════════
// 4. Pruebas de funciones
// ═══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/config/helpers.php';

echo "<div class='alert alert-info'>
    <h5><i class='bi bi-4-circle me-2'></i>Pruebas de Funciones</h5>
    <ul class='mb-0'>";

echo "<li>is_authenticated(): <strong>" . (is_authenticated() ? 'true' : 'false') . "</strong></li>";
echo "<li>is_admin(): <strong>" . (is_admin() ? 'true' : 'false') . "</strong></li>";
echo "<li>get_user_role(): <strong>" . get_user_role() . "</strong></li>";
echo "<li>formatearMoneda(25000): <strong>" . formatearMoneda(25000) . "</strong></li>";

echo "</ul></div>";

// ═══════════════════════════════════════════════════════════════════════
// 5. Acciones de prueba
// ═══════════════════════════════════════════════════════════════════════
echo "<div class='alert alert-secondary'>
    <h5><i class='bi bi-5-circle me-2'></i>Acciones de Prueba</h5>
    <p>Usa estos botones para probar el carrito:</p>
    <div class='d-flex flex-wrap gap-2'>";

// Botón para agregar producto de prueba
echo "<form method='POST' action='index.php?c=carrito&a=agregar' class='d-inline'>
    <input type='hidden' name='producto_id' value='1'>
    <input type='hidden' name='cantidad' value='2'>
    <input type='hidden' name='redirect' value='test_carrito.php'>
    <button type='submit' class='btn btn-primary'>
        <i class='bi bi-cart-plus me-1'></i>Agregar Producto #1 (x2)
    </button>
</form>";

// Botón para vaciar carrito
if (!empty($_SESSION['carrito'])) {
    echo "<form method='POST' action='index.php?c=carrito&a=vaciar' class='d-inline'>
        <input type='hidden' name='redirect' value='test_carrito.php'>
        <button type='submit' class='btn btn-danger'>
            <i class='bi bi-trash me-1'></i>Vaciar Carrito
        </button>
    </form>";
}

// Botón para ir al carrito
echo "<a href='index.php?c=carrito&a=index' class='btn btn-info'>
    <i class='bi bi-eye me-1'></i>Ver Vista del Carrito
</a>";

// Botón para ir a la página principal
echo "<a href='index.php' class='btn btn-success'>
    <i class='bi bi-house me-1'></i>Ir a Página Principal
</a>";

echo "</div></div>";

// ═══════════════════════════════════════════════════════════════════════
// 6. Mensajes flash
// ═══════════════════════════════════════════════════════════════════════
$flash = get_flash();
if ($flash) {
    $claseTipo = [
        'success' => 'alert-success',
        'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
    ];
    $clase = $claseTipo[$flash['tipo']] ?? 'alert-secondary';
    
    echo "<div class='alert $clase alert-dismissible fade show'>
        <strong>Mensaje del Sistema:</strong> " . htmlspecialchars($flash['mensaje']) . "
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
    </div>";
}

// ═══════════════════════════════════════════════════════════════════════
// Footer
// ═══════════════════════════════════════════════════════════════════════
echo "          </div>
            </div>
            
            <div class='mt-4 p-3 bg-white rounded shadow-sm'>
                <h5><i class='bi bi-info-circle me-2'></i>Instrucciones</h5>
                <ol>
                    <li>Este script verifica que el carrito funciona sin autenticación</li>
                    <li>Puedes agregar productos usando el botón de arriba</li>
                    <li>Los productos se almacenan en <code>\$_SESSION['carrito']</code></li>
                    <li>El carrito persiste mientras la sesión esté activa</li>
                    <li>Para confirmar un pedido, necesitas iniciar sesión como cliente</li>
                </ol>
                
                <div class='alert alert-success mb-0'>
                    <strong>✅ Conclusión:</strong> Si puedes agregar productos usando el botón de arriba
                    sin errores, el carrito está funcionando correctamente para usuarios anónimos.
                </div>
            </div>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>

<?php
/**
 * Test de Agregar al Carrito - Diagnóstico
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de Agregar al Carrito</h1>";
echo "<hr>";

// Mostrar estado actual
echo "<h2>1. Estado Antes de Agregar</h2>";
echo "<pre>";
echo "Sesión activa: " . (session_status() === PHP_SESSION_ACTIVE ? 'SÍ' : 'NO') . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Usuario autenticado: " . (isset($_SESSION['usuario_id']) ? 'SÍ (ID: ' . $_SESSION['usuario_id'] . ')' : 'NO') . "\n";
echo "Carrito actual: " . (isset($_SESSION['carrito']) ? count($_SESSION['carrito']) . ' productos' : 'Vacío') . "\n";
echo "</pre>";

if (!empty($_SESSION['carrito'])) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Producto</th><th>Cantidad</th></tr>";
    foreach ($_SESSION['carrito'] as $id => $item) {
        echo "<tr><td>$id</td><td>" . htmlspecialchars($item['nombre'] ?? 'N/A') . "</td><td>" . ($item['cantidad'] ?? 0) . "</td></tr>";
    }
    echo "</table>";
}

// Si viene de POST, intentar agregar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
    echo "<h2>2. Procesando POST</h2>";
    echo "<pre>";
    echo "Datos recibidos:\n";
    print_r($_POST);
    echo "</pre>";
    
    // Incluir lo necesario
    require_once __DIR__ . '/config/helpers.php';
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/models/Producto.php';
    
    $productoId = filter_var($_POST['producto_id'] ?? 0, FILTER_VALIDATE_INT);
    $cantidad = filter_var($_POST['cantidad'] ?? 1, FILTER_VALIDATE_INT);
    
    echo "<h3>Datos validados:</h3>";
    echo "<pre>";
    echo "producto_id: $productoId\n";
    echo "cantidad: $cantidad\n";
    echo "</pre>";
    
    if ($productoId && $cantidad) {
        try {
            $modeloProducto = new Producto();
            $prod = $modeloProducto->obtenerPorId($productoId);
            
            echo "<h3>Producto encontrado en BD:</h3>";
            echo "<pre>";
            print_r($prod);
            echo "</pre>";
            
            if ($prod && $prod['estado'] === 'activo') {
                // Inicializar carrito si no existe
                if (!isset($_SESSION['carrito'])) {
                    $_SESSION['carrito'] = [];
                }
                
                $id = (string)$productoId;
                $stockDisponible = (int)$prod['cantidad'];
                
                if (isset($_SESSION['carrito'][$id])) {
                    // Ya existe, sumar
                    $cantidadActual = $_SESSION['carrito'][$id]['cantidad'];
                    $cantidadNueva = $cantidadActual + $cantidad;
                    
                    if ($cantidadNueva > $stockDisponible) {
                        $cantidadNueva = $stockDisponible;
                        echo "<p style='color:orange;'>⚠️ Cantidad ajustada al stock disponible</p>";
                    }
                    
                    $_SESSION['carrito'][$id]['cantidad'] = $cantidadNueva;
                    $_SESSION['carrito'][$id]['subtotal'] = $cantidadNueva * (float)$prod['precio_venta'];
                    
                    echo "<p style='color:green;'>✅ Cantidad actualizada a: $cantidadNueva</p>";
                } else {
                    // Nuevo producto
                    $cant = min($cantidad, $stockDisponible);
                    
                    $_SESSION['carrito'][$id] = [
                        'producto_id' => $productoId,
                        'nombre' => $prod['nombre'],
                        'precio_unitario' => (float)$prod['precio_venta'],
                        'unidad_medida' => $prod['unidad_medida'],
                        'cantidad' => $cant,
                        'subtotal' => $cant * (float)$prod['precio_venta'],
                        'stock_real' => $stockDisponible,
                    ];
                    
                    echo "<p style='color:green;'>✅ Producto agregado al carrito</p>";
                }
                
                echo "<h3>Carrito después de agregar:</h3>";
                echo "<pre>";
                print_r($_SESSION['carrito']);
                echo "</pre>";
                
            } else {
                echo "<p style='color:red;'>❌ Producto no disponible o inactivo</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color:red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
    } else {
        echo "<p style='color:red;'>❌ Datos inválidos</p>";
    }
} else {
    echo "<h2>2. Formulario de Prueba</h2>";
    echo "<form method='POST'>";
    echo "<p>Producto ID: <input type='number' name='producto_id' value='1' min='1'></p>";
    echo "<p>Cantidad: <input type='number' name='cantidad' value='1' min='1'></p>";
    echo "<button type='submit' style='padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;'>Agregar al Carrito</button>";
    echo "</form>";
}

echo "<hr>";
echo "<h2>3. Enlaces</h2>";
echo "<a href='publico.php' style='padding:10px; background:#28a745; color:white; text-decoration:none; margin:5px; display:inline-block;'>Ir a Público</a>";
echo "<a href='debug_carrito.php' style='padding:10px; background:#ffc107; color:black; text-decoration:none; margin:5px; display:inline-block;'>Ver Debug Carrito</a>";
echo "<a href='index.php?c=carrito' style='padding:10px; background:#17a2b8; color:white; text-decoration:none; margin:5px; display:inline-block;'>Ver Carrito</a>";

if (!empty($_SESSION['carrito'])) {
    echo "<form method='POST' action='index.php?c=carrito&a=vaciar' style='display:inline;'>";
    echo "<input type='hidden' name='redirect' value='test_agregar.php'>";
    echo "<button type='submit' style='padding:10px; background:#dc3545; color:white; border:none; margin:5px;'>Vaciar Carrito</button>";
    echo "</form>";
}
?>
<style>
body { font-family: Arial; padding: 20px; max-width: 1000px; margin: 0 auto; background: #f5f5f5; }
h1 { color: #333; }
h2 { color: #555; border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-top: 30px; }
h3 { color: #666; }
pre { background: #f4f4f4; padding: 10px; border-left: 4px solid #007bff; overflow-x: auto; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; background: white; }
th { background: #007bff; color: white; padding: 10px; }
td { padding: 8px; }
</style>

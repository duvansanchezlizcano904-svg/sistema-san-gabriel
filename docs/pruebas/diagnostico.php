<?php
/**
 * Diagnóstico Completo del Sistema
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<html><head><title>Diagnóstico del Sistema</title>";
echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.section { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
h1 { color: #333; }
h2 { color: #555; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
pre { background: #f4f4f4; padding: 10px; border-left: 4px solid #007bff; overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
th { background: #007bff; color: white; }
</style></head><body>";

echo "<h1>🔍 Diagnóstico Completo del Sistema</h1>";

// ═══════════════════════════════════════════════════════════
// 1. INFORMACIÓN DE PHP
// ═══════════════════════════════════════════════════════════
echo "<div class='section'>";
echo "<h2>1. Información de PHP</h2>";
echo "<table>";
echo "<tr><th>Parámetro</th><th>Valor</th></tr>";
echo "<tr><td>Versión de PHP</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>Session Status</td><td>" . (session_status() === PHP_SESSION_ACTIVE ? '<span class="success">ACTIVA</span>' : '<span class="error">INACTIVA</span>') . "</td></tr>";
echo "<tr><td>Display Errors</td><td>" . ini_get('display_errors') . "</td></tr>";
echo "<tr><td>Error Reporting</td><td>" . error_reporting() . "</td></tr>";
echo "</table>";
echo "</div>";

// ═══════════════════════════════════════════════════════════
// 2. ARCHIVOS DEL SISTEMA
// ═══════════════════════════════════════════════════════════
echo "<div class='section'>";
echo "<h2>2. Archivos del Sistema</h2>";
echo "<table>";
echo "<tr><th>Archivo</th><th>Estado</th><th>Tamaño</th></tr>";

$archivos = [
    'index.php',
    'publico.php',
    'config/database.php',
    'config/helpers.php',
    'controllers/PublicoController.php',
    'controllers/CarritoController.php',
    'models/Producto.php',
    'views/inicio/home.php',
    'views/publico/catalogo.php',
    'views/carrito/index.php',
];

foreach ($archivos as $archivo) {
    $path = __DIR__ . '/' . $archivo;
    $existe = file_exists($path);
    $size = $existe ? filesize($path) : 0;
    echo "<tr>";
    echo "<td>$archivo</td>";
    echo "<td>" . ($existe ? '<span class="success">✅ Existe</span>' : '<span class="error">❌ No existe</span>') . "</td>";
    echo "<td>" . ($existe ? number_format($size) . ' bytes' : '-') . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// ═══════════════════════════════════════════════════════════
// 3. CONEXIÓN A BASE DE DATOS
// ═══════════════════════════════════════════════════════════
echo "<div class='section'>";
echo "<h2>3. Conexión a Base de Datos</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    echo "<p class='success'>✅ Archivo database.php cargado correctamente</p>";
    
    if (function_exists('getDB')) {
        $db = getDB();
        echo "<p class='success'>✅ Conexión a base de datos establecida</p>";
        
        // Verificar tabla productos
        $stmt = $db->query("SELECT COUNT(*) as total FROM productos");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p class='success'>✅ Tabla 'productos' accesible - Total de productos: " . $result['total'] . "</p>";
        
        // Productos activos
        $stmt = $db->query("SELECT COUNT(*) as total FROM productos WHERE estado = 'activo'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p class='success'>✅ Productos activos: " . $result['total'] . "</p>";
        
        if ($result['total'] == 0) {
            echo "<p class='warning'>⚠️ ADVERTENCIA: No hay productos activos en la base de datos</p>";
        }
        
        // Mostrar primeros 5 productos
        $stmt = $db->query("SELECT id, codigo, nombre, estado, cantidad FROM productos LIMIT 5");
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($productos)) {
            echo "<h3>Primeros 5 productos:</h3>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Código</th><th>Nombre</th><th>Estado</th><th>Stock</th></tr>";
            foreach ($productos as $p) {
                echo "<tr>";
                echo "<td>" . $p['id'] . "</td>";
                echo "<td>" . htmlspecialchars($p['codigo']) . "</td>";
                echo "<td>" . htmlspecialchars($p['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($p['estado']) . "</td>";
                echo "<td>" . $p['cantidad'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p class='error'>❌ Función getDB() no encontrada</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
echo "</div>";

// ═══════════════════════════════════════════════════════════
// 4. PROBAR HELPERS
// ═══════════════════════════════════════════════════════════
echo "<div class='section'>";
echo "<h2>4. Funciones Helper</h2>";
try {
    require_once __DIR__ . '/config/helpers.php';
    echo "<p class='success'>✅ Archivo helpers.php cargado</p>";
    
    echo "<table>";
    echo "<tr><th>Función</th><th>Resultado</th></tr>";
    echo "<tr><td>is_authenticated()</td><td>" . (is_authenticated() ? 'true' : 'false') . "</td></tr>";
    echo "<tr><td>is_admin()</td><td>" . (is_admin() ? 'true' : 'false') . "</td></tr>";
    echo "<tr><td>get_user_role()</td><td>" . get_user_role() . "</td></tr>";
    echo "<tr><td>formatearMoneda(25000)</td><td>" . formatearMoneda(25000) . "</td></tr>";
    echo "</table>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// ═══════════════════════════════════════════════════════════
// 5. PROBAR PUBLICOCONTROLLER
// ═══════════════════════════════════════════════════════════
echo "<div class='section'>";
echo "<h2>5. PublicoController</h2>";
try {
    require_once __DIR__ . '/controllers/PublicoController.php';
    echo "<p class='success'>✅ PublicoController cargado</p>";
    
    $ctrl = new PublicoController();
    echo "<p class='success'>✅ Instancia de PublicoController creada</p>";
    
    // No llamar al método inicio() porque renderizaría HTML
    echo "<p class='success'>✅ Controller listo para usar</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
echo "</div>";

// ═══════════════════════════════════════════════════════════
// 6. PROBAR CARRITOCONTROLLER
// ═══════════════════════════════════════════════════════════
echo "<div class='section'>";
echo "<h2>6. CarritoController</h2>";
try {
    require_once __DIR__ . '/controllers/CarritoController.php';
    echo "<p class='success'>✅ CarritoController cargado</p>";
    
    $ctrl = new CarritoController();
    echo "<p class='success'>✅ Instancia de CarritoController creada</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
echo "</div>";

// ═══════════════════════════════════════════════════════════
// 7. ESTADO DE LA SESIÓN
// ═══════════════════════════════════════════════════════════
session_start();
echo "<div class='section'>";
echo "<h2>7. Estado de la Sesión</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "</div>";

// ═══════════════════════════════════════════════════════════
// 8. ENLACES DE PRUEBA
// ═══════════════════════════════════════════════════════════
echo "<div class='section'>";
echo "<h2>8. Enlaces de Prueba</h2>";
echo "<p><a href='index.php' style='padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px; display:inline-block; margin:5px;'>Ir a index.php</a></p>";
echo "<p><a href='publico.php' style='padding:10px 20px; background:#28a745; color:white; text-decoration:none; border-radius:5px; display:inline-block; margin:5px;'>Ir a publico.php</a></p>";
echo "<p><a href='debug_carrito.php' style='padding:10px 20px; background:#ffc107; color:black; text-decoration:none; border-radius:5px; display:inline-block; margin:5px;'>Ver debug_carrito.php</a></p>";
echo "<p><a href='test_simple.html' style='padding:10px 20px; background:#17a2b8; color:white; text-decoration:none; border-radius:5px; display:inline-block; margin:5px;'>Ir a test_simple.html</a></p>";
echo "</div>";

echo "</body></html>";
?>

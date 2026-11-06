<?php
/*
╔══════════════════════════════════════════════════════════════════════════════╗
║           GUÍA DE APRENDIZAJE — QUESOS SAN GABRIEL                         ║
║           ARCHIVO 05: RUTAS Y CONTROLADORES                                 ║
╚══════════════════════════════════════════════════════════════════════════════╝

¿QUÉ ES UN "ENRUTADOR"?
────────────────────────
El enrutador es el "recepcionista" del sistema.
Cuando alguien visita una URL, el enrutador decide:
  "¿A qué controlador y función debo enviar esta petición?"

En este proyecto, el enrutador está en index.php.
Usa parámetros en la URL para decidir qué hacer:

  index.php?c=productos&a=crear
             ↑           ↑
             c = controlador (products)
             a = acción (función dentro del controlador)


╔══════════════════════════════════════════════════════════════════════════════╗
║  ARCHIVO PRINCIPAL: index.php (Enrutador)                                   ║
╚══════════════════════════════════════════════════════════════════════════════╝

EJEMPLOS DE URLS Y QUÉ HACEN:
───────────────────────────────

  URL                              CONTROLADOR              FUNCIÓN
  ─────────────────────────────────────────────────────────────────
  index.php                      → PublicoController      → inicio()
  index.php?c=carrito            → CarritoController      → index()
  index.php?c=carrito&a=agregar  → CarritoController      → agregar()
  index.php?c=productos          → ProductoController     → index()
  index.php?c=productos&a=crear  → ProductoController     → crear()
  index.php?c=dashboard          → DashboardController    → index()
  login.php                      → AuthController         → login()
  registro.php                   → AuthController         → registro()

*/


// ─── EL ENRUTADOR COMPLETO (index.php) ───────────────────────────────────────
/*
    // PARTE 1: Si no hay sesión activa
    if (!is_authenticated()) {
        
        // Leer qué controlador y acción pide la URL
        $controlador = sanitize($_GET['c'] ?? '');
        $accion      = sanitize($_GET['a'] ?? 'index');
        
        // Lista blanca: el carrito SÍ funciona sin sesión
        $accionesCarritoPermitidas = ['agregar', 'actualizar', 'quitar', 'vaciar', 'index', 'checkout'];
        
        if ($controlador === 'carrito' && in_array($accion, $accionesCarritoPermitidas, true)) {
            require_once __DIR__ . '/controllers/CarritoController.php';
            $instancia = new CarritoController();
            $instancia->$accion();
            exit;
        }
        
        // Para cualquier otra cosa sin sesión → mostrar página de inicio pública
        require_once __DIR__ . '/controllers/PublicoController.php';
        $ctrl = new PublicoController();
        $ctrl->inicio();
        exit;
    }
*/

// 💡 LISTA BLANCA del carrito:
//    Aunque no hayas iniciado sesión, PUEDES agregar productos al carrito.
//    El carrito se guarda en $_SESSION['carrito'] (no necesitas cuenta para eso).
//    Solo CONFIRMAR la compra requiere estar registrado/logueado.
//
// 💡 sanitize() limpia la URL para evitar que alguien inyecte código malicioso.
//    Ej: index.php?c=<script>alert('hack')</script> → sanitize() lo neutraliza.


// ─── PARTE 2: MAPEO DE CONTROLADORES ─────────────────────────────────────────
/*
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
*/

// 💡 Este array actúa como "diccionario":
//    La CLAVE  es lo que va en la URL (?c=productos)
//    El VALOR  es el nombre de la clase PHP (ProductoController)
//
// 💡 Seguridad: si pones una URL inválida (?c=hackintento),
//    el sistema verifica con array_key_exists() que exista en el mapa.
//    Si no existe → redirige con mensaje de error. No puede acceder a nada.


// ─── PARTE 3: CARGAR Y EJECUTAR EL CONTROLADOR ──────────────────────────────
/*
    $nombreClase        = $controladoresValidos[$controlador];
    $archivoControlador = __DIR__ . '/controllers/' . $nombreClase . '.php';

    // Verificar que el archivo exista
    if (!file_exists($archivoControlador)) {
        die("Error: No se encontró el controlador {$nombreClase}.");
    }

    require_once $archivoControlador;   // Cargar el archivo PHP
    $instancia = new $nombreClase();    // Crear objeto del controlador

    // Verificar que la acción (función) exista
    if (!method_exists($instancia, $accion)) {
        die("Error: La acción '{$accion}' no existe en {$nombreClase}.");
    }

    $instancia->$accion();   // Llamar la función dinámica
*/

// 💡 require_once → incluye el archivo PHP UNA SOLA VEZ (evita declarar la clase 2 veces)
// 💡 new $nombreClase() → PHP permite crear instancias con nombre de clase dinámico
//    Si $nombreClase = 'ProductoController' → es lo mismo que new ProductoController()
// 💡 $instancia->$accion() → también se pueden llamar métodos con nombre dinámico
//    Si $accion = 'index' → es lo mismo que $instancia->index()


// ─── DIAGRAMA DEL FLUJO COMPLETO ─────────────────────────────────────────────
/*

  USUARIO escribe URL en el navegador:
  http://localhost/php%20mi%20proyecto/index.php?c=carrito&a=agregar
                                                  ↑          ↑
                                             c=carrito    a=agregar
                 │
                 ▼
  ┌─────────────────────────────────┐
  │         index.php               │
  │      (Enrutador Principal)      │
  │                                 │
  │  1. ¿Hay sesión activa?         │
  │  2. ¿El controlador es válido?  │
  │  3. ¿La acción existe?          │
  └────────────────┬────────────────┘
                   │
                   ▼
  ┌─────────────────────────────────┐
  │     CarritoController.php       │
  │       agregar() {               │
  │         // recibe datos POST    │
  │         // valida stock         │
  │         // guarda en SESSION    │
  │         // devuelve JSON/redirect│
  │       }                         │
  └────────────────┬────────────────┘
                   │
                   ▼
  ┌─────────────────────────────────┐
  │         Producto.php            │
  │    (Modelo → consulta MySQL)    │
  │    obtenerPorId($id)            │
  └─────────────────────────────────┘

*/


// ─── CONTROLADORES DEL PROYECTO ──────────────────────────────────────────────
/*

  CONTROLADOR              ACCESO          FUNCIONES PRINCIPALES
  ─────────────────────────────────────────────────────────────────────────────
  
  AuthController           Todos           login(), registro(), logout()
  
  PublicoController        Sin sesión      inicio() → Página principal pública
                                           index()  → Catálogo público
  
  CarritoController        Sin/Con sesión  index()     → Ver carrito
                                           agregar()   → Agregar al carrito
                                           actualizar()→ Cambiar cantidad
                                           quitar()    → Eliminar item
                                           vaciar()    → Vaciar todo
                                           confirmar() → Finalizar compra (req. login)
                                           gracias()   → Pantalla de éxito
                                           factura()   → Ver comprobante
  
  CatalogoController       Clientes        index() → Catálogo para clientes logueados
  
  ProductoController       Solo Admin      index(), crear(), guardar(),
                                           editar(), actualizar(), eliminar()
  
  DashboardController      Solo Admin      index() → Panel de control con estadísticas
  
  VentaController          Solo Admin      index() → Gestión de ventas manuales
  
  InventarioController     Solo Admin      index() → Vista del inventario
  
  ProveedorController      Solo Admin      index(), crear(), guardar(),
                                           editar(), actualizar(), eliminar()
  
  HistorialController      Solo Admin      index() → Historial de pedidos/ventas
  
  ReporteController        Solo Admin      index() → Reportes y exportaciones

*/


// ─── CÓMO LLEGAN LOS DATOS DEL FORMULARIO AL CONTROLADOR ────────────────────
/*

  EJEMPLO: El usuario hace clic en "Agregar al carrito" en la página de inicio.

  FORMULARIO HTML (views/partials/tarjeta_producto.php):
  ┌─────────────────────────────────────────────────────────────────────────┐
  │ <form method="POST" action="index.php?c=carrito&a=agregar">            │
  │     <input type="hidden" name="producto_id" value="5">                 │
  │     <input type="hidden" name="cantidad" value="1">                    │
  │     <input type="hidden" name="ajax" value="1">                        │
  │     <button type="submit">Agregar al carrito</button>                  │
  │ </form>                                                                 │
  └─────────────────────────────────────────────────────────────────────────┘
  
  DATOS QUE LLEGAN (en $_POST):
  ┌─────────────────────────────────────────────────────────────────────────┐
  │ $_POST['producto_id'] = "5"                                             │
  │ $_POST['cantidad']    = "1"                                             │
  │ $_POST['ajax']        = "1"                                             │
  └─────────────────────────────────────────────────────────────────────────┘

  CONTROLADOR (CarritoController::agregar()):
  ┌─────────────────────────────────────────────────────────────────────────┐
  │ $productoId = filter_var($_POST['producto_id'] ?? 0, FILTER_VALIDATE_INT);│
  │ $cantidad   = filter_var($_POST['cantidad'] ?? 1, FILTER_VALIDATE_INT);  │
  │ $esAjax     = isset($_POST['ajax']) && $_POST['ajax'] === '1';           │
  └─────────────────────────────────────────────────────────────────────────┘

  RESPUESTA (JSON si es AJAX, redirect si es formulario normal):
  ┌─────────────────────────────────────────────────────────────────────────┐
  │ {"success": true, "mensaje": "¡Queso Fresco agregado!", "total_items": 1}│
  └─────────────────────────────────────────────────────────────────────────┘

*/


// ─── ¿QUÉ ES AJAX? ──────────────────────────────────────────────────────────
/*

  AJAX = Asynchronous JavaScript And XML
  
  Sin AJAX: Cada acción recarga TODA la página (experiencia lenta)
  Con AJAX: La página NO se recarga, solo se actualiza una parte (experiencia rápida)
  
  En el carrito del proyecto:
  
  JavaScript envía los datos al servidor (PHP) sin recargar la página:
  
    fetch('index.php?c=carrito&a=agregar', {
        method: 'POST',
        body: new FormData(formulario)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Actualizar el contador del carrito en el navbar
            document.getElementById('carrito-count').textContent = data.total_items;
            // Mostrar mensaje de éxito
        }
    });
  
  El controlador PHP detecta si es AJAX con:
    $esAjax = isset($_POST['ajax']) && $_POST['ajax'] === '1';
  
  Si es AJAX → responde con JSON:   {"success": true, "total_items": 2}
  Si NO es AJAX → redirige normal:  header("Location: index.php?c=carrito")

*/


// ─── ARCHIVOS INVOLUCRADOS ──────────────────────────────────────────────────
/*

  ENRUTADOR:
  └── index.php                     → Punto de entrada principal
  └── login.php                     → Redirige a AuthController::login()
  └── registro.php                  → Redirige a AuthController::registro()
  └── logout.php                    → Cierra sesión y redirige
  └── publico.php                   → Entrada a PublicoController

  CONTROLADORES (controllers/):
  ├── AuthController.php
  ├── CarritoController.php
  ├── CatalogoController.php
  ├── DashboardController.php
  ├── HistorialController.php
  ├── InventarioController.php
  ├── ProductoController.php
  ├── ProveedorController.php
  ├── PublicoController.php
  ├── ReporteController.php
  └── VentaController.php

  MODELOS (models/):
  ├── Pedido.php    → Gestión de pedidos/compras de clientes
  ├── Producto.php  → Catálogo e inventario de quesos
  ├── Proveedor.php → Gestión de proveedores
  ├── Usuario.php   → Autenticación y gestión de usuarios
  └── Venta.php     → Ventas manuales del administrador

  CONFIGURACIÓN (config/):
  ├── database.php  → Conexión PDO a MySQL
  └── helpers.php   → Funciones globales (redirect, sanitize, etc.)

*/


// ─── RESUMEN PARA PRINCIPIANTES ─────────────────────────────────────────────
/*

  📌 CONCEPTOS QUE APRENDISTE AQUÍ:

  1. ENRUTADOR:
     - Un archivo central (index.php) que decide qué código ejecutar según la URL.
     - Usa parámetros GET: ?c=controlador&a=accion

  2. PATRÓN MVC:
     - MODEL (Modelo)     → Consulta la base de datos
     - VIEW (Vista)       → Muestra el HTML al usuario
     - CONTROLLER (Ctrl.) → Coordina: recibe datos, procesa, llama Modelo y Vista

  3. LISTA BLANCA DE SEGURIDAD:
     - Solo se permite acceder a controladores y acciones definidas en el mapa.
     - Cualquier URL no registrada es bloqueada automáticamente.

  4. AJAX vs FORMULARIO NORMAL:
     - AJAX: respuesta en JSON, página no recarga (botón "Agregar al carrito")
     - Normal: redirige con redirect() (formularios de login, registro, etc.)

  5. ROLES Y PERMISOS:
     - Visitante sin sesión → solo puede ver la tienda y el carrito
     - Cliente → puede comprar, ver su historial
     - Administrador → puede gestionar todo el sistema

*/

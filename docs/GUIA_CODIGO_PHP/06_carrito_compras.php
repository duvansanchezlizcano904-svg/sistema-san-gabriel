<?php
/*
╔══════════════════════════════════════════════════════════════════════════════╗
║           GUÍA DE APRENDIZAJE — QUESOS SAN GABRIEL                         ║
║           ARCHIVO 06: EL CARRITO DE COMPRAS                                 ║
╚══════════════════════════════════════════════════════════════════════════════╝

¿QUÉ ES EL CARRITO DE COMPRAS?
────────────────────────────────
Es el módulo que permite al cliente:
  1. Agregar quesos al carrito (sin necesidad de cuenta)
  2. Ver todos los productos seleccionados
  3. Cambiar cantidades o eliminar productos
  4. Confirmar la compra (requiere login)
  5. Ver la factura/tiquete de la compra

¿DÓNDE SE GUARDA EL CARRITO?
──────────────────────────────
El carrito se guarda en la SESIÓN del servidor ($_SESSION['carrito']).
NO se guarda en la base de datos hasta que se confirma la compra.

Esto significa:
  ✅ Puedes agregar productos sin estar registrado
  ✅ El carrito persiste mientras el navegador esté abierto
  ❌ Si cierras el navegador, el carrito puede perderse
  ❌ Solo al CONFIRMAR se descuenta el stock y se registra el pedido


╔══════════════════════════════════════════════════════════════════════════════╗
║  ARCHIVO PRINCIPAL: controllers/CarritoController.php                       ║
╚══════════════════════════════════════════════════════════════════════════════╝
*/


// ─── ESTRUCTURA DEL CARRITO EN SESIÓN ────────────────────────────────────────
/*

  $_SESSION['carrito'] es un array asociativo donde:
  - La CLAVE  es el ID del producto (como string)
  - El VALOR  es un array con los datos del producto

  EJEMPLO:
  $_SESSION['carrito'] = [
      '3' => [
          'producto_id'    => 3,
          'nombre'         => 'Queso Fresco Artesanal',
          'precio_unitario'=> 15000.00,
          'unidad_medida'  => 'Kilogramo',
          'cantidad'       => 2,
          'subtotal'       => 30000.00,    // precio x cantidad
          'stock_real'     => 50,          // stock disponible en BD
          'categoria'      => 'Quesos Frescos',
          'tipo_leche'     => 'Vaca',
          'imagen'         => 'public/img/productos/queso_fresco.jpg',
      ],
      '7' => [
          'producto_id'    => 7,
          'nombre'         => 'Queso Azul Artesanal',
          'precio_unitario'=> 25000.00,
          'cantidad'       => 1,
          'subtotal'       => 25000.00,
          ...
      ]
  ];

*/


// ─── FUNCIÓN: agregar() — La más importante ───────────────────────────────────
/*

  Se activa cuando el cliente hace clic en "Agregar al carrito".

  FLUJO DETALLADO:

  1. Detectar si es AJAX (petición desde JavaScript sin recargar página):
     $esAjax = isset($_POST['ajax']) && $_POST['ajax'] === '1';

  2. Obtener y validar los datos del formulario:
     $productoId = filter_var($_POST['producto_id'] ?? 0, FILTER_VALIDATE_INT);
     $cantidad   = filter_var($_POST['cantidad'] ?? 1, FILTER_VALIDATE_INT);

     filter_var con FILTER_VALIDATE_INT:
     - Devuelve el número entero si es válido
     - Devuelve FALSE si no es un número (protección contra datos inválidos)

  3. Consultar MySQL para verificar que el producto existe y está activo:
     $prod = $this->modeloProducto->obtenerPorId($productoId);
     if (!$prod || $prod['estado'] !== 'activo') { ... error ... }

  4. Verificar que hay stock disponible:
     $stockDisponible = (int)$prod['cantidad'];
     if ($stockDisponible <= 0) { ... producto agotado ... }

  5a. Si el producto YA está en el carrito → aumentar cantidad:
      $cantidadActual = (int)$_SESSION['carrito'][$id]['cantidad'];
      $cantidadNueva  = $cantidadActual + $cantidad;
      
      // Respetar el límite de stock:
      if ($cantidadNueva > $stockDisponible) {
          $cantidadNueva = $stockDisponible;  // No puede pedir más del disponible
      }
      
      $_SESSION['carrito'][$id]['cantidad'] = $cantidadNueva;
      $_SESSION['carrito'][$id]['subtotal'] = $cantidadNueva * (float)$prod['precio_venta'];

  5b. Si es NUEVO en el carrito → insertar con toda la info:
      $_SESSION['carrito'][$id] = [
          'producto_id'    => $productoId,
          'nombre'         => $prod['nombre'],
          'precio_unitario'=> (float)$prod['precio_venta'],
          'cantidad'       => $cantFinal,
          'subtotal'       => $cantFinal * (float)$prod['precio_venta'],
          'stock_real'     => $stockDisponible,
          ...
      ];

  6. Calcular totales actualizados del carrito:
     $totalItems = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
     $totalMonto = array_sum(array_column($_SESSION['carrito'], 'subtotal'));

  7a. Si es AJAX → devolver JSON con resultado:
      echo json_encode([
          'success'     => true,
          'mensaje'     => '¡Queso Fresco agregado!',
          'total_items' => 2,
          'total_monto' => 45000.00,
      ]);

  7b. Si NO es AJAX → redirigir con mensaje flash:
      set_flash('success', '¡Producto agregado!');
      redirect('index.php');

*/


// ─── FUNCIÓN: confirmar() — La compra final ──────────────────────────────────
/*

  Se activa cuando el cliente hace clic en "Confirmar Pedido".

  FLUJO DE CONFIRMACIÓN:

  1. VERIFICAR AUTENTICACIÓN (solo clientes pueden comprar):
     if (!is_authenticated()) {
         set_flash('warning', 'Para comprar necesitas iniciar sesión.');
         redirect('index.php?c=carrito');
     }

  2. VERIFICAR QUE NO SEA ADMINISTRADOR:
     if (is_admin()) {
         set_flash('danger', 'El admin no puede hacer compras.');
         redirect('index.php?c=carrito');
     }

  3. VERIFICAR TOKEN CSRF (anti-duplicados):
     $tokenEnviado = $_POST['csrf_token'] ?? '';
     $tokenSesion  = $_SESSION['pedido_csrf_token'] ?? '';
     
     if (!hash_equals($tokenSesion, $tokenEnviado)) {
         set_flash('warning', 'Este pedido ya fue procesado.');
         redirect('index.php?c=carrito');
     }
     
     // Invalidar el token INMEDIATAMENTE para evitar doble envío:
     unset($_SESSION['pedido_csrf_token']);

  4. REGISTRAR EL PEDIDO EN MYSQL (transacción atómica):
     $pedidoId = $this->modeloPedido->registrarPedido([
         'cliente_id' => (int)$_SESSION['usuario_id'],
         'notas'      => $notas,
     ], $items);

  5. VACIAR EL CARRITO:
     $_SESSION['carrito'] = [];

  6. REDIRIGIR A PANTALLA DE ÉXITO:
     redirect('index.php?c=carrito&a=gracias&id=' . $pedidoId);

*/


// ─── ¿QUÉ ES UN TOKEN CSRF? ──────────────────────────────────────────────────
/*

  CSRF = Cross-Site Request Forgery (Falsificación de petición entre sitios)
  
  PROBLEMA SIN CSRF:
  Un hacker podría enviarte un email con un link que, al hacer clic,
  confirmaría una compra en tu cuenta sin que lo sepas.
  
  SOLUCIÓN CON TOKEN CSRF:
  1. Cuando el usuario VE el carrito, PHP genera un token secreto:
     $_SESSION['pedido_csrf_token'] = bin2hex(random_bytes(16));
     // Ej: "a3f9b2c1d4e5f6a7b8c9d0e1f2a3b4c5"
  
  2. El token se incluye como campo oculto en el formulario de confirmación:
     <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
  
  3. Al confirmar, PHP verifica que el token coincida:
     if (!hash_equals($tokenSesion, $tokenEnviado)) {
         // Alguien intentó enviar el formulario sin el token correcto → BLOQUEADO
     }
  
  4. Después de usar el token, se elimina para que no sirva otra vez:
     unset($_SESSION['pedido_csrf_token']);
  
  RESULTADO:
  - Si recargas la página de confirmación → el pedido NO se duplica
  - Si un hacker envía el formulario → no tiene el token correcto → BLOQUEADO

*/


// ─── MODELO: registrarPedido() — Transacción atómica ────────────────────────
/*

  En models/Pedido.php, esta función realiza múltiples operaciones en MySQL
  asegurando que TODAS se completen o NINGUNA se aplique.

  public function registrarPedido(array $pedido, array $items): int {
      
      $db = Database::getConnection();
      $db->beginTransaction();   // ← Iniciar transacción
      
      try {
          // 1. Crear el pedido en la tabla "pedidos"
          $stmt = $db->prepare("INSERT INTO pedidos (cliente_id, total, estado, notas) VALUES (...)");
          $stmt->execute([...]);
          $pedidoId = (int)$db->lastInsertId();
          
          // 2. Para cada producto, insertar en "detalle_pedidos" y descontar stock
          foreach ($items as $item) {
              // Insertar detalle
              $stmt2 = $db->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, ...) VALUES (...)");
              $stmt2->execute([...]);
              
              // Descontar del inventario
              $ok = $this->modeloProducto->descontarStock($item['producto_id'], $item['cantidad'], $db);
              
              if (!$ok) {
                  throw new Exception("Stock insuficiente para: {$item['nombre']}");
              }
          }
          
          $db->commit();      // ← Confirmar TODOS los cambios
          return $pedidoId;
          
      } catch (Exception $e) {
          $db->rollBack();    // ← Si algo falla, DESHACER todo
          throw $e;
      }
  }

  ¿POR QUÉ ES IMPORTANTE LA TRANSACCIÓN?
  Si 3 productos se insertan bien pero el 4to falla por falta de stock:
  ❌ Sin transacción: Los 3 primeros quedan en la BD pero el pedido está incompleto.
  ✅ Con transacción:  rollBack() deshace todo → la BD queda como si nada hubiera pasado.

*/


// ─── FLUJO VISUAL DEL CARRITO ────────────────────────────────────────────────
/*

  [VISITA LA TIENDA]
       │
       ▼
  [Ve los quesos en el catálogo]
       │
       ▼
  [Clic en "Agregar al carrito"]
       │ (AJAX → sin recargar página)
       ▼
  [CarritoController::agregar()]
  → Verifica producto en MySQL
  → Guarda en $_SESSION['carrito']
  → Devuelve JSON {"success": true, "total_items": 1}
       │
       ▼
  [JavaScript actualiza el contador del navbar: 🛒 1]
       │
       ▼
  [Cliente hace clic en el ícono del carrito]
       │
       ▼
  [CarritoController::index()]
  → Refresca precios desde MySQL
  → Muestra vista carrito/index.php
       │
       ▼
  [¿Está logueado?]
  ├── NO → Muestra botones de Login/Registro antes de confirmar
  └── SÍ → Muestra formulario de confirmación
       │
       ▼
  [Clic en "Confirmar Pedido"]
       │
       ▼
  [CarritoController::confirmar()]
  → Verifica autenticación
  → Verifica token CSRF
  → Pedido::registrarPedido() (transacción MySQL)
     → INSERT en pedidos
     → INSERT en detalle_pedidos (x cada producto)
     → UPDATE productos (descontar stock)
  → Vacía $_SESSION['carrito']
       │
       ▼
  [Redirige a CarritoController::gracias()]
  → Muestra pantalla de éxito con el pedido
  → Botón para ver la factura/tiquete

*/


// ─── TABLAS DE MySQL INVOLUCRADAS ────────────────────────────────────────────
/*

  TABLA: pedidos
  ┌──────────────┬──────────┬─────────────────────────────────────────────────┐
  │ columna      │ tipo     │ descripción                                     │
  ├──────────────┼──────────┼─────────────────────────────────────────────────┤
  │ id           │ INT PK   │ Identificador único del pedido                  │
  │ numero_pedido│ VARCHAR  │ Código legible: PED-000001                      │
  │ cliente_id   │ INT FK   │ → usuarios.id (quién compró)                   │
  │ total        │ DECIMAL  │ Suma total del pedido                           │
  │ estado       │ VARCHAR  │ "pendiente", "procesando", "entregado"          │
  │ notas        │ TEXT     │ Instrucciones especiales del cliente            │
  │ fecha        │ TIMESTAMP│ Fecha y hora de la compra                       │
  └──────────────┴──────────┴─────────────────────────────────────────────────┘

  TABLA: detalle_pedidos
  ┌──────────────────┬──────────┬─────────────────────────────────────────────┐
  │ columna          │ tipo     │ descripción                                 │
  ├──────────────────┼──────────┼─────────────────────────────────────────────┤
  │ id               │ INT PK   │ Identificador del detalle                   │
  │ pedido_id        │ INT FK   │ → pedidos.id                               │
  │ producto_id      │ INT FK   │ → productos.id                             │
  │ producto_nombre  │ VARCHAR  │ Nombre guardado (por si se edita el producto)│
  │ cantidad         │ INT      │ Cuántas unidades se compraron               │
  │ precio_unitario  │ DECIMAL  │ Precio al momento de la compra              │
  │ subtotal         │ DECIMAL  │ precio_unitario × cantidad                  │
  └──────────────────┴──────────┴─────────────────────────────────────────────┘

  ¿POR QUÉ GUARDAR producto_nombre Y precio_unitario?
  Si mañana cambias el precio del queso o cambias su nombre,
  los pedidos históricos seguirán mostrando el precio y nombre ORIGINAL.
  ¡Es crucial para la integridad histórica!

*/


// ─── RESUMEN PARA PRINCIPIANTES ─────────────────────────────────────────────
/*

  📌 CONCEPTOS QUE APRENDISTE AQUÍ:

  1. SESIONES PARA EL CARRITO:
     - $_SESSION es memoria temporal en el servidor (dura mientras el tab está abierto)
     - No necesitas cuenta para agregar al carrito, solo para comprar

  2. AJAX:
     - Peticiones al servidor sin recargar la página
     - El servidor responde con JSON (formato de datos)
     - JavaScript actualiza la interfaz dinámicamente

  3. TOKENS CSRF:
     - Previenen el doble envío de formularios y ataques de terceros
     - Se generan con bin2hex(random_bytes(16)) → totalmente aleatorio
     - Se invalidan inmediatamente después de usarse una vez

  4. TRANSACCIONES MySQL:
     - beginTransaction() → "empieza a guardar cambios temporalmente"
     - commit() → "confirma TODOS los cambios permanentemente"
     - rollBack() → "cancela TODOS los cambios si algo salió mal"
     - Garantizan que nunca queden datos a medias

  5. STOCK Y PRECIOS CONGELADOS:
     - El precio se guarda en detalle_pedidos al momento de comprar
     - Si mañana sube el precio, el historial muestra el precio original

*/

<?php
/*
╔══════════════════════════════════════════════════════════════════════════════╗
║           GUÍA DE APRENDIZAJE — QUESOS SAN GABRIEL                         ║
║           ARCHIVO 01: INICIO DE SESIÓN (LOGIN)                              ║
╚══════════════════════════════════════════════════════════════════════════════╝

¿QUÉ ES EL LOGIN?
─────────────────
El "login" es el proceso por el cual un usuario demuestra que tiene
una cuenta y puede acceder al sistema. Es como mostrar tu cédula
en la puerta de un edificio.

¿CÓMO FUNCIONA EL LOGIN EN ESTE PROYECTO?
──────────────────────────────────────────
El proyecto usa el patrón MVC (Modelo - Vista - Controlador):

  USUARIO escribe su correo y contraseña en el formulario (VISTA)
       ↓
  El formulario envía los datos al CONTROLADOR (AuthController)
       ↓
  El CONTROLADOR llama al MODELO (Usuario) para verificar en MySQL
       ↓
  Si los datos son correctos → se guarda la sesión y se redirige
  Si los datos son incorrectos → se muestra un mensaje de error


╔══════════════════════════════════════════════════════════════════════════════╗
║  ARCHIVO PRINCIPAL: controllers/AuthController.php → función login()        ║
╚══════════════════════════════════════════════════════════════════════════════╝

CÓDIGO ORIGINAL CON EXPLICACIONES:
*/

// ─── CÓDIGO EXPLICADO PASO A PASO ────────────────────────────────────────────

// PASO 1: El controlador revisa si el usuario YA tiene sesión activa.
// Si ya inició sesión, no tiene sentido mostrarle el login de nuevo.
/*
    if (is_authenticated()) {
        if (is_admin()) {
            redirect('index.php?c=dashboard');   // Admin → va al panel
        } else {
            redirect('index.php?c=catalogo');    // Cliente → va al catálogo
        }
    }
*/

// 💡 CONCEPTOS CLAVE:
//    is_authenticated() → función en config/helpers.php que verifica si
//                         existe $_SESSION['usuario_id']
//    redirect()         → función que redirige a otra página con header()


// PASO 2: Si el formulario fue enviado (método POST), procesamos los datos.
/*
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'login') {
*/

// 💡 $_SERVER['REQUEST_METHOD'] nos dice si el usuario envió un formulario (POST)
//    o solo visitó la página (GET)
// 💡 $_POST['_action'] es un campo oculto en el formulario HTML para confirmar
//    que se mandó desde el formulario correcto.


// PASO 3: Obtener y validar los datos del formulario
/*
    $emailIngresado = trim($_POST['email'] ?? '');
    $password       = $_POST['password'] ?? '';

    if (empty($emailIngresado) || empty($password)) {
        $error = 'Por favor, completa todos los campos requeridos.';
    }
*/

// 💡 trim() elimina espacios en blanco al inicio y al final del texto
// 💡 ?? '' significa "si no existe ese valor, usa string vacío"
// 💡 empty() revisa si una variable está vacía o no existe


// PASO 4: Verificar credenciales con el MODELO de Usuario
/*
    $user = $this->modeloUsuario->autenticar($emailIngresado, $password);
*/

// 💡 $this->modeloUsuario es un objeto de la clase Usuario (models/Usuario.php)
//    La función autenticar() busca en la base de datos MySQL si ese correo
//    y contraseña son correctos. Usa password_verify() para comparar
//    el hash guardado con la contraseña ingresada.
//
//    IMPORTANTE: Las contraseñas NUNCA se guardan en texto plano.
//    Se guardan como un "hash" (código cifrado) usando password_hash().


// PASO 5: Si el usuario es válido, guardar la SESIÓN
/*
    if ($user) {
        session_regenerate_id(true);  // Seguridad: cambia el ID de sesión

        $_SESSION['usuario_id']       = $user['id'];
        $_SESSION['usuario_nombre']   = $user['nombre'];
        $_SESSION['usuario_username'] = $user['usuario'];
        $_SESSION['usuario_email']    = $user['email'];
        $_SESSION['usuario_rol']      = $user['rol'] ?? 'cliente';
*/

// 💡 $_SESSION es un array especial que PHP guarda en el servidor.
//    Funciona como una memoria temporal para recordar quién es el usuario
//    mientras navega por el sitio.
//
// 💡 session_regenerate_id(true) es una medida de seguridad para
//    prevenir ataques de "Session Fixation" (fijación de sesión).


// PASO 6: Redirigir según el rol del usuario
/*
    if ($user['rol'] === 'administrador') {
        redirect('index.php?c=dashboard');      // Admin → panel de control
    } elseif ($hayCarrito) {
        redirect('index.php?c=carrito');        // Cliente con carrito → checkout
    } else {
        redirect('index.php');                  // Cliente normal → inicio
    }
*/


// ─── DIAGRAMA DE FLUJO DEL LOGIN ──────────────────────────────────────────────
/*

  [Usuario abre login.php]
          │
          ▼
  [¿Ya tiene sesión?] ──SÍ──▶ [Redirigir según rol]
          │
         NO
          ▼
  [Muestra el formulario HTML]
          │
  [Usuario escribe email y contraseña]
          │
          ▼
  [Envía formulario (POST)]
          │
          ▼
  [AuthController::login() recibe los datos]
          │
          ▼
  [Valida que no estén vacíos]──VACÍOS──▶ [Mostrar error]
          │
          ▼
  [Usuario::autenticar() busca en MySQL]
          │
          ▼
  [¿Encontró al usuario?]──NO──▶ [Mostrar "credenciales incorrectas"]
          │
         SÍ
          ▼
  [Guardar datos en $_SESSION]
          │
          ▼
  [Redirigir al destino según rol]

*/


// ─── ARCHIVOS INVOLUCRADOS ──────────────────────────────────────────────────
/*

  login.php                    → Punto de entrada (redirige a AuthController)
  controllers/AuthController.php → Contiene la lógica del login
  models/Usuario.php           → Consulta la base de datos MySQL
  config/helpers.php           → Funciones útiles: is_authenticated(), redirect()
  views/login/login.php        → El formulario HTML que ve el usuario

  TABLA EN MySQL: usuarios
  ┌──────────────┬─────────────┬───────────────────────────────────────────────┐
  │  columna     │  tipo       │  descripción                                  │
  ├──────────────┼─────────────┼───────────────────────────────────────────────┤
  │  id          │  INT        │  Identificador único del usuario              │
  │  nombre      │  VARCHAR    │  Nombre completo                              │
  │  usuario     │  VARCHAR    │  Nombre de usuario (generado del email)       │
  │  email       │  VARCHAR    │  Correo electrónico (también sirve para login)│
  │  password    │  VARCHAR    │  Contraseña hasheada con bcrypt               │
  │  rol         │  VARCHAR    │  'administrador' o 'cliente'                  │
  │  creado_en   │  TIMESTAMP  │  Fecha de registro                            │
  └──────────────┴─────────────┴───────────────────────────────────────────────┘

*/


// ─── FUNCIÓN AUXILIAR: autenticar() en models/Usuario.php ───────────────────
/*

    public function autenticar(string $emailOUsuario, string $password): ?array {
        // 1. Busca por email primero, luego por nombre de usuario
        $user = $this->obtenerPorEmail($emailOUsuario)
             ?? $this->obtenerPorUsuario($emailOUsuario);

        // 2. Verifica la contraseña con el hash guardado en MySQL
        if ($user && password_verify($password, $user['password'])) {
            return $user;  // ✅ Retorna el usuario si todo es correcto
        }
        return null;       // ❌ Retorna null si las credenciales son incorrectas
    }

*/

// 💡 password_verify($password, $hash):
//    Compara la contraseña ingresada con el hash en la base de datos.
//    Aunque el hash se ve así: "$2y$10$abcXYZ...", PHP sabe compararlo.
//    Es IMPOSIBLE "descifrar" el hash para obtener la contraseña original.


// ─── RESUMEN PARA PRINCIPIANTES ─────────────────────────────────────────────
/*

  📌 CONCEPTOS QUE APRENDISTE AQUÍ:
  
  1. SESSION ($_SESSION): Permite recordar datos del usuario entre páginas.
  
  2. POST vs GET:
     - GET  = datos visibles en la URL (ej: ?nombre=Juan)
     - POST = datos enviados de forma oculta (formularios de login/registro)
  
  3. password_hash() / password_verify():
     - Las contraseñas NUNCA se guardan en texto plano por seguridad.
     - Se guardan cifradas (hasheadas) y se comparan con password_verify().
  
  4. Roles:
     - 'administrador': puede gestionar productos, ventas, etc.
     - 'cliente': puede comprar productos.
  
  5. Redirección: header("Location: url") mueve al usuario a otra página.

*/

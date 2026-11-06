<?php
/*
╔══════════════════════════════════════════════════════════════════════════════╗
║           GUÍA DE APRENDIZAJE — QUESOS SAN GABRIEL                         ║
║           ARCHIVO 02: REGISTRO DE NUEVOS USUARIOS                           ║
╚══════════════════════════════════════════════════════════════════════════════╝

¿QUÉ ES EL REGISTRO?
────────────────────
El registro permite que una persona que nunca ha entrado al sistema
cree su propia cuenta. Es como llenar una ficha en la entrada de un club.

¿CÓMO FUNCIONA EN ESTE PROYECTO?
─────────────────────────────────
El proceso pasa por estas etapas:

  USUARIO llena el formulario de registro (nombre, email, teléfono, contraseña)
       ↓
  El formulario envía los datos al CONTROLADOR (AuthController::registro())
       ↓
  El CONTROLADOR valida todos los campos
       ↓
  El MODELO (Usuario::registrar()) guarda el nuevo usuario en MySQL
       ↓
  Se crea la sesión automáticamente y el usuario queda logueado
       ↓
  Se redirige al catálogo (o al carrito si tenía productos)


╔══════════════════════════════════════════════════════════════════════════════╗
║  ARCHIVO PRINCIPAL: controllers/AuthController.php → función registro()     ║
╚══════════════════════════════════════════════════════════════════════════════╝
*/

// ─── CÓDIGO EXPLICADO PASO A PASO ────────────────────────────────────────────


// PASO 1: Obtener los datos del formulario HTML
/*
    $nombre    = trim($_POST['reg_nombre']    ?? '');
    $email     = trim($_POST['reg_email']     ?? '');
    $telefono  = trim($_POST['reg_telefono']  ?? '');
    $password  = $_POST['reg_password']  ?? '';
    $password2 = $_POST['reg_password2'] ?? '';
*/

// 💡 Los campos del formulario tienen prefijo 'reg_' (ej: reg_nombre, reg_email)
//    Esto evita conflictos con otros formularios (como el de login)
//    que están en la misma página.


// PASO 2: Validaciones — Verificar que todo esté correcto ANTES de guardar
/*
    if (empty($nombre) || empty($email) || empty($telefono) || empty($password) || empty($password2)) {
        $errorRegistro = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorRegistro = 'El correo electrónico no tiene un formato válido.';
    } elseif ($password !== $password2) {
        $errorRegistro = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $errorRegistro = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($this->modeloUsuario->obtenerPorEmail($email) !== null) {
        $errorRegistro = 'Este correo ya está registrado. Intenta iniciar sesión.';
    }
*/

// 💡 LISTA DE VALIDACIONES APLICADAS:
//
//   ✅ Campos vacíos       → empty()
//   ✅ Email válido        → filter_var($email, FILTER_VALIDATE_EMAIL)
//   ✅ Contraseñas iguales → $password !== $password2
//   ✅ Mínimo 6 caracteres → strlen($password) < 6
//   ✅ Email único         → consulta en MySQL para ver si ya existe


// PASO 3: Si todo está bien, llamar al Modelo para guardar en MySQL
/*
    $ok = $this->modeloUsuario->registrar($nombre, $email, $telefono, $password);
*/

// 💡 La función registrar() en models/Usuario.php:
//    1. Genera un nombre de usuario único a partir del email
//       Ej: "juan.perez@gmail.com" → "juanperez"
//    2. Si ese usuario ya existe, agrega un número: "juanperez1", "juanperez2"
//    3. Cifra la contraseña con password_hash()
//    4. Guarda en MySQL con INSERT INTO usuarios ...


// PASO 4: Después del registro, iniciar sesión automáticamente
/*
    $nuevoUsuario = $this->modeloUsuario->obtenerPorEmail($email);
    if ($nuevoUsuario) {
        session_regenerate_id(true);
        $_SESSION['usuario_id']     = $nuevoUsuario['id'];
        $_SESSION['usuario_nombre'] = $nuevoUsuario['nombre'];
        $_SESSION['usuario_email']  = $nuevoUsuario['email'];
        $_SESSION['usuario_rol']    = 'cliente';
    }
*/

// 💡 Después del registro exitoso, el sistema:
//    - Busca al usuario recién creado en la BD para obtener su ID
//    - Lo logea automáticamente (sin que tenga que ir al login)
//    - Si tenía productos en el carrito → lo lleva al carrito
//    - Si no tenía carrito → lo lleva al catálogo de productos


// ─── FUNCIÓN COMPLETA EN models/Usuario.php ──────────────────────────────────
/*

    public function registrar(string $nombre, string $email, string $telefono, string $password): bool {

        // 1. Crear nombre de usuario a partir del email
        $baseUsuario = strtolower(explode('@', $email)[0]);
        // Ej: "Juan.Perez@gmail.com" → "juan.perez" → limpiado → "juanperez"
        $baseUsuario = preg_replace('/[^a-z0-9_]/', '', $baseUsuario);
        $usuario     = $baseUsuario;

        // 2. Si el nombre de usuario ya existe, agregarle un número
        $intento = 0;
        while ($this->obtenerPorUsuario($usuario) !== null) {
            $intento++;
            $usuario = $baseUsuario . $intento;  // juanperez, juanperez1, juanperez2...
        }

        // 3. Cifrar la contraseña (NUNCA guardar en texto plano)
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // 4. Insertar en MySQL
        $sql = "INSERT INTO usuarios (nombre, usuario, password, email, telefono, rol)
                VALUES (:nombre, :usuario, :password, :email, :telefono, 'cliente')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'   => $nombre,
            ':usuario'  => $usuario,
            ':password' => $hash,
            ':email'    => $email,
            ':telefono' => $telefono,
        ]);
    }

*/


// ─── DIAGRAMA: ¿QUÉ PASA CUANDO ME REGISTRO? ────────────────────────────────
/*

  [Usuario llena el formulario de registro]
           │
           ▼
  [AuthController::registro() recibe los datos via POST]
           │
           ▼
  [¿Todos los campos están llenos?] ──NO──▶ [Mostrar error]
           │SÍ
           ▼
  [¿El email tiene formato válido?] ──NO──▶ [Mostrar error]
           │SÍ
           ▼
  [¿Las contraseñas coinciden?] ──NO──▶ [Mostrar error]
           │SÍ
           ▼
  [¿El correo ya está en MySQL?] ──SÍ──▶ [Mostrar "ya registrado"]
           │NO
           ▼
  [Usuario::registrar() guarda en MySQL con password_hash()]
           │
           ▼
  [Se inicia sesión automáticamente ($SESSION)]
           │
           ▼
  [¿Tenía carrito?] ──SÍ──▶ [Ir al carrito para comprar]
           │NO
           ▼
  [Ir al catálogo de productos]

*/


// ─── SEGURIDAD IMPLEMENTADA ──────────────────────────────────────────────────
/*

  🔐 MEDIDAS DE SEGURIDAD EN EL REGISTRO:

  1. PASSWORD HASHING (cifrado de contraseñas):
     - Se usa PASSWORD_BCRYPT (algoritmo bcrypt), el más seguro para contraseñas.
     - Aunque alguien robe la base de datos, NO puede ver las contraseñas.
     - Ejemplo:
       Contraseña: "mipassword123"
       Hash en BD: "$2y$10$X9jSd3p/4Hkr7.GYT8ZHMuQmVA.JHI1mK2lNPfLWsErYjdOVpKWa"

  2. SESSION REGENERATION:
     - session_regenerate_id(true) cambia el ID de sesión tras el login/registro.
     - Previene ataques de "Session Fixation".

  3. VALIDACIÓN DOBLE:
     - Validación en el servidor (PHP) — siempre necesaria
     - El HTML puede tener validación también, pero NUNCA confiar solo en ella

  4. CONSULTAS PREPARADAS (PDO Prepared Statements):
     - Se usa $db->prepare($sql) y ->execute([params])
     - Previene ataques de SQL Injection

*/


// ─── ¿QUÉ ES SQL INJECTION Y POR QUÉ LO PREVIENE? ─────────────────────────
/*

  ❌ CÓDIGO INSEGURO (NO usamos esto):
     $sql = "INSERT INTO usuarios WHERE email = '" . $_POST['email'] . "'";
     Un hacker podría escribir: email = "' OR '1'='1" y romper la consulta.

  ✅ CÓDIGO SEGURO (LO QUE USAMOS):
     $stmt = $db->prepare("INSERT INTO usuarios WHERE email = :email");
     $stmt->execute([':email' => $_POST['email']]);
     Aquí PHP "escapa" automáticamente los caracteres peligrosos.

*/


// ─── ARCHIVOS INVOLUCRADOS ──────────────────────────────────────────────────
/*

  registro.php                    → Punto de entrada (redirige al controlador)
  controllers/AuthController.php  → Función registro() con toda la lógica
  models/Usuario.php              → Función registrar() que inserta en MySQL
  config/helpers.php              → redirect(), set_flash(), is_authenticated()
  views/registro/registro.php     → El formulario HTML del registro

  TABLA EN MySQL: usuarios
  - El sistema SOLO puede crear usuarios con rol 'cliente' desde este flujo.
  - Los administradores se crean directamente en la base de datos.

*/


// ─── RESUMEN PARA PRINCIPIANTES ─────────────────────────────────────────────
/*

  📌 CONCEPTOS QUE APRENDISTE AQUÍ:

  1. VALIDACIÓN DE FORMULARIOS:
     - Siempre valida datos en el servidor (PHP), no solo en HTML.
     - Verifica: campos vacíos, formato de email, contraseñas iguales, duplicados.

  2. CIFRADO DE CONTRASEÑAS:
     - password_hash($password, PASSWORD_BCRYPT) → crea el hash.
     - password_verify($ingresada, $hash) → verifica en el login.
     - NUNCA guardes contraseñas en texto plano.

  3. CONSULTAS PREPARADAS (PDO):
     - Evitan SQL Injection (inyección de código malicioso en MySQL).
     - Siempre usar $db->prepare() + ->execute([$params]).

  4. UX (Experiencia de usuario):
     - Tras el registro, el usuario queda logueado automáticamente.
     - Si tenía productos en el carrito, se le lleva directo a completar compra.

*/

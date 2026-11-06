<?php
/**
 * Controlador de Autenticación
 * Maneja el inicio de sesión, registro y cierre de sesión de usuarios.
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private Usuario $modeloUsuario;

    public function __construct() {
        $this->modeloUsuario = new Usuario();
    }

    /**
     * Muestra el formulario de inicio de sesión o procesa el POST
     */
    public function login(): void {
        // Si ya está autenticado, redirigir según rol
        if (is_authenticated()) {
            if (is_admin()) {
                redirect('index.php?c=dashboard');
            } else {
                redirect('index.php?c=catalogo');
            }
        }

        $error = null;
        $emailIngresado = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'login') {
            $emailIngresado = trim($_POST['email'] ?? '');
            $password       = $_POST['password'] ?? '';

            if (empty($emailIngresado) || empty($password)) {
                $error = 'Por favor, completa todos los campos requeridos.';
            } else {
                $user = $this->modeloUsuario->autenticar($emailIngresado, $password);

                if ($user) {
                    // Regenerar ID de sesión para prevenir Session Fixation
                    session_regenerate_id(true);

                    // Guardar datos en la sesión
                    $_SESSION['usuario_id']       = $user['id'];
                    $_SESSION['usuario_nombre']   = $user['nombre'];
                    $_SESSION['usuario_username'] = $user['usuario'];
                    $_SESSION['usuario_email']    = $user['email'];
                    $_SESSION['usuario_rol']      = $user['rol'] ?? 'cliente';

                    set_flash('success', "¡Bienvenido de nuevo, {$user['nombre']}!");

                    // Si hay productos en el carrito, redirigir al carrito para completar la compra
                    $hayCarrito = !empty($_SESSION['carrito']);

                    if ($user['rol'] === 'administrador') {
                        redirect('index.php?c=dashboard');
                    } elseif ($hayCarrito) {
                        set_flash('success', "¡Bienvenido de nuevo, {$user['nombre']}! Ahora puedes completar tu compra.");
                        redirect('index.php?c=carrito');
                    } else {
                        redirect('index.php?c=catalogo');
                    }
                } else {
                    $error = 'Correo o contraseña incorrectos. Verifica tus credenciales.';
                }
            }
        }

        require_once __DIR__ . '/../views/login/login.php';
    }

    /**
     * Procesa el formulario de registro de clientes
     */
    public function registro(): void {
        // Si ya está autenticado, redirigir
        if (is_authenticated()) {
            if (is_admin()) {
                redirect('index.php?c=dashboard');
            } else {
                redirect('index.php');
            }
        }

        $errorRegistro = null;
        $datosRegistro = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'registro') {
            $nombre    = trim($_POST['reg_nombre']    ?? '');
            $email     = trim($_POST['reg_email']     ?? '');
            $telefono  = trim($_POST['reg_telefono']  ?? '');
            $password  = $_POST['reg_password']  ?? '';
            $password2 = $_POST['reg_password2'] ?? '';

            // Guardar para repoblar el formulario en caso de error
            $datosRegistro = [
                'nombre'   => $nombre,
                'email'    => $email,
                'telefono' => $telefono,
            ];

            // Validaciones
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
            } else {
                // Registrar el cliente
                $ok = $this->modeloUsuario->registrar($nombre, $email, $telefono, $password);

                if ($ok) {
                    $nuevoUsuario = $this->modeloUsuario->obtenerPorEmail($email);
                    if ($nuevoUsuario) {
                        session_regenerate_id(true);
                        $_SESSION['usuario_id']       = $nuevoUsuario['id'];
                        $_SESSION['usuario_nombre']   = $nuevoUsuario['nombre'];
                        $_SESSION['usuario_username'] = $nuevoUsuario['usuario'];
                        $_SESSION['usuario_email']    = $nuevoUsuario['email'];
                        $_SESSION['usuario_rol']      = 'cliente';

                        // Si hay productos en el carrito, redirigir al carrito
                        $hayCarrito = !empty($_SESSION['carrito']);
                        
                        if ($hayCarrito) {
                            set_flash('success', "¡Bienvenido a Quesos San Gabriel, {$nuevoUsuario['nombre']}! Ahora puedes completar tu compra.");
                            redirect('index.php?c=carrito');
                        } else {
                            set_flash('success', "¡Bienvenido a Quesos San Gabriel, {$nuevoUsuario['nombre']}! Ya puedes explorar nuestros productos.");
                            redirect('index.php#productos');
                        }
                    } else {
                        set_flash('success', '¡Cuenta creada exitosamente! Ahora puedes iniciar sesión.');
                        redirect('login.php');
                    }
                } else {
                    $errorRegistro = 'Ocurrió un error al crear tu cuenta. Inténtalo nuevamente.';
                }
            }
        }

        // Cargar la vista de registro dentro de views/registro/
        require_once __DIR__ . '/../views/registro/registro.php';
    }

    /**
     * Cierra la sesión activa y limpia los datos (HU-02)
     */
    public function logout(): void {
        enviar_headers_sin_cache();
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        session_start();
        set_flash('info', 'Has cerrado sesión exitosamente.');
        redirect('login.php');
    }

    /**
     * Solicitar enlace para restablecer contraseña (HU-30 Escenario 1 y 2)
     */
    public function recuperar(): void {
        if (is_authenticated()) {
            redirect(is_admin() ? 'index.php?c=dashboard' : 'index.php?c=catalogo');
        }

        $error = null;
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                $error = 'Por favor, ingresa tu correo electrónico.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El formato del correo electrónico no es válido.';
            } else {
                $usuario = $this->modeloUsuario->obtenerPorEmail($email);
                if ($usuario) {
                    $token = bin2hex(random_bytes(32));
                    $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    $this->modeloUsuario->guardarTokenRecuperacion($email, $token, $expiracion);

                    // Enviar correo con PHPMailer
                    enviar_correo_recuperacion($email, $usuario['nombre'], $token);
                }
                
                // Mismo mensaje exista o no el correo (por seguridad)
                set_flash('success', 'Si el correo está registrado, te enviamos un enlace para recuperar tu contraseña.');
                redirect('login.php');
            }
        }

        // Si viene con error por POST, guardamos en flash y volvemos al login con modal
        if (!empty($error)) {
            set_flash('danger', $error);
            redirect('login.php?modal=recuperar');
        }

        redirect('login.php');
    }

    /**
     * Formulario y procesamiento de nueva contraseña con token (HU-30 Escenario 2 y 3)
     */
    public function restablecer(): void {
        if (is_authenticated()) {
            redirect(is_admin() ? 'index.php?c=dashboard' : 'index.php?c=catalogo');
        }

        $token = trim($_GET['token'] ?? $_POST['token'] ?? '');

        if (empty($token)) {
            set_flash('danger', 'El enlace de recuperación es inválido.');
            redirect('login.php');
        }

        $usuario = $this->modeloUsuario->obtenerPorTokenRecuperacion($token);
        if (!$usuario) {
            set_flash('danger', 'El enlace de recuperación ha expirado o ya fue utilizado. Solicita uno nuevo.');
            redirect('login.php');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass1 = $_POST['password'] ?? '';
            $pass2 = $_POST['password2'] ?? '';

            if (empty($pass1) || empty($pass2)) {
                $error = 'Por favor, completa ambos campos de contraseña.';
            } elseif (strlen($pass1) < 6) {
                $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
            } elseif ($pass1 !== $pass2) {
                $error = 'Las contraseñas no coinciden.';
            } else {
                $actualizado = $this->modeloUsuario->actualizarPasswordPorToken($token, $pass1);
                if ($actualizado) {
                    set_flash('success', '¡Contraseña actualizada exitosamente! Ya puedes iniciar sesión con tu nueva clave.');
                    redirect('login.php');
                } else {
                    $error = 'No se pudo actualizar la contraseña. El enlace pudo haber vencido.';
                }
            }
        }

        require_once __DIR__ . '/../views/login/restablecer.php';
    }
}

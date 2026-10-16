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

                    if ($user['rol'] === 'administrador') {
                        redirect('index.php?c=dashboard');
                    } else {
                        redirect('index.php');
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

                        set_flash('success', "¡Bienvenido a Quesos San Gabriel, {$nuevoUsuario['nombre']}! Ya puedes añadir productos a tu carrito y realizar tu compra.");
                        redirect('index.php#productos');
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
     * Cierra la sesión activa y limpia los datos
     */
    public function logout(): void {
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
}

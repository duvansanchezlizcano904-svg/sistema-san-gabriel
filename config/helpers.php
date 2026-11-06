<?php
date_default_timezone_set('America/Bogota');
/**
 * Gabriel Lácteos - Funciones Auxiliares (Helpers)
 * Contiene funciones de utilidad global para seguridad, formateo y sesiones.
 */

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

/**
 * Obtiene la URL base dinámica del proyecto.
 * Funciona perfectamente tanto en Laragon (http://localhost/php%20mi%20proyecto/) como en virtual hosts.
 */
function base_url(string $path = ''): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Obtiene el directorio base del script actual
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    // Si estamos en la raíz del script principal
    $base = rtrim($protocol . $host . $scriptDir, '/');
    
    // Si el script actual está dentro de una subcarpeta, normalizamos
    // Por ejemplo, si scriptDir termina en subcarpetas, lo limpiamos para que apunte a la raíz
    return $base . ($path ? '/' . ltrim($path, '/') : '');
}

/**
 * Redirecciona a una ruta interna del proyecto (protegido contra Open Redirect)
 */
function redirect(string $path): void {
    $trimmed = trim($path);
    // Prevenir Open Redirect: si empieza por //, http://, https:// o tiene esquema extraño, forzar index.php
    if (preg_match('#^(https?:)?//#i', $trimmed) || preg_match('#^[a-z0-9+.-]+:#i', $trimmed) || $trimmed === '') {
        $path = 'index.php';
    }
    header("Location: " . $path);
    exit;
}

/**
 * Limpia y sanitiza datos de entrada para prevenir ataques XSS
 */
function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Establece un mensaje flash en la sesión (tipo: success, danger, warning, info)
 */
function set_flash(string $tipo, string $mensaje): void {
    $_SESSION['flash_message'] = [
        'tipo' => $tipo,
        'mensaje' => $mensaje
    ];
}

/**
 * Obtiene y elimina el mensaje flash actual
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Verifica si el usuario actual ha iniciado sesión
 */
function is_authenticated(): bool {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Envía encabezados HTTP para impedir almacenamiento en caché de páginas protegidas (HU-02)
 */
function enviar_headers_sin_cache(): void {
    if (!headers_sent()) {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: 0");
    }
}

/**
 * Obliga a que el usuario esté autenticado. Si no, redirige a login.php (HU-03)
 */
function require_auth(): void {
    enviar_headers_sin_cache();
    if (!is_authenticated()) {
        set_flash('warning', 'Debes iniciar sesión para acceder al sistema.');
        header("Location: login.php");
        exit;
    }
}

/**
 * Obtiene el rol del usuario actualmente autenticado (administrador o cliente)
 */
function get_user_role(): string {
    return $_SESSION['usuario_rol'] ?? 'cliente';
}

/**
 * Comprueba si el usuario autenticado es Administrador
 */
function is_admin(): bool {
    return is_authenticated() && get_user_role() === 'administrador';
}

/**
 * Comprueba si el usuario autenticado es Cliente
 */
function is_cliente(): bool {
    return is_authenticated() && get_user_role() === 'cliente';
}

/**
 * Restringe el acceso exclusivamente a Administradores
 */
function require_admin(): void {
    require_auth();
    if (!is_admin()) {
        set_flash('danger', 'Acceso denegado: Esta sección es exclusiva para el Administrador.');
        header("Location: index.php?c=catalogo");
        exit;
    }
}

/**
 * Formatea un número como moneda (ej: $ 15.000)
 */
function formatearMoneda(float|int|string $valor): string {
    return '$ ' . number_format((float)$valor, 2, ',', '.');
}

/**
 * Formatea una fecha y hora en formato legible en español
 */
function formatearFecha(string $fecha, bool $conHora = true): string {
    if (empty($fecha)) return '-';
    $timestamp = strtotime($fecha);
    if (!$timestamp) return $fecha;
    
    $formato = $conHora ? 'd/m/Y h:i A' : 'd/m/Y';
    return date($formato, $timestamp);
}

/**
 * Calcula automáticamente el estado y badge del inventario según cantidad y stock mínimo
 */
function calcularEstadoStock(int $cantidad, int $stockMinimo): array {
    if ($cantidad <= 0) {
        return [
            'nombre' => 'Agotado',
            'clase'  => 'badge bg-danger',
            'codigo' => 'agotado'
        ];
    } elseif ($cantidad <= $stockMinimo) {
        return [
            'nombre' => 'Stock Bajo',
            'clase'  => 'badge bg-warning text-dark',
            'codigo' => 'bajo'
        ];
    } else {
        return [
            'nombre' => 'Disponible',
            'clase'  => 'badge bg-success',
            'codigo' => 'disponible'
        ];
    }
}

/**
 * Resuelve la imagen correcta para un producto
 */
if (!function_exists('resolverImagenProducto')) {
    function resolverImagenProducto(array $p): string {
        $mapa = [
            'brie'         => 'brie.jpg',
            'burrata'      => 'burrata.jpg',
            'cheddar'      => 'cheddar.jpg',
            'cuajada'      => 'cuajada.jpg',
            'doble crema'  => 'doble_crema.jpg',
            'doble_crema'  => 'doble_crema.jpg',
            'mozzarella'   => 'mozzarella.jpg',
            'mozarela'     => 'mozzarella.jpg',
            'parmesano'    => 'parmesano.jpg',
            'provolone'    => 'provolone.jpg',
            'quesillo'     => 'quesillo_hoja.jpg',
            'ahumado'      => 'queso_artesanal.jpg',
            'artesanal'    => 'queso_artesanal.jpg',
            'azul'         => 'queso_azul.jpg',
            'cabra'        => 'queso_cabra.jpg',
            'costeño'      => 'queso_costeno.jpg',
            'costeno'      => 'queso_costeno.jpg',
            'fresco'       => 'queso_fresco.jpg',
            'trufa'        => 'queso_trufa.jpg',
            'lac'          => 'lac-1000.png',
        ];
        if (!empty($p['imagen'])) {
            $ruta = 'public/img/productos/' . $p['imagen'];
            if (file_exists(__DIR__ . '/../' . $ruta)) return $ruta;
        }
        $nombreLower = strtolower($p['nombre'] ?? '');
        foreach ($mapa as $clave => $archivo) {
            if (str_contains($nombreLower, $clave)) {
                return 'public/img/productos/' . $archivo;
            }
        }
        return 'public/img/placeholder-queso.png';
    }
}

/**
 * Genera el badge de categoría
 */
if (!function_exists('categoriaBadge')) {
    function categoriaBadge(string $cat): string {
        return "<span class='cat-badge'>" . htmlspecialchars(strtoupper($cat)) . "</span>";
    }
}
/**
 * Envía un correo electrónico usando PHPMailer
 * 
 * @param string $destinatario Email del destinatario
 * @param string $nombreDest   Nombre del destinatario
 * @param string $asunto       Asunto del correo
 * @param string $cuerpoHtml   Cuerpo del correo en HTML
 * @return bool true si se envió correctamente, false en caso de error
 */
if (!function_exists('enviar_correo')) {
    function enviar_correo(string $destinatario, string $nombreDest, string $asunto, string $cuerpoHtml): bool {
        static $config = null;
        
        if ($config === null) {
            // Cargar configuración
            $mailConfig = require __DIR__ . '/mail.php';
            
            // Cargar PHPMailer
            require_once __DIR__ . '/../vendor/autoload.php';
            
            $config = [
                'host'     => $mailConfig['SMTP_HOST'],
                'port'     => $mailConfig['SMTP_PORT'],
                'username' => $mailConfig['SMTP_USERNAME'],
                'password' => $mailConfig['SMTP_PASSWORD'],
                'secure'   => $mailConfig['SMTP_SECURE'],
                'fromEmail'=> $mailConfig['FROM_EMAIL'],
                'fromName' => $mailConfig['FROM_NAME'],
                'debug'    => $mailConfig['DEBUG'],
            ];
        }
        
        // Modo debug: mostrar en pantalla en lugar de enviar
        if ($config['debug']) {
            $_SESSION['_debug_correo'] = [
                'to'      => $destinatario,
                'name'    => $nombreDest,
                'subject' => $asunto,
                'body'    => $cuerpoHtml,
            ];
            error_log("📧 [MAIL DEBUG] Enviando a: $destinatario - Asunto: $asunto");
            return true;
        }
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['username'];
            $mail->Password   = $config['password'];
            $mail->SMTPSecure = $config['secure'];
            $mail->Port       = $config['port'];
            $mail->CharSet    = 'UTF-8';
            $mail->WordWrap   = 78;
            
            // Remitente
            $mail->setFrom($config['fromEmail'], $config['fromName']);
            
            // Destinatario
            $mail->addAddress($destinatario, $nombreDest);
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpoHtml;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $cuerpoHtml));
            
            $mail->send();
            error_log("📧 Correo enviado correctamente a: $destinatario");
            return true;
            
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log("📧 Error al enviar correo a $destinatario: " . $mail->ErrorInfo);
            return false;
        }
    }
}

/**
 * Envía el correo de recuperación de contraseña
 * 
 * @param string $email Email del usuario
 * @param string $nombre Nombre del usuario
 * @param string $token Token de recuperación
 * @return bool true si se envió correctamente
 */
if (!function_exists('enviar_correo_recuperacion')) {
    function enviar_correo_recuperacion(string $email, string $nombre, string $token): bool {
        $enlace = base_url("login.php?a=restablecer&token=" . urlencode($token));
        
        $asunto = "🔐 Recuperación de contraseña - Quesos San Gabriel";
        
        $cuerpo = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .container { background: #f9f9f9; border-radius: 10px; padding: 30px; }
                .logo { text-align: center; margin-bottom: 20px; }
                .logo h1 { color: #d4a373; margin: 0; }
                .content { background: white; padding: 20px; border-radius: 8px; }
                .button { display: inline-block; background: #d4a373; color: white !important; 
                          text-decoration: none; padding: 12px 24px; border-radius: 5px; 
                          margin: 20px 0; font-weight: bold; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='logo'>
                    <h1>🧀 Quesos San Gabriel</h1>
                </div>
                <div class='content'>
                    <h2>Hola, {$nombre}</h2>
                    <p>Hemos recibido una solicitud para recuperar tu contraseña.</p>
                    <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
                    <p style='text-align: center;'>
                        <a href='{$enlace}' class='button'>Restablecer mi contraseña</a>
                    </p>
                    <p>O copia y pega este enlace en tu navegador:</p>
                    <p style='word-break: break-all; font-size: 12px; color: #666;'>{$enlace}</p>
                    <p><strong>Este enlace expires en 1 hora.</strong></p>
                    <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
                </div>
                <div class='footer'>
                    <p>© 2026 Quesos San Gabriel. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return enviar_correo($email, $nombre, $asunto, $cuerpo);
    }
}
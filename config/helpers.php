<?php
/**
 * Gabriel Lácteos - Funciones Auxiliares (Helpers)
 * Contiene funciones de utilidad global para seguridad, formateo y sesiones.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
 * Redirecciona a una ruta interna del proyecto
 */
function redirect(string $path): void {
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
 * Obliga a que el usuario esté autenticado. Si no, redirige a login.php
 */
function require_auth(): void {
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

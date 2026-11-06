<?php
/**
 * Quesos San Gabriel — Punto de entrada para Inicio de Sesión y Recuperación de Contraseña
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController();
$accion = sanitize($_GET['a'] ?? 'login');

if ($accion === 'recuperar') {
    $auth->recuperar();
} elseif ($accion === 'restablecer') {
    $auth->restablecer();
} else {
    $auth->login();
}

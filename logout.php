<?php
/**
 * Acceso directo para Cerrar Sesión
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController();
$auth->logout();

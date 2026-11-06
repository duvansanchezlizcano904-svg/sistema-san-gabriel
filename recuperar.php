<?php
/**
 * Quesos San Gabriel — Punto de entrada directo para Recuperar Contraseña
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController();
$auth->recuperar();


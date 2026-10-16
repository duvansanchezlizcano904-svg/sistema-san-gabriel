<?php
/**
 * Quesos San Gabriel — Punto de entrada para Registro de Clientes
 * Procesa el registro o muestra la vista con el modal de registro.
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController();
$auth->registro();

<?php
/**
 * Quesos San Gabriel — Punto de entrada para Registro de Clientes
 * Despacha al AuthController para mostrar y procesar el formulario de registro.
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController();
$auth->registro();

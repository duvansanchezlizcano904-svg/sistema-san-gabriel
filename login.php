<?php
/**
 * Quesos San Gabriel — Punto de entrada para Inicio de Sesión
 * Permite acceder a la vista de login y procesar la autenticación de usuarios.
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController();
$auth->login();

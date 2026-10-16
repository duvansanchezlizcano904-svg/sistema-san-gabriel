<?php
/**
 * Quesos San Gabriel — Página Pública (sin login requerido)
 * Entry point: publico.php
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/controllers/PublicoController.php';

$ctrl = new PublicoController();
$ctrl->index();

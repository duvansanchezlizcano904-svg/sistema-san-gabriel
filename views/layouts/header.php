<?php
/**
 * Layout Header
 */
require_once __DIR__ . '/../../config/helpers.php';
$titulo = $tituloPagina ?? 'Quesos San Gabriel — Panel de Gestión';
$activo = $menuActivo ?? '';
$usuarioActual = $_SESSION['usuario_nombre'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app-wrapper">
    <!-- Barra Lateral (Sidebar) -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <!-- Contenido Principal -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar no-print">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle" type="button" aria-label="Abrir Menú">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-sm-block">
                    <span class="text-muted small">Quesos San Gabriel — Sistema de Gestión</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <?php if (is_admin()): ?>
                    <a href="index.php?c=ventas" class="btn btn-sm btn-primary d-none d-md-inline-flex align-items-center gap-2">
                        <i class="bi bi-cart-plus"></i> Nueva Venta
                    </a>
                <?php endif; ?>

                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle text-primary fs-5"></i>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($usuarioActual) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><h6 class="dropdown-header">Rol: <?= is_admin() ? 'Administrador' : 'Cliente' ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger d-flex align-items-center gap-2" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Cuerpo del contenido -->
        <main class="content-body">
            <!-- Mensajes Flash de notificación -->
            <?php $flash = get_flash(); if ($flash): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['tipo']) ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($flash['tipo'] === 'success'): ?>
                            <i class="bi bi-check-circle-fill fs-5"></i>
                        <?php elseif ($flash['tipo'] === 'danger'): ?>
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <?php elseif ($flash['tipo'] === 'warning'): ?>
                            <i class="bi bi-exclamation-circle-fill fs-5"></i>
                        <?php else: ?>
                            <i class="bi bi-info-circle-fill fs-5"></i>
                        <?php endif; ?>
                        <div><?= $flash['mensaje'] ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

<?php
/**
 * Sidebar — Quesos San Gabriel
 */
$activo      = $menuActivo ?? '';
$carrito     = $_SESSION['carrito'] ?? [];
$carritoItems = array_sum(array_column($carrito, 'cantidad'));
?>
<aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <a href="<?= is_admin() ? 'index.php?c=dashboard' : 'publico.php' ?>" class="sidebar-brand">
        <div class="brand-icon">🧀</div>
        <div>
            <div class="lh-1">Quesos San Gabriel</div>
            <small style="font-size:0.63rem;color:#94a3b8;font-weight:500;">Quesería Artesanal</small>
        </div>
    </a>

    <ul class="sidebar-nav">
        <?php if (is_admin()): ?>
            <!-- ── ADMIN ── -->
            <li class="nav-heading">Panel Admin</li>

            <li class="nav-item">
                <a class="nav-link <?= $activo==='dashboard' ? 'active' : '' ?>" href="index.php?c=dashboard">
                    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activo==='productos' ? 'active' : '' ?>" href="index.php?c=productos">
                    <i class="bi bi-box-seam"></i><span>Productos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activo==='inventario' ? 'active' : '' ?>" href="index.php?c=inventario">
                    <i class="bi bi-clipboard-data"></i><span>Inventario</span>
                </a>
            </li>

            <li class="nav-heading mt-3">Comercial</li>

            <li class="nav-item">
                <a class="nav-link <?= $activo==='ventas' ? 'active' : '' ?>" href="index.php?c=ventas">
                    <i class="bi bi-cart-check"></i><span>Ventas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activo==='historial' ? 'active' : '' ?>" href="index.php?c=historial">
                    <i class="bi bi-receipt"></i><span>Historial</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activo==='proveedores' ? 'active' : '' ?>" href="index.php?c=proveedores">
                    <i class="bi bi-truck"></i><span>Proveedores</span>
                </a>
            </li>

            <li class="nav-heading mt-3">Análisis</li>

            <li class="nav-item">
                <a class="nav-link <?= $activo==='reportes' ? 'active' : '' ?>" href="index.php?c=reportes">
                    <i class="bi bi-file-earmark-bar-graph"></i><span>Reportes</span>
                </a>
            </li>

            <li class="nav-heading mt-3">Tienda</li>

            <li class="nav-item">
                <a class="nav-link <?= $activo==='catalogo' ? 'active' : '' ?>" href="index.php?c=catalogo">
                    <i class="bi bi-shop"></i><span>Catálogo (Admin)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="publico.php" target="_blank">
                    <i class="bi bi-globe"></i><span>Ver tienda pública</span>
                </a>
            </li>

        <?php else: ?>
            <!-- ── CLIENTE ── -->
            <li class="nav-heading">Tienda</li>

            <li class="nav-item">
                <a class="nav-link <?= $activo==='catalogo' ? 'active' : '' ?>" href="publico.php">
                    <i class="bi bi-shop"></i><span>Catálogo de Quesos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activo==='carrito' ? 'active' : '' ?>" href="index.php?c=carrito">
                    <i class="bi bi-cart3"></i>
                    <span>Mi Carrito</span>
                    <?php if ($carritoItems > 0): ?>
                        <span class="carrito-count"><?= $carritoItems ?></span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-heading mt-3">Cuenta</li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">
                <i class="bi bi-box-arrow-right text-danger"></i><span>Cerrar sesión</span>
            </a>
        </li>
    </ul>

    <!-- Footer sidebar -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                <i class="bi <?= is_admin() ? 'bi-shield-lock' : 'bi-person' ?>"></i>
            </div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?></div>
                <div class="user-role"><?= is_admin() ? 'Administrador' : 'Cliente' ?></div>
            </div>
        </div>
    </div>
</aside>

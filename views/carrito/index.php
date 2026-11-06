<?php
/**
 * Vista del Carrito de Compras — Quesos San Gabriel
 * Diseño limpio, moderno y comercial respetando la identidad visual del proyecto.
 */

$errorRegistro  = $errorRegistro ?? null;
$error          = $error ?? '';
$emailIngresado = $emailIngresado ?? '';
$datosRegistro  = $datosRegistro ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito de Compras — Quesos San Gabriel</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Estilos del sitio -->
    <link href="public/css/publico.css" rel="stylesheet">
    <link href="public/css/home.css" rel="stylesheet">
    <link href="public/css/login.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
        .cart-page-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            padding: 3rem 0 2rem;
            margin-bottom: 2.5rem;
            border-bottom: 3px solid #f59e0b;
        }
        .cart-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .cart-item-row {
            transition: background-color 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem;
        }
        .cart-item-row:last-child {
            border-bottom: none;
        }
        .cart-item-row:hover {
            background-color: #f8fafc;
        }
        .cart-prod-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
            flex-shrink: 0;
        }
        .cart-qty-wrapper {
            display: inline-flex;
            align-items: center;
            border: 1px solid #cbd5e1;
            border-radius: 50px;
            background: #ffffff;
            padding: 2px 4px;
        }
        .cart-qty-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            color: #334155;
            font-weight: 700;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.15s;
        }
        .cart-qty-btn:hover:not(:disabled) {
            background: #e2e8f0;
            color: #0f172a;
        }
        .cart-qty-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }
        .cart-qty-val {
            width: 36px;
            text-align: center;
            font-weight: 700;
            color: #0f172a;
            font-size: 0.95rem;
        }
        .cart-summary-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 25px -4px rgba(37, 99, 235, 0.1);
            position: sticky;
            top: 90px;
        }
        .btn-checkout-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            color: #ffffff;
            font-weight: 700;
            border-radius: 50px;
            padding: 0.85rem 1.75rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }
        .btn-checkout-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
        .btn-continue-shopping {
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            color: #475569;
            border: 1px solid #cbd5e1;
            transition: all 0.2s;
        }
        .btn-continue-shopping:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }
        .cart-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1080;
            min-width: 300px;
        }
    </style>
</head>
<body>

<!-- =============================================
     NAVBAR (IDÉNTICO AL ENCABEZADO ACTUAL)
     ============================================= -->
<nav class="home-navbar" id="homeNavbar" style="position: sticky; top:0; z-index: 1030; background: #0f172a; border-bottom: 1px solid #334155;">
    <div class="home-container">
        <div class="home-nav-inner">

            <!-- Logo -->
            <a href="index.php" class="home-brand">
                <span class="home-brand-emoji">🧀</span>
                <div>
                    <div class="home-brand-name">Quesos San Gabriel</div>
                    <div class="home-brand-sub">Quesería Artesanal</div>
                </div>
            </a>

            <!-- Links escritorio -->
            <ul class="home-nav-links">
                <li><a href="index.php" class="home-nav-link">Inicio</a></li>
                <li><a href="index.php#productos" class="home-nav-link">Productos</a></li>
                <li><a href="index.php#nosotros" class="home-nav-link">Nosotros</a></li>

                <?php if ($estaAutenticado && !$esAdmin): ?>
                    <li>
                        <span class="home-nav-user text-white">
                            <i class="bi bi-person-circle text-info me-1"></i>
                            Hola, <?= htmlspecialchars(explode(' ', $usuarioNombre)[0] ?: 'Cliente') ?>
                        </span>
                    </li>
                    <li>
                        <a href="index.php?c=carrito" class="home-nav-cart-btn" style="background:#2563eb; color:#fff; text-decoration:none;">
                            <i class="bi bi-cart3"></i>
                            <span>Carrito</span>
                            <span class="badge bg-warning text-dark ms-1" id="nav-cart-count"><?= (int)$totalUnidades ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="home-nav-link text-danger">
                            <i class="bi bi-box-arrow-right me-1"></i>Salir
                        </a>
                    </li>
                <?php elseif ($estaAutenticado && $esAdmin): ?>
                    <li>
                        <a href="index.php?c=dashboard" class="home-nav-btn" style="background:#2563eb; text-decoration:none;">
                            <i class="bi bi-speedometer2 me-1"></i>Panel Admin
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="home-nav-link">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="index.php?c=carrito" class="home-nav-cart-btn" style="background:#2563eb; color:#fff; text-decoration:none;">
                            <i class="bi bi-cart3"></i>
                            <span>Carrito</span>
                            <span class="badge bg-warning text-dark ms-1" id="nav-cart-count"><?= (int)$totalUnidades ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="login.php" class="home-nav-btn" style="text-decoration:none;">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
                        </a>
                    </li>
                    <li>
                        <a href="registro.php" class="home-nav-btn home-nav-btn-registro" style="text-decoration:none;">
                            <i class="bi bi-person-plus me-1"></i>Registrarse
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

        </div>
    </div>
</nav>

<!-- Encabezado de la página -->
<div class="cart-page-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark fw-bold px-3 py-1 text-uppercase mb-2">Tienda en Línea</span>
                <h1 class="h2 fw-bold text-white mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-cart3 text-warning"></i> Mi Carrito de Compras
                </h1>
                <p class="text-slate-300 text-white-50 mb-0 small">
                    Revisa y personaliza tu pedido de quesos artesanales antes de continuar
                </p>
            </div>
            <div>
                <a href="index.php#productos" class="btn btn-outline-light rounded-pill px-4 btn-sm d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Seguir explorando quesos
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Contenedor Principal -->
<div class="container pb-5">

    <!-- Mensajes Flash -->
    <?php if (!empty($flash)): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['tipo']) ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi <?= $flash['tipo'] === 'success' ? 'bi-check-circle-fill' : 'bi-info-circle-fill' ?> fs-5"></i>
            <div><?= $flash['mensaje'] ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php endif; ?>

    <?php if (empty($carrito)): ?>
        <!-- =============================================
             ESTADO CARRITO VACÍO
             ============================================= -->
        <div class="cart-card p-5 text-center my-4">
            <div style="font-size: 4.5rem;" class="mb-3">🧀</div>
            <h3 class="fw-bold text-dark mb-2">Tu carrito está vacío</h3>
            <p class="text-muted mb-4 fs-5" style="max-width: 520px; margin: 0 auto;">
                Tu carrito está vacío. Explora nuestros quesos y encuentra tus favoritos.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="index.php#productos" class="btn btn-checkout-primary px-5 py-3 fs-6">
                    <i class="bi bi-grid-3x3-gap me-2"></i> Explorar Catálogo de Quesos
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- =============================================
             CARRITO CON PRODUCTOS
             ============================================= -->
        <div class="row g-4">

            <!-- Columna Izquierda: Listado de Productos -->
            <div class="col-12 col-lg-8">
                <div class="cart-card">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="fw-bold text-dark text-uppercase small">
                            <i class="bi bi-box-seam me-1 text-primary"></i>
                            Productos seleccionados (<span id="cart-header-count"><?= $totalUnidades ?></span> unidades)
                        </span>
                        <form method="POST" action="index.php?c=carrito&a=vaciar" onsubmit="return confirm('¿Seguro que deseas vaciar todos los productos del carrito?');">
                            <input type="hidden" name="redirect" value="index.php?c=carrito">
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1">
                                <i class="bi bi-trash3 me-1"></i> Vaciar Carrito
                            </button>
                        </form>
                    </div>

                    <div id="cart-items-container">
                        <?php foreach ($carrito as $id => $item): ?>
                        <div class="cart-item-row" id="cart-row-<?= $id ?>">
                            <div class="d-flex align-items-center gap-3 flex-wrap flex-md-nowrap">

                                <!-- Imagen del queso -->
                                <img src="<?= htmlspecialchars($item['imagen']) ?>"
                                     alt="<?= htmlspecialchars($item['nombre']) ?>"
                                     class="cart-prod-img"
                                     onerror="this.src='public/img/placeholder-queso.png'">

                                <!-- Info del Queso -->
                                <div class="flex-grow-1 min-w-0">
                                    <h5 class="fw-bold text-dark mb-1 text-truncate">
                                        <?= htmlspecialchars($item['nombre']) ?>
                                    </h5>
                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-1">
                                        <?php if (!empty($item['categoria'])): ?>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">
                                                <?= htmlspecialchars($item['categoria']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="badge bg-light text-secondary border small">
                                            <?= htmlspecialchars($item['peso_presentacion'] ?? $item['unidad_medida']) ?>
                                        </span>
                                    </div>
                                    <div class="small text-muted">
                                        Precio unitario: <strong>$ <?= number_format($item['precio_unitario'], 0, ',', '.') ?></strong>
                                        <span class="mx-1">·</span>
                                        <span class="text-success"><i class="bi bi-check2-circle me-1"></i>Stock: <?= $item['stock_real'] ?> disponibles</span>
                                    </div>
                                </div>

                                <!-- Selector de Cantidad -->
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cart-qty-wrapper">
                                        <button type="button" class="cart-qty-btn btn-qty-minus"
                                                onclick="actualizarCantidadAjax(<?= $id ?>, -1, <?= $item['stock_real'] ?>)"
                                                <?= $item['cantidad'] <= 1 ? 'disabled' : '' ?>>
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <span class="cart-qty-val" id="qty-val-<?= $id ?>"><?= $item['cantidad'] ?></span>
                                        <button type="button" class="cart-qty-btn btn-qty-plus"
                                                onclick="actualizarCantidadAjax(<?= $id ?>, 1, <?= $item['stock_real'] ?>)"
                                                <?= $item['cantidad'] >= $item['stock_real'] ? 'disabled' : '' ?>>
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Subtotal por producto -->
                                <div class="text-end" style="min-width: 110px;">
                                    <span class="text-muted d-block" style="font-size:0.75rem;">Subtotal</span>
                                    <span class="fw-bold text-primary fs-5" id="subtotal-val-<?= $id ?>">
                                        $ <?= number_format($item['subtotal'], 0, ',', '.') ?>
                                    </span>
                                </div>

                                <!-- Botón Eliminar -->
                                <div>
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm rounded-circle p-2"
                                            title="Eliminar producto del carrito"
                                            onclick="eliminarItemAjax(<?= $id ?>, '<?= htmlspecialchars(addslashes($item['nombre'])) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <a href="index.php#productos" class="btn btn-continue-shopping d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i> Seguir comprando
                    </a>
                </div>
            </div>

            <!-- Columna Derecha: Resumen de Compra -->
            <div class="col-12 col-lg-4">
                <div class="cart-summary-card p-4">
                    <h4 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                        <i class="bi bi-receipt-cutoff text-primary"></i> Resumen de Compra
                    </h4>

                    <!-- Desglose -->
                    <div class="d-flex justify-content-between text-muted mb-2">
                        <span>Cantidad total de unidades:</span>
                        <strong class="text-dark" id="summary-total-units"><?= $totalUnidades ?></strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted mb-2">
                        <span>Subtotal de la compra:</span>
                        <strong class="text-dark" id="summary-subtotal">$ <?= number_format($totalMonto, 0, ',', '.') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted mb-3 pb-3 border-bottom">
                        <span>Costos de envío:</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">Gratis</span>
                    </div>

                    <!-- Total final a pagar -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="text-muted d-block small">Total final a pagar</span>
                            <span class="fs-6 fw-semibold text-secondary">IVA incluido</span>
                        </div>
                        <div class="text-end">
                            <span class="fs-2 fw-extrabold text-primary fw-bold" id="summary-total-final">
                                $ <?= number_format($totalMonto, 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Botón Continuar con la compra -->
                    <div class="d-grid gap-2">
                        <?php if ($estaAutenticado && !$esAdmin): ?>
                            <!-- Cliente autenticado: pasa directo al resumen y confirmación de entrega -->
                            <button type="button" class="btn btn-checkout-primary py-3 fs-5" data-bs-toggle="modal" data-bs-target="#modalConfirmarPedido">
                                <i class="bi bi-check-circle-fill me-2"></i> Continuar con la compra
                            </button>
                        <?php elseif ($estaAutenticado && $esAdmin): ?>
                            <!-- Administrador -->
                            <div class="alert alert-warning small mb-0">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Has iniciado sesión como <strong>Administrador</strong>. Para realizar compras como cliente debes iniciar sesión con una cuenta de cliente.
                            </div>
                        <?php else: ?>
                            <!-- Cliente no autenticado: solicitar registrarse o iniciar sesión -->
                            <button type="button" class="btn btn-checkout-primary py-3 fs-5" data-bs-toggle="modal" data-bs-target="#modalLogin">
                                <i class="bi bi-shield-lock me-2"></i> Continuar con la compra
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 p-3 rounded-3 bg-light border">
                        <div class="d-flex gap-2 align-items-center text-muted small mb-2">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                            <span>Compra 100% segura y garantizada</span>
                        </div>
                        <div class="d-flex gap-2 align-items-center text-muted small">
                            <i class="bi bi-truck text-primary fs-5"></i>
                            <span>Despacho directo desde quesería artesanal</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    <?php endif; ?>

</div>

<!-- ============================================================
     MODAL DE LOGIN
     ============================================================ -->
<div class="modal fade modal-login" id="modalLogin" tabindex="-1"
     aria-labelledby="modalLoginLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                <div class="modal-logo">
                    <i class="bi bi-droplet-fill"></i>
                </div>
                <h5 class="modal-title" id="modalLoginLabel">Bienvenido</h5>
                <p class="modal-subtitle">Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">

                <?php if ($flash && !$errorRegistro): ?>
                    <div class="alert alert-<?= htmlspecialchars($flash['tipo']) ?> alert-dismissible fade show py-2 mb-3" role="alert">
                        <small><?= $flash['mensaje'] ?></small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <small><?= htmlspecialchars($error) ?></small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" autocomplete="off">
                    <input type="hidden" name="_action" value="login">

                    <div class="mb-3">
                        <label for="email" class="form-label">Usuario o correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 ps-1"
                                   id="email"
                                   name="email"
                                   placeholder="ej. admin o correo@ejemplo.com"
                                   required
                                   value="<?= htmlspecialchars($emailIngresado) ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">Contraseña</label>
                            <a href="login.php?modal=recuperar" class="small text-decoration-none text-muted" id="link-olvido-pass">¿Olvidaste tu contraseña?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password"
                                   class="form-control border-start-0 ps-1"
                                   id="password"
                                   name="password"
                                   placeholder="••••••••"
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn-modal-submit" id="btn-submit-login">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </button>

                </form>

                <p class="modal-switch-text">
                    ¿No tienes cuenta?
                    <a href="#" id="link-ir-registro"
                       data-bs-toggle="modal"
                       data-bs-target="#modalRegistro">Regístrate aquí</a>
                </p>

                <p class="modal-footer-text">
                    &copy; <?= date('Y') ?> San Gabriel — Todos los derechos reservados.
                </p>

            </div><!-- /.modal-body -->

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /#modalLogin -->

<!-- ============================================================
     MODAL DE REGISTRO
     ============================================================ -->
<div class="modal fade modal-login modal-registro" id="modalRegistro" tabindex="-1"
     aria-labelledby="modalRegistroLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header modal-header-registro">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                <div class="modal-logo">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h5 class="modal-title" id="modalRegistroLabel">Crear Cuenta</h5>
                <p class="modal-subtitle">Regístrate como cliente de Quesos San Gabriel</p>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">
                <form action="registro.php" method="POST" autocomplete="off">
                    <input type="hidden" name="_action" value="registro">

                    <div class="mb-3">
                        <label for="reg_nombre" class="form-label">Nombre completo</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_nombre"
                                   name="reg_nombre"
                                   placeholder="Tu nombre completo"
                                   required
                                   value="<?= htmlspecialchars($datosRegistro['nombre'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reg_email" class="form-label">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_email"
                                   name="reg_email"
                                   placeholder="correo@ejemplo.com"
                                   required
                                   value="<?= htmlspecialchars($datosRegistro['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reg_telefono" class="form-label">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="tel"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_telefono"
                                   name="reg_telefono"
                                   placeholder="310 000 0000"
                                   required
                                   value="<?= htmlspecialchars($datosRegistro['telefono'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reg_password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_password"
                                   name="reg_password"
                                   placeholder="Mínimo 6 caracteres"
                                   required
                                   minlength="6">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reg_password2" class="form-label">Confirmar contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_password2"
                                   name="reg_password2"
                                   placeholder="Repite tu contraseña"
                                   required
                                   minlength="6">
                        </div>
                    </div>

                    <button type="submit" class="btn-modal-submit btn-modal-submit-registro" id="btn-submit-registro">
                        <i class="bi bi-person-check"></i> Crear Cuenta y Continuar
                    </button>
                </form>

                <p class="modal-switch-text">
                    ¿Ya tienes cuenta?
                    <a href="#" id="link-ir-login"
                       data-bs-toggle="modal"
                       data-bs-target="#modalLogin">Inicia sesión aquí</a>
                </p>

                <p class="modal-footer-text">
                    &copy; <?= date('Y') ?> San Gabriel — Todos los derechos reservados.
                </p>

            </div><!-- /.modal-body -->

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /#modalRegistro -->

<!-- =============================================
     MODAL CASO B: CLIENTE AUTENTICADO
     Confirmación final del pedido y detalles de entrega
     ============================================= -->
<?php if ($estaAutenticado && !$esAdmin): ?>
<div class="modal fade" id="modalConfirmarPedido" tabindex="-1" aria-labelledby="modalConfirmarPedidoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size: 2rem;">🧀</span>
                    <div>
                        <h5 class="modal-title fw-bold" id="modalConfirmarPedidoLabel">Confirmar y Finalizar Pedido</h5>
                        <small class="text-white-50">Cliente: <?= htmlspecialchars($usuarioNombre) ?> (<?= htmlspecialchars($usuarioEmail) ?>)</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <form method="POST" action="index.php?c=carrito&a=confirmar" id="formConfirmarPedido">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-dark text-uppercase small mb-3">
                        <i class="bi bi-card-checklist me-1 text-primary"></i> Resumen de los productos a pedir:
                    </h6>

                    <div class="table-responsive rounded border mb-3">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carrito as $item): ?>
                                <tr>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($item['nombre']) ?></td>
                                    <td class="text-center fw-bold"><?= $item['cantidad'] ?></td>
                                    <td class="text-end text-muted">$ <?= number_format($item['precio_unitario'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold text-primary">$ <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end fw-bold">TOTAL FINAL:</th>
                                    <th class="text-end fw-bold text-success fs-5">$ <?= number_format($totalMonto, 0, ',', '.') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label for="notas_pedido" class="form-label fw-bold text-dark small">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> Dirección de entrega / Indicaciones (Opcional):
                        </label>
                        <textarea class="form-control" id="notas_pedido" name="notas" rows="3"
                                  placeholder="Ej: Calle 45 # 12-34 apto 502, dejar en portería..."></textarea>
                        <div class="form-text">Tu pedido quedará registrado en estado <strong>Pendiente</strong> y el inventario se actualizará de inmediato.</div>
                    </div>
                </div>

                <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Modificar Carrito
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold d-flex align-items-center gap-2" id="btnSubmitPedido">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <span>Confirmar Compra</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Toast de Notificaciones -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
    <div id="cartActionToast" class="toast align-items-center text-bg-dark border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="toastMessage">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <span>Operación realizada exitosamente.</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>

<!-- Bootstrap 5.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/**
 * Mostrar Toast flotante informativo
 */
function mostrarToast(mensaje, esExito = true) {
    const toastEl = document.getElementById('cartActionToast');
    const toastMsg = document.getElementById('toastMessage');
    if (!toastEl || !toastMsg) return;

    toastMsg.innerHTML = `<i class="bi ${esExito ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning'} fs-5"></i> <span>${mensaje}</span>`;
    const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
    toast.show();
}

/**
 * Formatear número como moneda colombiana ($ 15.000)
 */
function formatearPesos(valor) {
    return '$ ' + new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(valor);
}

/**
 * Actualizar cantidad de un producto con AJAX
 */
function actualizarCantidadAjax(productoId, cambio, stockMax) {
    const qtySpan = document.getElementById('qty-val-' + productoId);
    if (!qtySpan) return;

    let cantActual = parseInt(qtySpan.textContent) || 1;
    let cantNueva  = cantActual + cambio;

    if (cantNueva < 1) cantNueva = 1;
    if (cantNueva > stockMax) cantNueva = stockMax;

    if (cantNueva === cantActual) return;

    // Deshabilitar temporalmente botones
    const row = document.getElementById('cart-row-' + productoId);
    const btnMinus = row.querySelector('.btn-qty-minus');
    const btnPlus  = row.querySelector('.btn-qty-plus');
    if (btnMinus) btnMinus.disabled = true;
    if (btnPlus) btnPlus.disabled = true;

    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('producto_id', productoId);
    formData.append('cantidad', cantNueva);

    fetch('index.php?c=carrito&a=actualizar', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            qtySpan.textContent = data.cantidad;
            
            const subtotalEl = document.getElementById('subtotal-val-' + productoId);
            if (subtotalEl) {
                subtotalEl.textContent = formatearPesos(data.subtotal);
            }

            // Actualizar resumen
            actualizarResumenTotales(data.total_items, data.total_monto);

            // Reactivar botones según límites
            if (btnMinus) btnMinus.disabled = data.cantidad <= 1;
            if (btnPlus) btnPlus.disabled = data.cantidad >= stockMax;

            mostrarToast('Cantidad actualizada a ' + data.cantidad + ' unidades.', true);
        } else {
            mostrarToast(data.mensaje || 'No fue posible actualizar la cantidad.', false);
            if (btnMinus) btnMinus.disabled = cantActual <= 1;
            if (btnPlus) btnPlus.disabled = cantActual >= stockMax;
        }
    })
    .catch(err => {
        console.error('Error al actualizar:', err);
        window.location.reload();
    });
}

/**
 * Eliminar un producto del carrito con AJAX
 */
function eliminarItemAjax(productoId, nombreProducto) {
    if (!confirm('¿Deseas eliminar "' + nombreProducto + '" de tu carrito?')) {
        return;
    }

    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('producto_id', productoId);

    fetch('index.php?c=carrito&a=quitar', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById('cart-row-' + productoId);
            if (row) {
                row.remove();
            }

            actualizarResumenTotales(data.total_items, data.total_monto);
            mostrarToast('"' + nombreProducto + '" eliminado del carrito.', true);

            if (data.total_items <= 0) {
                window.location.reload();
            }
        }
    })
    .catch(err => {
        console.error('Error al eliminar:', err);
        window.location.reload();
    });
}

/**
 * Actualizar los elementos visuales del resumen y el navbar
 */
function actualizarResumenTotales(totalItems, totalMonto) {
    const navCartBadge = document.getElementById('nav-cart-count');
    if (navCartBadge) navCartBadge.textContent = totalItems;

    const headerCount = document.getElementById('cart-header-count');
    if (headerCount) headerCount.textContent = totalItems;

    const summaryUnits = document.getElementById('summary-total-units');
    if (summaryUnits) summaryUnits.textContent = totalItems;

    const summarySubtotal = document.getElementById('summary-subtotal');
    if (summarySubtotal) summarySubtotal.textContent = formatearPesos(totalMonto);

    const summaryFinal = document.getElementById('summary-total-final');
    if (summaryFinal) summaryFinal.textContent = formatearPesos(totalMonto);
}

// Prevenir múltiples envíos al confirmar compra
const formConfirmar = document.getElementById('formConfirmarPedido');
if (formConfirmar) {
    formConfirmar.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitPedido');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Procesando compra...';
        }
    });
}

// Alternar entre modalLogin y modalRegistro sin superposición de backdrops
function switchModal(closeId, openId) {
    const closingEl = document.getElementById(closeId);
    const openingEl = document.getElementById(openId);
    if (!closingEl || !openingEl) return;

    const closingModal = bootstrap.Modal.getInstance(closingEl);
    if (closingModal) {
        closingEl.addEventListener('hidden.bs.modal', function handler() {
            closingEl.removeEventListener('hidden.bs.modal', handler);
            const opModal = bootstrap.Modal.getInstance(openingEl) || new bootstrap.Modal(openingEl);
            opModal.show();
        });
        closingModal.hide();
    } else {
        const opModal = bootstrap.Modal.getInstance(openingEl) || new bootstrap.Modal(openingEl);
        opModal.show();
    }
}

const linkIrReg = document.getElementById('link-ir-registro');
if (linkIrReg) {
    linkIrReg.addEventListener('click', function(e) {
        e.preventDefault();
        switchModal('modalLogin', 'modalRegistro');
    });
}

const linkIrLog = document.getElementById('link-ir-login');
if (linkIrLog) {
    linkIrLog.addEventListener('click', function(e) {
        e.preventDefault();
        switchModal('modalRegistro', 'modalLogin');
    });
}
</script>

</body>
</html>

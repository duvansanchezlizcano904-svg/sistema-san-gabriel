<?php
/**
 * Vista de Inicio de Sesión y Registro
 * Landing page hero + modal de Login + modal de Registro.
 */
require_once __DIR__ . '/../../config/helpers.php';
$flash = get_flash();

// Variables del registro (seteadas si venimos del AuthController::registro())
$errorRegistro  = $errorRegistro  ?? null;
$datosRegistro  = $datosRegistro  ?? [];
$error          = $error          ?? null;
$emailIngresado = $emailIngresado ?? '';

// Determinar qué modal abrir automáticamente
$abrirModalLogin    = !empty($error) || ($flash && !$errorRegistro);
$abrirModalRegistro = !empty($errorRegistro);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quesos San Gabriel — Sistema de Gestión</title>
    <meta name="description" content="Quesos San Gabriel: sistema de gestión de quesería artesanal colombiana. Inicia sesión o regístrate como cliente.">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Estilos del Login -->
    <link href="public/css/login.css" rel="stylesheet">
</head>
<body>

<!-- ============================================================
     LANDING PAGE HERO
     ============================================================ -->
<div class="hero-page">

    <!-- Imagen del queso a la derecha (semitransparente) -->
    <div class="hero-bg">
        <img src="public/img/queso-fondo.jpg"
             alt=""
             onerror="this.src='https://images.unsplash.com/photo-1452195100486-9cc805987862?w=900&q=80'">
    </div>

    <!-- Gradiente de legibilidad -->
    <div class="hero-overlay"></div>

    <!-- ZONA 1: Topbar -->
    <div class="hero-topbar">
        <div class="hero-brand">
            <div class="hero-brand-icon">
                🧀
            </div>
            <div>
                <div style="line-height:1.1">Quesos San Gabriel</div>
                <div style="font-size:.63rem; color:rgba(255,255,255,.45); font-weight:500;">Quesería Artesanal</div>
            </div>
        </div>
    </div>

    <!-- ZONA 2: Contenido central -->
    <div class="hero-center">
        <div class="hero-eyebrow">
            <i class="bi bi-award-fill"></i> Sistema Profesional de Gestión
        </div>
        <h1 class="hero-title">
            Los mejores<br>
            <span class="highlight">Quesos</span><br>
            San Gabriel
        </h1>
        <p class="hero-desc">
            Panel de administración del inventario, ventas y reportes
            de Quesos San Gabriel.
        </p>

        <!-- BOTONES PRINCIPALES: solo Iniciar sesión y Registrarse -->
        <div class="hero-btn-group">
            <button class="btn-hero-login" id="btn-abrir-login"
                    data-bs-toggle="modal" data-bs-target="#modalLogin">
                <i class="bi bi-box-arrow-in-right"></i>
                Iniciar Sesión
            </button>
            <button class="btn-hero-register" id="btn-abrir-registro"
                    data-bs-toggle="modal" data-bs-target="#modalRegistro">
                <i class="bi bi-person-plus"></i>
                Registrarse
            </button>
        </div>
    </div>

    <!-- ZONA 3: Stats en la parte inferior -->
    <div class="hero-bottom">
        <div class="hero-stat-pill">
            <i class="bi bi-box-seam"></i>
            <span><strong>10+</strong> Variedades de queso</span>
        </div>
        <div class="hero-stat-pill">
            <i class="bi bi-cart-check"></i>
            <span><strong>100%</strong> Control de ventas</span>
        </div>
        <div class="hero-stat-pill">
            <i class="bi bi-shield-check"></i>
            <span><strong>Seguro</strong> y confiable</span>
        </div>
    </div>

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
                        <label for="email" class="form-label">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email"
                                   class="form-control border-start-0 ps-1"
                                   id="email"
                                   name="email"
                                   placeholder="correo@ejemplo.com"
                                   required
                                   value="<?= htmlspecialchars($emailIngresado) ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
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

                <?php if (!empty($errorRegistro)): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <small><?= htmlspecialchars($errorRegistro) ?></small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($flash && $errorRegistro === null && !empty($flash)): ?>
                    <!-- El flash de éxito se muestra en el modal de login -->
                <?php endif; ?>

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
                        <div class="password-match-msg" id="msg-password-match"></div>
                    </div>

                    <button type="submit" class="btn-modal-submit btn-modal-submit-registro" id="btn-submit-registro">
                        <i class="bi bi-person-check"></i> Crear Cuenta
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


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Reapertura automática de modal según contexto ──────────────────
    <?php if ($abrirModalLogin): ?>
    new bootstrap.Modal(document.getElementById('modalLogin')).show();
    <?php elseif ($abrirModalRegistro): ?>
    new bootstrap.Modal(document.getElementById('modalRegistro')).show();
    <?php endif; ?>

    // ── Cierra un modal antes de abrir el otro (evita overlay doble) ───
    function switchModal(closeId, openId) {
        var closingEl = document.getElementById(closeId);
        var openingEl = document.getElementById(openId);
        var closingModal = bootstrap.Modal.getInstance(closingEl);
        if (closingModal) {
            closingEl.addEventListener('hidden.bs.modal', function handler() {
                closingEl.removeEventListener('hidden.bs.modal', handler);
                new bootstrap.Modal(openingEl).show();
            });
            closingModal.hide();
        } else {
            new bootstrap.Modal(openingEl).show();
        }
    }

    // Enlace "Regístrate aquí" (dentro del modal login)
    document.getElementById('link-ir-registro').addEventListener('click', function (e) {
        e.preventDefault();
        switchModal('modalLogin', 'modalRegistro');
    });

    // Enlace "Inicia sesión aquí" (dentro del modal registro)
    document.getElementById('link-ir-login').addEventListener('click', function (e) {
        e.preventDefault();
        switchModal('modalRegistro', 'modalLogin');
    });

    // ── Validación de contraseñas coincidentes en tiempo real ──────────
    var pass1 = document.getElementById('reg_password');
    var pass2 = document.getElementById('reg_password2');
    var msg   = document.getElementById('msg-password-match');

    function checkPasswords() {
        if (!pass2.value) { msg.textContent = ''; msg.className = 'password-match-msg'; return; }
        if (pass1.value === pass2.value) {
            msg.textContent = '✓ Las contraseñas coinciden';
            msg.className = 'password-match-msg match-ok';
        } else {
            msg.textContent = '✗ Las contraseñas no coinciden';
            msg.className = 'password-match-msg match-error';
        }
    }

    pass1.addEventListener('input', checkPasswords);
    pass2.addEventListener('input', checkPasswords);

});
</script>

</body>
</html>

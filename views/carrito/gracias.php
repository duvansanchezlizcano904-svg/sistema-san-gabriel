<?php
/**
 * Vista de Pedido Confirmado / Agradecimiento — Quesos San Gabriel
 * Diseño limpio, profesional y coherente con la identidad visual de la tienda.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pedido Confirmado! — Quesos San Gabriel</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Estilos -->
    <link href="public/css/publico.css" rel="stylesheet">
    <link href="public/css/home.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
        .order-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .order-header-banner {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            padding: 2.5rem 2rem 2rem;
            text-align: center;
        }
        .order-icon-badge {
            width: 72px;
            height: 72px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.4);
        }
        .btn-order-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            color: #ffffff;
            font-weight: 700;
            border-radius: 50px;
            padding: 0.85rem 1.75rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }
        .btn-order-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="home-navbar" style="position: sticky; top:0; z-index: 1030; background: #0f172a; border-bottom: 1px solid #334155;">
    <div class="home-container">
        <div class="home-nav-inner">
            <a href="index.php" class="home-brand">
                <span class="home-brand-emoji">🧀</span>
                <div>
                    <div class="home-brand-name">Quesos San Gabriel</div>
                    <div class="home-brand-sub">Quesería Artesanal</div>
                </div>
            </a>
            <ul class="home-nav-links">
                <li><a href="index.php" class="home-nav-link">Inicio</a></li>
                <li><a href="index.php#productos" class="home-nav-link">Productos</a></li>
                <li><a href="index.php#nosotros" class="home-nav-link">Nosotros</a></li>
                <li>
                    <span class="home-nav-user text-white">
                        <i class="bi bi-person-circle text-info me-1"></i>
                        <?= htmlspecialchars(explode(' ', $usuarioNombre)[0] ?: 'Cliente') ?>
                    </span>
                </li>
                <li>
                    <a href="logout.php" class="home-nav-link text-danger">
                        <i class="bi bi-box-arrow-right me-1"></i>Salir
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-7">

            <div class="order-card mb-4">
                <!-- Banner Éxito -->
                <div class="order-header-banner">
                    <div class="order-icon-badge">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <h2 class="fw-bold mb-1">¡Gracias por tu compra!</h2>
                    <p class="mb-0 text-white-50">
                        Tu pedido ha sido registrado exitosamente en nuestro sistema.
                    </p>
                </div>

                <!-- Detalles del pedido -->
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom flex-wrap gap-2">
                        <div>
                            <span class="text-muted small d-block">Número de Pedido</span>
                            <span class="fs-4 fw-bold text-primary font-monospace">
                                <?= htmlspecialchars($pedido['numero_pedido']) ?>
                            </span>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
                                <i class="bi bi-clock-history me-1"></i>
                                <?= ucfirst(htmlspecialchars($pedido['estado'] ?? 'pendiente')) ?>
                            </span>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <span class="text-muted small d-block">Fecha y Hora</span>
                            <strong class="text-dark"><?= formatearFecha($pedido['fecha']) ?></strong>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted small d-block">Cliente</span>
                            <strong class="text-dark"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Cliente') ?></strong>
                        </div>
                        <?php if (!empty($pedido['notas'])): ?>
                        <div class="col-12 mt-2">
                            <div class="p-2 bg-light rounded small border">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                <strong>Indicaciones de entrega:</strong> <?= htmlspecialchars($pedido['notas']) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <h6 class="fw-bold text-dark text-uppercase small mb-3">
                        <i class="bi bi-list-check me-1 text-primary"></i> Resumen de productos comprados:
                    </h6>

                    <div class="table-responsive rounded border mb-4">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedido['detalles'] as $d): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($d['producto_nombre']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($d['unidad_medida'] ?? '') ?></small>
                                    </td>
                                    <td class="text-center fw-bold"><?= $d['cantidad'] ?></td>
                                    <td class="text-end text-muted"><?= formatearMoneda($d['precio_unitario']) ?></td>
                                    <td class="text-end fw-bold text-primary"><?= formatearMoneda($d['subtotal']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end fw-bold fs-6">TOTAL PAGADO:</th>
                                    <th class="text-end fw-bold text-success fs-5"><?= formatearMoneda($pedido['total']) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Botón para ver e imprimir factura/tiquete con QR -->
                    <div class="d-grid gap-2 mb-4">
                        <a href="index.php?c=carrito&a=factura&id=<?= $pedido['id'] ?>" target="_blank" class="btn btn-order-primary d-flex align-items-center justify-content-center gap-2 py-3 fs-6">
                            <i class="bi bi-receipt-cutoff fs-5"></i>
                            <span>Ver e Imprimir Tiquete / Comprobante Oficial</span>
                        </a>
                    </div>

                    <div class="alert alert-info d-flex align-items-start gap-2 py-2 small mb-0">
                        <i class="bi bi-qr-code fs-4 flex-shrink-0"></i>
                        <div>
                            Tu comprobante incluye un código QR con el que puedes verificar la autenticidad de tu pedido en cualquier momento.
                        </div>
                    </div>

                </div>
            </div>

            <!-- Botones volver -->
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 d-flex align-items-center gap-2">
                    <i class="bi bi-house-door"></i> Ir a la Página Principal
                </a>
                <a href="index.php#productos" class="btn btn-primary rounded-pill px-4 py-2 d-flex align-items-center gap-2">
                    <i class="bi bi-grid-3x3-gap"></i> Seguir Comprando Quesos
                </a>
            </div>

        </div>
    </div>
</div>

<footer class="home-footer mt-5 py-4 bg-dark text-white text-center">
    <div class="container small text-white-50">
        &copy; <?= date('Y') ?> Quesos San Gabriel — Quesería Artesanal Colombiana. Todos los derechos reservados.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

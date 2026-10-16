<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiquete <?= htmlspecialchars($numero) ?> — Quesos San Gabriel</title>

    <!-- QR Code generator (sin dependencias externas de servidor) -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <style>
        /* =============================================
           ESTILOS DE TIQUETE TÉRMICO 80mm
           ============================================= */
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

        :root {
            --ticket-width: 300px;
            --ticket-bg:    #fff;
            --ticket-text:  #111;
            --ticket-muted: #555;
            --ticket-line:  #ccc;
            --ticket-accent:#1d4ed8;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #e8ecf0;
            font-family: 'IBM Plex Mono', 'Courier New', monospace;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            padding: 2rem 1rem;
            color: var(--ticket-text);
        }

        /* ── Barra de acciones (no se imprime) ── */
        .action-bar {
            width: 100%;
            max-width: 520px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .action-bar h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .btn-back {
            background: #fff;
            border: 1px solid #d1d5db;
            color: #374151;
            padding: 0.45rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
        }
        .btn-back:hover { background: #f3f4f6; }

        .btn-print {
            background: #1d4ed8;
            border: none;
            color: #fff;
            padding: 0.45rem 1.25rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
        }
        .btn-print:hover { background: #1e3a8a; }

        /* ── Tiquete ── */
        .ticket-wrap {
            width: var(--ticket-width);
            position: relative;
        }

        /* Borde dentado superior */
        .ticket-edge-top {
            width: 100%;
            height: 14px;
            background:
                radial-gradient(circle at 7px 14px, #e8ecf0 7px, transparent 0) 0 0 / 14px 14px,
                radial-gradient(circle at 7px 0,   #e8ecf0 7px, transparent 0) 7px 0 / 14px 14px;
            background-color: var(--ticket-bg);
            background-repeat: repeat-x;
        }

        /* Borde dentado inferior */
        .ticket-edge-bottom {
            width: 100%;
            height: 14px;
            background:
                radial-gradient(circle at 7px 0,   #e8ecf0 7px, transparent 0) 0 0 / 14px 14px,
                radial-gradient(circle at 7px 14px, #e8ecf0 7px, transparent 0) 7px 0 / 14px 14px;
            background-color: var(--ticket-bg);
            background-repeat: repeat-x;
        }

        .ticket-body {
            background: var(--ticket-bg);
            padding: 0.75rem 1rem;
            width: 100%;
        }

        /* Sombra del tiquete completo */
        .ticket-wrap .ticket-edge-top,
        .ticket-wrap .ticket-body,
        .ticket-wrap .ticket-edge-bottom {
            box-shadow: 0 2px 12px rgba(0,0,0,0.12);
        }

        /* ── Header del tiquete ── */
        .tkt-header {
            text-align: center;
            padding-bottom: 0.6rem;
            margin-bottom: 0.6rem;
            border-bottom: 1px dashed var(--ticket-line);
        }

        .tkt-emoji { font-size: 2rem; line-height: 1; display: block; margin-bottom: 0.25rem; }

        .tkt-brand {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #000;
            text-transform: uppercase;
        }

        .tkt-sub {
            font-size: 0.65rem;
            color: var(--ticket-muted);
            letter-spacing: 0.05em;
            margin-top: 0.1rem;
        }

        .tkt-tipo {
            display: inline-block;
            margin-top: 0.4rem;
            background: #1d4ed8;
            color: #fff;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 0.15rem 0.6rem;
            border-radius: 4px;
        }

        /* ── Info del documento ── */
        .tkt-info {
            margin-bottom: 0.6rem;
            padding-bottom: 0.6rem;
            border-bottom: 1px dashed var(--ticket-line);
        }

        .tkt-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 0.7rem;
            line-height: 1.6;
            gap: 0.5rem;
        }

        .tkt-row .label { color: var(--ticket-muted); white-space: nowrap; }
        .tkt-row .value { font-weight: 600; text-align: right; word-break: break-all; }

        .tkt-numero {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--ticket-accent);
            letter-spacing: 0.05em;
        }

        /* ── Separador ── */
        .tkt-sep {
            border: none;
            border-top: 1px dashed var(--ticket-line);
            margin: 0.55rem 0;
        }

        .tkt-sep-solid {
            border: none;
            border-top: 2px solid var(--ticket-text);
            margin: 0.55rem 0;
        }

        /* ── Productos ── */
        .tkt-items-header {
            display: grid;
            grid-template-columns: 1fr 42px 72px;
            gap: 0.25rem;
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--ticket-muted);
            margin-bottom: 0.3rem;
        }

        .tkt-item {
            margin-bottom: 0.45rem;
        }

        .tkt-item-name {
            font-size: 0.72rem;
            font-weight: 600;
            color: #000;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tkt-item-detail {
            display: grid;
            grid-template-columns: 1fr 42px 72px;
            gap: 0.25rem;
            font-size: 0.68rem;
            color: var(--ticket-muted);
            align-items: baseline;
        }

        .tkt-item-detail .subtotal {
            font-weight: 700;
            color: #000;
            text-align: right;
        }

        .tkt-item-detail .qty { text-align: center; }
        .tkt-item-detail .unit-price { text-align: left; }

        /* ── Totales ── */
        .tkt-totales { margin-top: 0.35rem; }

        .tkt-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            line-height: 1.7;
        }
        .tkt-total-row .label { color: var(--ticket-muted); }
        .tkt-total-row .value { font-weight: 600; }

        .tkt-grand-total {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-top: 0.35rem;
            padding-top: 0.35rem;
            border-top: 2px solid #000;
        }
        .tkt-grand-total .label {
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .tkt-grand-total .value {
            font-size: 1.05rem;
            font-weight: 800;
        }

        /* ── Estado del pedido ── */
        .tkt-estado {
            text-align: center;
            margin: 0.5rem 0;
        }
        .tkt-estado span {
            display: inline-block;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.2rem 0.75rem;
            border-radius: 4px;
            border: 1px solid currentColor;
        }
        .estado-pendiente  { color: #92400e; background: #fef3c7; }
        .estado-confirmado { color: #065f46; background: #d1fae5; }
        .estado-entregado  { color: #1e3a8a; background: #dbeafe; }

        /* ── QR ── */
        .tkt-qr-section {
            text-align: center;
            padding: 0.65rem 0 0.4rem;
        }
        .tkt-qr-section p {
            font-size: 0.6rem;
            color: var(--ticket-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.4rem;
        }
        #qrcode {
            display: inline-block;
            padding: 4px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        #qrcode canvas, #qrcode img { display: block; }

        /* ── Footer tiquete ── */
        .tkt-footer {
            text-align: center;
            padding-top: 0.6rem;
            border-top: 1px dashed var(--ticket-line);
            margin-top: 0.5rem;
        }
        .tkt-footer p {
            font-size: 0.63rem;
            color: var(--ticket-muted);
            line-height: 1.5;
        }
        .tkt-footer strong { color: #000; }

        /* ── IMPRESIÓN ── */
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            body {
                background: #fff !important;
                padding: 0 !important;
                display: block !important;
            }

            .action-bar { display: none !important; }

            .ticket-wrap {
                width: 100% !important;
                box-shadow: none !important;
            }

            .ticket-edge-top,
            .ticket-edge-bottom {
                display: none !important;
            }

            .ticket-body {
                box-shadow: none !important;
                padding: 4mm 5mm !important;
            }

            .tkt-brand  { font-size: 13pt !important; }
            .tkt-numero { font-size: 10pt !important; }
            .tkt-grand-total .value { font-size: 12pt !important; }

            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body>

<!-- ── Barra de acciones (no se imprime) ── -->
<div class="action-bar no-print">
    <div>
        <h2>🧾 Tiquete <?= htmlspecialchars($numero) ?></h2>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap">
        <a href="<?= htmlspecialchars($urlVolver) ?>" class="btn-back">
            ← Volver
        </a>
        <button class="btn-print" onclick="window.print()">
            🖨️ Imprimir Tiquete
        </button>
    </div>
</div>

<!-- ── Tiquete ── -->
<div class="ticket-wrap">
    <div class="ticket-edge-top"></div>

    <div class="ticket-body">

        <!-- Encabezado -->
        <div class="tkt-header">
            <span class="tkt-emoji">🧀</span>
            <div class="tkt-brand">Quesos San Gabriel</div>
            <div class="tkt-sub">Quesería Artesanal</div>
            <div class="tkt-sub">ventas@quesossangabriel.com</div>
            <span class="tkt-tipo"><?= $esPedido ? 'Comprobante de Pedido' : 'Comprobante de Venta' ?></span>
        </div>

        <!-- Datos del documento -->
        <div class="tkt-info">
            <div class="tkt-row">
                <span class="label"><?= $esPedido ? 'N° Pedido' : 'N° Venta' ?>:</span>
                <span class="value tkt-numero"><?= htmlspecialchars($numero) ?></span>
            </div>
            <div class="tkt-row">
                <span class="label">Fecha:</span>
                <span class="value"><?= date('d/m/Y', strtotime($fecha)) ?></span>
            </div>
            <div class="tkt-row">
                <span class="label">Hora:</span>
                <span class="value"><?= date('h:i A', strtotime($fecha)) ?></span>
            </div>
            <div class="tkt-row">
                <span class="label">Cliente:</span>
                <span class="value"><?= htmlspecialchars($cliente) ?></span>
            </div>
            <?php if (!empty($vendedor)): ?>
            <div class="tkt-row">
                <span class="label">Atendido por:</span>
                <span class="value"><?= htmlspecialchars($vendedor) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($notas)): ?>
            <div class="tkt-row">
                <span class="label">Notas:</span>
                <span class="value"><?= htmlspecialchars($notas) ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Estado (solo pedidos) -->
        <?php if ($esPedido && !empty($estado)): ?>
        <div class="tkt-estado">
            <span class="estado-<?= htmlspecialchars($estado) ?>">
                Estado: <?= strtoupper($estado) ?>
            </span>
        </div>
        <hr class="tkt-sep">
        <?php endif; ?>

        <!-- Encabezado de items -->
        <div class="tkt-items-header">
            <span>Producto</span>
            <span style="text-align:center">Cant</span>
            <span style="text-align:right">Subtotal</span>
        </div>

        <hr class="tkt-sep-solid">

        <!-- Items -->
        <?php foreach ($items as $item): ?>
        <div class="tkt-item">
            <div class="tkt-item-name" title="<?= htmlspecialchars($item['nombre']) ?>">
                <?= htmlspecialchars($item['nombre']) ?>
            </div>
            <div class="tkt-item-detail">
                <span class="unit-price">@ <?= formatearMoneda($item['precio_unitario']) ?></span>
                <span class="qty"><?= $item['cantidad'] ?></span>
                <span class="subtotal"><?= formatearMoneda($item['subtotal']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>

        <hr class="tkt-sep-solid">

        <!-- Totales -->
        <div class="tkt-totales">
            <div class="tkt-total-row">
                <span class="label">Subtotal</span>
                <span class="value"><?= formatearMoneda($subtotal) ?></span>
            </div>
            <div class="tkt-total-row">
                <span class="label">Descuento</span>
                <span class="value">$ 0,00</span>
            </div>
            <div class="tkt-grand-total">
                <span class="label">TOTAL</span>
                <span class="value"><?= formatearMoneda($total) ?></span>
            </div>
        </div>

        <!-- QR -->
        <hr class="tkt-sep">
        <div class="tkt-qr-section">
            <p>Escanea para verificar</p>
            <div id="qrcode"></div>
        </div>

        <!-- Footer -->
        <div class="tkt-footer">
            <p>
                <strong>¡Gracias por tu compra!</strong><br>
                <?= $esPedido ? 'Tu pedido está siendo procesado.' : 'Vuelve pronto por más quesos artesanales.' ?><br>
                <strong>Quesos San Gabriel</strong> &copy; <?= date('Y') ?>
            </p>
        </div>

    </div><!-- /.ticket-body -->

    <div class="ticket-edge-bottom"></div>
</div><!-- /.ticket-wrap -->


<script>
    // Generar QR con los datos del comprobante
    var qrData = <?= json_encode($qrData) ?>;

    new QRCode(document.getElementById("qrcode"), {
        text: qrData,
        width:  128,
        height: 128,
        colorDark:  "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
    });

    // Auto-abrir diálogo de impresión si viene con ?print=1
    <?php if (!empty($_GET['print'])): ?>
    window.addEventListener('load', function() {
        setTimeout(function() { window.print(); }, 600);
    });
    <?php endif; ?>
</script>
</body>
</html>

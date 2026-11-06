<?php
/**
 * Controlador del Carrito de Compras — Quesos San Gabriel
 * Gestiona el carrito en sesión, validación de stock, cálculo seguro
 * y confirmación de pedidos atómicos en la base de datos.
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Pedido.php';

class CarritoController {
    private Producto $modeloProducto;
    private Pedido   $modeloPedido;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->modeloProducto = new Producto();
        $this->modeloPedido   = new Pedido();
    }

    /**
     * Resuelve la imagen adecuada para un producto (BD, archivo local o categoría)
     */
    private function resolverImagen(array $prod): string {
        $img = trim($prod['imagen'] ?? $prod['imagen_url'] ?? '');
        if (!empty($img)) {
            if (filter_var($img, FILTER_VALIDATE_URL) || str_starts_with($img, 'http')) {
                return $img;
            }
            if (file_exists(__DIR__ . '/../' . ltrim($img, '/'))) {
                return $img;
            }
        }

        $catMap = [
            'Quesos Frescos'     => 'public/img/productos/queso_fresco.jpg',
            'Quesos Madurados'   => 'public/img/productos/parmesano.jpg',
            'Quesos Artesanales' => 'public/img/productos/queso_artesanal.jpg',
            'Quesos Cremosos'    => 'public/img/productos/brie.jpg',
            'Quesos de Cabra'    => 'public/img/productos/queso_cabra.jpg',
            'Quesos Azules'      => 'public/img/productos/queso_azul.jpg',
        ];
        $cat = $prod['categoria'] ?? $prod['tipo'] ?? '';
        return $catMap[$cat] ?? 'public/img/placeholder-queso.png';
    }

    /* ─── Mostrar vista principal del carrito ─── */
    public function index(): void {
        $carrito = $_SESSION['carrito'] ?? [];

        // Refrescar precios y validar stock directamente desde la base de datos
        foreach ($carrito as $id => &$item) {
            $prod = $this->modeloProducto->obtenerPorId((int)$id);
            if (!$prod || $prod['estado'] !== 'activo') {
                unset($carrito[$id]);
                continue;
            }
            $item['precio_unitario']   = (float)$prod['precio_venta'];
            $item['stock_real']        = (int)$prod['cantidad'];
            if ($item['cantidad'] > $item['stock_real']) {
                $item['cantidad'] = max(1, $item['stock_real']);
            }
            $item['subtotal']          = $item['precio_unitario'] * $item['cantidad'];
            $item['unidad_medida']     = $prod['unidad_medida'];
            $item['categoria']         = $prod['categoria'] ?? $prod['tipo'] ?? '';
            $item['tipo_leche']        = $prod['tipo_leche'] ?? '';
            $item['peso_presentacion'] = $prod['peso_presentacion'] ?? $prod['unidad_medida'];
            $item['tipo']              = $prod['tipo'] ?? '';
            $item['imagen']            = $this->resolverImagen($prod);
        }
        unset($item);
        $_SESSION['carrito'] = $carrito;

        $totalUnidades = array_sum(array_column($carrito, 'cantidad'));
        $totalMonto    = array_sum(array_column($carrito, 'subtotal'));

        // Generar token anti-duplicados y CSRF para el proceso de confirmación
        if (empty($_SESSION['pedido_csrf_token'])) {
            $_SESSION['pedido_csrf_token'] = bin2hex(random_bytes(16));
        }
        $csrfToken = $_SESSION['pedido_csrf_token'];

        $estaAutenticado = is_authenticated();
        $esAdmin         = is_admin();
        $usuarioNombre   = $_SESSION['usuario_nombre'] ?? '';
        $usuarioEmail    = $_SESSION['usuario_email'] ?? '';
        $flash           = get_flash();

        $tituloPagina = 'Mi Carrito — Quesos San Gabriel';
        $menuActivo   = 'carrito';

        require_once __DIR__ . '/../views/carrito/index.php';
    }

    /* ─── Agregar producto al carrito ─── */
    public function agregar(): void {
        $esAjax = (isset($_POST['ajax']) && $_POST['ajax'] === '1') ||
                  (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
                exit;
            }
            redirect('index.php');
        }

        $productoId = filter_var($_POST['producto_id'] ?? 0, FILTER_VALIDATE_INT);
        $cantidad   = filter_var($_POST['cantidad'] ?? 1, FILTER_VALIDATE_INT);
        $redirect   = trim($_POST['redirect'] ?? '');

        // Validar y sanitizar redirect interno
        if (!empty($redirect) && preg_match('#^(https?:)?//#i', $redirect)) {
            $redirect = '';
        }
        if (empty($redirect)) {
            $redirect = is_authenticated() ? 'index.php?c=catalogo' : 'index.php';
        }

        // 1. Validar parámetros de entrada
        if (!$productoId || !$cantidad || $cantidad < 1) {
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'mensaje' => 'La cantidad ingresada no es válida.']);
                exit;
            }
            set_flash('danger', 'Cantidad no válida para agregar al carrito.');
            redirect($redirect);
        }

        // 2. Comprobar existencia y disponibilidad en el servidor (MySQL)
        $prod = $this->modeloProducto->obtenerPorId($productoId);
        if (!$prod || $prod['estado'] !== 'activo') {
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'mensaje' => 'Este queso no está disponible en este momento.']);
                exit;
            }
            set_flash('warning', 'Ese producto no está disponible en este momento.');
            redirect($redirect);
        }

        $stockDisponible = (int)$prod['cantidad'];

        // 3. No permitir agregar productos agotados
        if ($stockDisponible <= 0) {
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'mensaje' => "El producto '{$prod['nombre']}' se encuentra agotado."]);
                exit;
            }
            set_flash('danger', "El producto '{$prod['nombre']}' está agotado.");
            redirect($redirect);
        }

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $id = (string)$productoId;
        $mensajeFlash = '';
        $tipoFlash    = 'success';

        // 4. Si el producto ya está en el carrito, aumentar cantidad sin duplicar
        if (isset($_SESSION['carrito'][$id])) {
            $cantidadActual = (int)$_SESSION['carrito'][$id]['cantidad'];
            $cantidadNueva  = $cantidadActual + $cantidad;

            if ($cantidadNueva > $stockDisponible) {
                $cantidadNueva = $stockDisponible;
                $mensajeFlash = "Solo hay {$stockDisponible} unidades disponibles de {$prod['nombre']}. Se ajustó al máximo.";
                $tipoFlash    = 'warning';
            } else {
                $mensajeFlash = "Se agregaron {$cantidad} unidad(es) más de '{$prod['nombre']}'. Total en carrito: {$cantidadNueva}.";
            }

            $_SESSION['carrito'][$id]['cantidad']        = $cantidadNueva;
            $_SESSION['carrito'][$id]['precio_unitario'] = (float)$prod['precio_venta'];
            $_SESSION['carrito'][$id]['subtotal']        = $cantidadNueva * (float)$prod['precio_venta'];
            $_SESSION['carrito'][$id]['stock_real']      = $stockDisponible;
            $_SESSION['carrito'][$id]['imagen']          = $this->resolverImagen($prod);

        } else {
            // Producto nuevo en el carrito
            $cantFinal = min($cantidad, $stockDisponible);

            if ($cantFinal < $cantidad) {
                $mensajeFlash = "Solo se pudieron agregar {$cantFinal} unidades de '{$prod['nombre']}' por inventario disponible.";
                $tipoFlash    = 'warning';
            } else {
                $mensajeFlash = "¡{$prod['nombre']} agregado al carrito exitosamente!";
            }

            $_SESSION['carrito'][$id] = [
                'producto_id'       => $productoId,
                'nombre'            => $prod['nombre'],
                'precio_unitario'   => (float)$prod['precio_venta'],
                'unidad_medida'     => $prod['unidad_medida'],
                'cantidad'          => $cantFinal,
                'subtotal'          => $cantFinal * (float)$prod['precio_venta'],
                'stock_real'        => $stockDisponible,
                'categoria'         => $prod['categoria'] ?? $prod['tipo'] ?? '',
                'tipo_leche'        => $prod['tipo_leche'] ?? '',
                'peso_presentacion' => $prod['peso_presentacion'] ?? $prod['unidad_medida'],
                'tipo'              => $prod['tipo'] ?? '',
                'imagen'            => $this->resolverImagen($prod),
            ];
        }

        $totalItems = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
        $totalMonto = array_sum(array_column($_SESSION['carrito'], 'subtotal'));

        if ($esAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'      => true,
                'mensaje'      => $mensajeFlash,
                'tipo'         => $tipoFlash,
                'total_items'  => $totalItems,
                'total_monto'  => $totalMonto,
                'producto_id'  => $productoId,
                'nombre'       => $prod['nombre'],
                'cantidad'     => $_SESSION['carrito'][$id]['cantidad']
            ]);
            exit;
        }

        set_flash($tipoFlash, $mensajeFlash);
        redirect($redirect);
    }

    /* ─── Actualizar cantidad desde la vista del carrito ─── */
    public function actualizar(): void {
        $esAjax = (isset($_POST['ajax']) && $_POST['ajax'] === '1') ||
                  (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
                exit;
            }
            redirect('index.php?c=carrito');
        }

        $productoId = filter_var($_POST['producto_id'] ?? 0, FILTER_VALIDATE_INT);
        $cantidad   = filter_var($_POST['cantidad'] ?? 1, FILTER_VALIDATE_INT);

        if (!$productoId || $cantidad < 1) {
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'mensaje' => 'Cantidad o producto no válido.']);
                exit;
            }
            set_flash('danger', 'Cantidad no válida.');
            redirect('index.php?c=carrito');
        }

        $id = (string)$productoId;
        if (!isset($_SESSION['carrito'][$id])) {
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'mensaje' => 'El producto no está en el carrito.']);
                exit;
            }
            set_flash('warning', 'El producto no está en tu carrito.');
            redirect('index.php?c=carrito');
        }

        // Validar stock real en el servidor
        $prod = $this->modeloProducto->obtenerPorId($productoId);
        if (!$prod || $prod['estado'] !== 'activo') {
            unset($_SESSION['carrito'][$id]);
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'mensaje' => 'Este producto ya no está disponible.']);
                exit;
            }
            set_flash('danger', 'El producto seleccionado ya no está disponible.');
            redirect('index.php?c=carrito');
        }

        $stockReal = (int)$prod['cantidad'];
        if ($cantidad > $stockReal) {
            $cantidad = $stockReal;
        }

        $_SESSION['carrito'][$id]['cantidad']        = $cantidad;
        $_SESSION['carrito'][$id]['precio_unitario'] = (float)$prod['precio_venta'];
        $_SESSION['carrito'][$id]['subtotal']        = $cantidad * (float)$prod['precio_venta'];
        $_SESSION['carrito'][$id]['stock_real']      = $stockReal;

        $totalItems = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
        $totalMonto = array_sum(array_column($_SESSION['carrito'], 'subtotal'));

        if ($esAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'     => true,
                'cantidad'    => $cantidad,
                'subtotal'    => $_SESSION['carrito'][$id]['subtotal'],
                'total_monto' => $totalMonto,
                'total_items' => $totalItems,
                'stock_real'  => $stockReal
            ]);
            exit;
        }

        set_flash('success', 'Cantidad actualizada correctamente.');
        redirect('index.php?c=carrito');
    }

    /* ─── Quitar producto del carrito ─── */
    public function quitar(): void {
        $esAjax = (isset($_POST['ajax']) && $_POST['ajax'] === '1');
        $productoId = (string)(int)($_POST['producto_id'] ?? $_GET['id'] ?? 0);
        $redirect   = $_POST['redirect'] ?? $_GET['redirect'] ?? 'index.php?c=carrito';

        if (isset($_SESSION['carrito'][$productoId])) {
            $nombre = $_SESSION['carrito'][$productoId]['nombre'] ?? 'Producto';
            unset($_SESSION['carrito'][$productoId]);
            $mensaje = "'{$nombre}' ha sido eliminado del carrito.";
        } else {
            $mensaje = "El producto no estaba en el carrito.";
        }

        $totalItems = array_sum(array_column($_SESSION['carrito'] ?? [], 'cantidad'));
        $totalMonto = array_sum(array_column($_SESSION['carrito'] ?? [], 'subtotal'));

        if ($esAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'     => true,
                'mensaje'     => $mensaje,
                'total_items' => $totalItems,
                'total_monto' => $totalMonto
            ]);
            exit;
        }

        set_flash('info', $mensaje);
        redirect($redirect);
    }

    /* ─── Vaciar todo el carrito ─── */
    public function vaciar(): void {
        $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? 'index.php?c=carrito';
        $_SESSION['carrito'] = [];
        set_flash('info', 'El carrito ha sido vaciado.');
        redirect($redirect);
    }

    /* ─── Confirmar pedido y registrar compra atómica ─── */
    public function confirmar(): void {
        // 1. Debe estar autenticado como cliente
        if (!is_authenticated()) {
            set_flash('warning', 'Para continuar con tu compra, necesitas tener una cuenta o iniciar sesión.');
            redirect('index.php?c=carrito');
        }

        if (is_admin()) {
            set_flash('danger', 'El usuario Administrador no puede registrar pedidos como cliente. Por favor inicia sesión con una cuenta de cliente.');
            redirect('index.php?c=carrito');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?c=carrito');
        }

        // 2. Protección contra solicitudes duplicadas (recarga de página o doble clic)
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        $tokenSesion  = $_SESSION['pedido_csrf_token'] ?? '';

        if (empty($tokenEnviado) || empty($tokenSesion) || !hash_equals($tokenSesion, $tokenEnviado)) {
            set_flash('warning', 'Este pedido ya fue procesado o la sesión expiró para evitar compras duplicadas.');
            redirect('index.php?c=carrito');
        }

        // Inmediatamente invalidar el token en sesión
        unset($_SESSION['pedido_csrf_token']);

        $carrito = $_SESSION['carrito'] ?? [];
        if (empty($carrito)) {
            set_flash('warning', 'Tu carrito está vacío.');
            redirect('index.php?c=carrito');
        }

        $notas = sanitize($_POST['notas'] ?? '');

        // 3. Preparar items y validar datos
        $items = [];
        foreach ($carrito as $id => $item) {
            $items[] = [
                'producto_id'     => (int)$id,
                'nombre'          => $item['nombre'],
                'cantidad'        => (int)$item['cantidad'],
                'precio_unitario' => (float)$item['precio_unitario'],
                'subtotal'        => (float)$item['subtotal'],
            ];
        }

        try {
            // 4. Registrar pedido y descontar inventario en transacción PDO
            $pedidoId = $this->modeloPedido->registrarPedido([
                'cliente_id' => (int)$_SESSION['usuario_id'],
                'notas'      => $notas,
            ], $items);

            // 5. Vaciar carrito tras éxito comprobado
            $_SESSION['carrito'] = [];

            set_flash('success', '¡Compra confirmada exitosamente! Tu pedido ha sido registrado.');
            redirect('index.php?c=carrito&a=gracias&id=' . $pedidoId);

        } catch (Exception $e) {
            // Regenerar token para que el usuario pueda volver a intentar si hubo error de stock
            $_SESSION['pedido_csrf_token'] = bin2hex(random_bytes(16));
            set_flash('danger', 'Error al procesar la compra: ' . $e->getMessage());
            redirect('index.php?c=carrito');
        }
    }

    /* ─── Pantalla de confirmación y agradecimiento ─── */
    public function gracias(): void {
        require_auth();
        $pedidoId = (int)($_GET['id'] ?? 0);

        if (!$pedidoId) {
            redirect('index.php');
        }

        $pedido = $this->modeloPedido->obtenerPorId($pedidoId);
        if (!$pedido) {
            set_flash('danger', 'El pedido no existe.');
            redirect('index.php');
        }

        // Control de seguridad: solo el cliente dueño o el admin puede consultar su pedido
        if ((int)$pedido['cliente_id'] !== (int)($_SESSION['usuario_id'] ?? 0) && !is_admin()) {
            set_flash('danger', 'No tienes permiso para ver este pedido.');
            redirect('index.php');
        }

        $tituloPagina    = '¡Compra Confirmada! — Quesos San Gabriel';
        $menuActivo      = 'carrito';
        $estaAutenticado = is_authenticated();
        $esAdmin         = is_admin();
        $usuarioNombre   = $_SESSION['usuario_nombre'] ?? '';
        $flash           = get_flash();

        require_once __DIR__ . '/../views/carrito/gracias.php';
    }

    /* ─── Tiquete / Factura de pedido del cliente ─── */
    public function factura(): void {
        require_auth();
        $pedidoId = (int)($_GET['id'] ?? 0);

        if (!$pedidoId) {
            set_flash('danger', 'Pedido no encontrado.');
            redirect('index.php?c=carrito');
        }

        $pedido = $this->modeloPedido->obtenerPorId($pedidoId);
        if (!$pedido) {
            set_flash('danger', 'El pedido no existe.');
            redirect('index.php?c=carrito');
        }

        if ((int)$pedido['cliente_id'] !== (int)($_SESSION['usuario_id'] ?? 0) && !is_admin()) {
            set_flash('danger', 'No tienes permiso para ver este pedido.');
            redirect('index.php?c=carrito');
        }

        $esPedido  = true;
        $numero    = $pedido['numero_pedido'];
        $fecha     = $pedido['fecha'];
        $cliente   = $_SESSION['usuario_nombre'] ?? 'Cliente';
        $vendedor  = '';
        $notas     = $pedido['notas'] ?? '';
        $estado    = $pedido['estado'] ?? 'pendiente';
        $total     = (float)$pedido['total'];
        $subtotal  = $total;
        $urlVolver = 'index.php?c=carrito&a=gracias&id=' . $pedidoId;

        $items = array_map(fn($d) => [
            'nombre'          => $d['producto_nombre'],
            'cantidad'        => $d['cantidad'],
            'precio_unitario' => (float)$d['precio_unitario'],
            'subtotal'        => (float)$d['subtotal'],
        ], $pedido['detalles']);

        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $qrData = "https://{$host}/index.php?c=carrito&a=factura&id={$pedidoId}\n"
                . "Pedido: {$numero}\n"
                . "Total: " . number_format($total, 2, ',', '.') . "\n"
                . "Quesos San Gabriel";

        require_once __DIR__ . '/../views/factura/tiquete.php';
    }
}

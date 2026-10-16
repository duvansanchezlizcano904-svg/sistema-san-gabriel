<?php
/**
 * Controlador del Carrito de Compras (Clientes)
 * Gestiona el carrito en sesión y confirma pedidos a la BD.
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Pedido.php';

class CarritoController {
    private Producto $modeloProducto;
    private Pedido   $modeloPedido;

    public function __construct() {
        // Inicializar modelos
        $this->modeloProducto = new Producto();
        $this->modeloPedido   = new Pedido();
    }

    /* ─── Mostrar carrito ─── */
    public function index(): void {
        $carrito = $_SESSION['carrito'] ?? [];

        // Refrescar precios y stock desde BD con información de categorización
        foreach ($carrito as $id => &$item) {
            $prod = $this->modeloProducto->obtenerPorId((int)$id);
            if (!$prod || $prod['estado'] !== 'activo') {
                unset($carrito[$id]);
                continue;
            }
            $item['precio_unitario']   = (float)$prod['precio_venta'];
            $item['subtotal']          = $item['precio_unitario'] * $item['cantidad'];
            $item['stock_real']        = (int)$prod['cantidad'];
            $item['unidad_medida']     = $prod['unidad_medida'];
            $item['categoria']         = $prod['categoria'] ?? '';
            $item['tipo_leche']        = $prod['tipo_leche'] ?? '';
            $item['peso_presentacion'] = $prod['peso_presentacion'] ?? $prod['unidad_medida'];
            $item['tipo']              = $prod['tipo'] ?? '';
        }
        unset($item);
        $_SESSION['carrito'] = $carrito;

        $total = array_sum(array_column($carrito, 'subtotal'));

        $tituloPagina = 'Mi Carrito — Quesos San Gabriel';
        $menuActivo   = 'carrito';
        require_once __DIR__ . '/../views/carrito/index.php';
    }

    /* ─── Agregar producto al carrito ─── */
    public function agregar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('publico.php');
        }

        $productoId = (int)($_POST['producto_id'] ?? 0);
        $cantidad   = max(1, (int)($_POST['cantidad'] ?? 1));
        $redirect   = $_POST['redirect'] ?? 'publico.php';

        $prod = $this->modeloProducto->obtenerPorId($productoId);
        if (!$prod || $prod['estado'] !== 'activo') {
            set_flash('danger', 'El producto no está disponible.');
            redirect($redirect);
        }

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $id = (string)$productoId;
        if (isset($_SESSION['carrito'][$id])) {
            // El producto ya existe, incrementar cantidad
            $nueva = $_SESSION['carrito'][$id]['cantidad'] + $cantidad;
            $nueva = min($nueva, (int)$prod['cantidad']); // no superar stock
            $_SESSION['carrito'][$id]['cantidad'] = $nueva;
            $_SESSION['carrito'][$id]['subtotal']  = $nueva * (float)$prod['precio_venta'];
            
            set_flash('success', "<strong>{$prod['nombre']}</strong> — cantidad actualizada en el carrito.");
        } else {
            // Producto nuevo, agregarlo al carrito
            $cant = min($cantidad, (int)$prod['cantidad']);
            $_SESSION['carrito'][$id] = [
                'producto_id'       => $productoId,
                'nombre'            => $prod['nombre'],
                'precio_unitario'   => (float)$prod['precio_venta'],
                'unidad_medida'     => $prod['unidad_medida'],
                'cantidad'          => $cant,
                'subtotal'          => $cant * (float)$prod['precio_venta'],
                'stock_real'        => (int)$prod['cantidad'],
                'categoria'         => $prod['categoria'] ?? '',
                'tipo_leche'        => $prod['tipo_leche'] ?? '',
                'peso_presentacion' => $prod['peso_presentacion'] ?? $prod['unidad_medida'],
                'tipo'              => $prod['tipo'] ?? '',
            ];
            
            set_flash('success', "<strong>{$prod['nombre']}</strong> agregado al carrito.");
        }
        
        redirect($redirect);
    }

    /* ─── Actualizar cantidad desde la vista del carrito ─── */
    public function actualizar(): void {
        $redirect = $_POST['redirect'] ?? 'index.php?c=carrito';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect($redirect);
        }

        $productoId = (string)(int)($_POST['producto_id'] ?? 0);
        $cantidad   = max(1, (int)($_POST['cantidad'] ?? 1));

        if (isset($_SESSION['carrito'][$productoId])) {
            $prod = $this->modeloProducto->obtenerPorId((int)$productoId);
            $stockReal = $prod ? (int)$prod['cantidad'] : 999;
            $cant = min($cantidad, $stockReal);
            $_SESSION['carrito'][$productoId]['cantidad'] = $cant;
            $_SESSION['carrito'][$productoId]['subtotal']  =
                $cant * $_SESSION['carrito'][$productoId]['precio_unitario'];
        }

        redirect($redirect);
    }

    /* ─── Quitar producto del carrito ─── */
    public function quitar(): void {
        $productoId = (string)(int)($_POST['producto_id'] ?? $_GET['id'] ?? 0);
        $redirect   = $_POST['redirect'] ?? $_GET['redirect'] ?? 'index.php?c=carrito';
        unset($_SESSION['carrito'][$productoId]);
        set_flash('info', 'Producto eliminado del carrito.');
        redirect($redirect);
    }

    /* ─── Vaciar todo el carrito ─── */
    public function vaciar(): void {
        $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? 'index.php?c=carrito';
        $_SESSION['carrito'] = [];
        set_flash('info', 'El carrito fue vaciado.');
        redirect($redirect);
    }

    /* ─── Confirmar pedido ─── */
    public function confirmar(): void {
        require_auth(); // Requiere inicio de sesión para facturar

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?c=carrito');
        }

        $carrito = $_SESSION['carrito'] ?? [];
        if (empty($carrito)) {
            set_flash('warning', 'Tu carrito está vacío.');
            redirect('index.php?c=carrito');
        }

        // Solo clientes pueden confirmar pedidos
        if (is_admin()) {
            set_flash('danger', 'El administrador no puede realizar pedidos desde el carrito.');
            redirect('index.php?c=carrito');
        }

        $notas = sanitize($_POST['notas'] ?? '');

        $items = [];
        foreach ($carrito as $id => $item) {
            $items[] = [
                'producto_id'     => (int)$id,
                'nombre'          => $item['nombre'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal'        => $item['subtotal'],
            ];
        }

        try {
            $pedidoId = $this->modeloPedido->registrarPedido([
                'cliente_id' => $_SESSION['usuario_id'],
                'notas'      => $notas,
            ], $items);

            // Vaciar carrito tras pedido exitoso
            $_SESSION['carrito'] = [];

            set_flash('success', '¡Pedido confirmado! Tu número de pedido fue registrado exitosamente.');
            redirect('index.php?c=carrito&a=gracias&id=' . $pedidoId);

        } catch (Exception $e) {
            set_flash('danger', 'Error al procesar el pedido: ' . $e->getMessage());
            redirect('index.php?c=carrito');
        }
    }

    /* ─── Pantalla de confirmación / gracias ─── */
    public function gracias(): void {
        require_auth();
        $pedidoId = (int)($_GET['id'] ?? 0);
        $pedido   = $pedidoId ? $this->modeloPedido->obtenerPorId($pedidoId) : null;

        $tituloPagina = 'Pedido Confirmado — Quesos San Gabriel';
        $menuActivo   = 'carrito';
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

        // Solo el propio cliente puede ver su pedido
        if ((int)$pedido['cliente_id'] !== (int)($_SESSION['usuario_id'] ?? 0) && !is_admin()) {
            set_flash('danger', 'No tienes permiso para ver este pedido.');
            redirect('index.php?c=carrito');
        }

        // Variables que espera la vista tiquete.php
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

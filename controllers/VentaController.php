<?php
/**
 * Controlador de Registro de Ventas
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Venta.php';

class VentaController {
    private Producto $modeloProducto;
    private Venta $modeloVenta;

    public function __construct() {
        require_admin();
        $this->modeloProducto = new Producto();
        $this->modeloVenta = new Venta();
    }

    /**
     * Muestra la pantalla para registrar una nueva venta
     */
    public function index(): void {
        // Solo productos activos y con existencia disponible (> 0)
        $todosProductos = $this->modeloProducto->listarTodos('', '', 'activo');
        $productosDisponibles = array_filter($todosProductos, function($p) {
            return (int)$p['cantidad'] > 0;
        });

        $siguienteNumeroVenta = $this->modeloVenta->generarNumeroVenta();

        $tituloPagina = 'Registrar Venta - San Gabriel';
        $menuActivo = 'ventas';

        require_once __DIR__ . '/../views/ventas/index.php';
    }

    /**
     * Procesa y almacena la venta con transacción atómica
     */
    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?c=ventas');
        }

        $cliente = sanitize($_POST['cliente'] ?? 'Cliente Mostrador');
        $notas = sanitize($_POST['notas'] ?? '');
        $fecha = !empty($_POST['fecha']) ? sanitize($_POST['fecha']) : date('Y-m-d H:i:s');
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        // Recibir los items de venta enviados desde el formulario
        // Pueden venir en forma de array: productos_id[], cantidades[]
        $productosIds = $_POST['producto_id'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];

        if (empty($productosIds) || !is_array($productosIds)) {
            set_flash('danger', 'Debes agregar al menos un producto a la venta.');
            redirect('index.php?c=ventas');
        }

        $items = [];
        for ($i = 0; $i < count($productosIds); $i++) {
            $pId = intval($productosIds[$i]);
            $cant = intval($cantidades[$i] ?? 0);

            if ($pId > 0 && $cant > 0) {
                $items[] = [
                    'producto_id' => $pId,
                    'cantidad'    => $cant
                ];
            }
        }

        if (empty($items)) {
            set_flash('danger', 'Debe seleccionar productos válidos y cantidades mayores a cero.');
            redirect('index.php?c=ventas');
        }

        $datosVenta = [
            'cliente'    => $cliente,
            'notas'      => $notas,
            'fecha'      => $fecha,
            'usuario_id' => $usuarioId
        ];

        try {
            $ventaId = $this->modeloVenta->registrarVenta($datosVenta, $items);
            set_flash('success', "¡Venta registrada con éxito! El inventario fue actualizado automáticamente.");
            redirect('index.php?c=historial&a=detalle&id=' . $ventaId);
        } catch (Exception $e) {
            set_flash('danger', 'Error al procesar la venta: ' . $e->getMessage());
            redirect('index.php?c=ventas');
        }
    }
}

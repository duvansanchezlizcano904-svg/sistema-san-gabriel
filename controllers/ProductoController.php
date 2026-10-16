<?php
/**
 * Controlador de Gestión de Productos
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Producto.php';

class ProductoController {
    private Producto $modeloProducto;

    public function __construct() {
        require_admin();
        $this->modeloProducto = new Producto();
    }

    /**
     * Listado principal de productos con filtros
     */
    public function index(): void {
        $busqueda = sanitize($_GET['q'] ?? '');
        $tipo = sanitize($_GET['tipo'] ?? '');
        $estado = sanitize($_GET['estado'] ?? '');

        $productos = $this->modeloProducto->listarTodos($busqueda, $tipo, $estado);
        $tiposDisponibles = $this->modeloProducto->obtenerTipos();

        $tituloPagina = 'Gestión de Productos - San Gabriel';
        $menuActivo = 'productos';

        require_once __DIR__ . '/../views/productos/index.php';
    }

    /**
     * Muestra formulario para registrar un nuevo producto
     */
    public function crear(): void {
        $tiposDisponibles = $this->modeloProducto->obtenerTipos();
        $tituloPagina = 'Registrar Nuevo Producto - San Gabriel';
        $menuActivo = 'productos';

        require_once __DIR__ . '/../views/productos/crear.php';
    }

    /**
     * Procesa el guardado del nuevo producto
     */
    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?c=productos');
        }

        $codigo = strtoupper(sanitize($_POST['codigo'] ?? ''));
        $nombre = sanitize($_POST['nombre'] ?? '');
        $tipo = sanitize($_POST['tipo'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $precioCompra = floatval($_POST['precio_compra'] ?? 0);
        $precioVenta = floatval($_POST['precio_venta'] ?? 0);
        $cantidad = intval($_POST['cantidad'] ?? 0);
        $stockMinimo = intval($_POST['stock_minimo'] ?? 5);
        $unidadMedida = sanitize($_POST['unidad_medida'] ?? 'Kilogramo');
        $estado = in_array($_POST['estado'] ?? '', ['activo', 'inactivo']) ? $_POST['estado'] : 'activo';

        // Validaciones
        $errores = [];
        if (empty($codigo)) $errores[] = "El código del producto es obligatorio.";
        if (empty($nombre)) $errores[] = "El nombre del producto es obligatorio.";
        if (empty($tipo)) $errores[] = "El tipo de producto es obligatorio.";
        if ($precioVenta <= 0) $errores[] = "El precio de venta debe ser mayor a cero.";
        if ($precioCompra < 0) $errores[] = "El precio de compra no puede ser negativo.";
        if ($cantidad < 0) $errores[] = "La cantidad inicial no puede ser negativa.";
        if ($stockMinimo < 0) $errores[] = "El stock mínimo no puede ser negativo.";

        if ($this->modeloProducto->existeCodigo($codigo)) {
            $errores[] = "Ya existe un producto con el código '{$codigo}'.";
        }

        if (!empty($errores)) {
            set_flash('danger', implode('<br>', $errores));
            redirect('index.php?c=productos&a=crear');
        }

        $datos = [
            'codigo'        => $codigo,
            'nombre'        => $nombre,
            'tipo'          => $tipo,
            'descripcion'   => $descripcion,
            'precio_compra' => $precioCompra,
            'precio_venta'  => $precioVenta,
            'cantidad'      => $cantidad,
            'stock_minimo'  => $stockMinimo,
            'unidad_medida' => $unidadMedida,
            'estado'        => $estado
        ];

        try {
            $this->modeloProducto->crear($datos);
            set_flash('success', "¡Producto '{$nombre}' registrado exitosamente!");
            redirect('index.php?c=productos');
        } catch (Exception $e) {
            set_flash('danger', "Error al registrar el producto: " . $e->getMessage());
            redirect('index.php?c=productos&a=crear');
        }
    }

    /**
     * Muestra formulario para editar un producto
     */
    public function editar(): void {
        $id = intval($_GET['id'] ?? 0);
        $producto = $this->modeloProducto->obtenerPorId($id);

        if (!$producto) {
            set_flash('danger', 'El producto solicitado no existe.');
            redirect('index.php?c=productos');
        }

        $tiposDisponibles = $this->modeloProducto->obtenerTipos();
        $tituloPagina = 'Editar Producto - ' . htmlspecialchars($producto['nombre']);
        $menuActivo = 'productos';

        require_once __DIR__ . '/../views/productos/editar.php';
    }

    /**
     * Procesa la actualización del producto
     */
    public function actualizar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?c=productos');
        }

        $id = intval($_POST['id'] ?? 0);
        $productoExistente = $this->modeloProducto->obtenerPorId($id);

        if (!$productoExistente) {
            set_flash('danger', 'El producto que intentas actualizar no existe.');
            redirect('index.php?c=productos');
        }

        $codigo = strtoupper(sanitize($_POST['codigo'] ?? ''));
        $nombre = sanitize($_POST['nombre'] ?? '');
        $tipo = sanitize($_POST['tipo'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $precioCompra = floatval($_POST['precio_compra'] ?? 0);
        $precioVenta = floatval($_POST['precio_venta'] ?? 0);
        $cantidad = intval($_POST['cantidad'] ?? 0);
        $stockMinimo = intval($_POST['stock_minimo'] ?? 5);
        $unidadMedida = sanitize($_POST['unidad_medida'] ?? 'Kilogramo');
        $estado = in_array($_POST['estado'] ?? '', ['activo', 'inactivo']) ? $_POST['estado'] : 'activo';

        $errores = [];
        if (empty($codigo)) $errores[] = "El código es obligatorio.";
        if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
        if (empty($tipo)) $errores[] = "El tipo de producto es obligatorio.";
        if ($precioVenta <= 0) $errores[] = "El precio de venta debe ser mayor a cero.";
        if ($precioCompra < 0) $errores[] = "El precio de compra no puede ser negativo.";
        if ($cantidad < 0) $errores[] = "La cantidad disponible no puede ser negativa.";
        if ($stockMinimo < 0) $errores[] = "El stock mínimo no puede ser negativo.";

        if ($this->modeloProducto->existeCodigo($codigo, $id)) {
            $errores[] = "El código '{$codigo}' ya está asignado a otro producto.";
        }

        if (!empty($errores)) {
            set_flash('danger', implode('<br>', $errores));
            redirect('index.php?c=productos&a=editar&id=' . $id);
        }

        $datos = [
            'codigo'        => $codigo,
            'nombre'        => $nombre,
            'tipo'          => $tipo,
            'descripcion'   => $descripcion,
            'precio_compra' => $precioCompra,
            'precio_venta'  => $precioVenta,
            'cantidad'      => $cantidad,
            'stock_minimo'  => $stockMinimo,
            'unidad_medida' => $unidadMedida,
            'estado'        => $estado
        ];

        try {
            $this->modeloProducto->actualizar($id, $datos);
            set_flash('success', "¡Producto '{$nombre}' actualizado correctamente!");
            redirect('index.php?c=productos');
        } catch (Exception $e) {
            set_flash('danger', "Error al actualizar producto: " . $e->getMessage());
            redirect('index.php?c=productos&a=editar&id=' . $id);
        }
    }

    /**
     * Elimina un producto previa confirmación
     */
    public function eliminar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?c=productos');
        }

        $id = intval($_POST['id'] ?? 0);

        try {
            $this->modeloProducto->eliminar($id);
            set_flash('success', "Producto eliminado correctamente del catálogo.");
        } catch (Exception $e) {
            set_flash('danger', $e->getMessage());
        }

        redirect('index.php?c=productos');
    }
}

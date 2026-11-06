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
     * Listado principal de productos con filtros y paginación
     */
    public function index(): void {
        $busqueda = sanitize($_GET['q'] ?? '');
        $tipo = sanitize($_GET['tipo'] ?? '');
        $estado = sanitize($_GET['estado'] ?? '');
        
        // Parámetros de paginación
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $porPagina = 10; // Productos por página

        // Obtener productos con paginación
        $resultado = $this->modeloProducto->listarConPaginacion(
            $pagina,
            $porPagina,
            $busqueda,
            $tipo,
            $estado
        );
        
        $productos = $resultado['productos'];
        $totalProductos = $resultado['total'];
        $totalPaginas = ceil($totalProductos / $porPagina);
        
        $tiposDisponibles = $this->modeloProducto->obtenerTipos();

        $tituloPagina = 'Gestión de Productos - San Gabriel';
        $menuActivo = 'productos';
        
        // Variables para la vista
        $busquedaActual = $busqueda;
        $tipoActual = $tipo;
        $estadoActual = $estado;

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
        $categoria = sanitize($_POST['categoria'] ?? '');
        $tipoLeche = sanitize($_POST['tipo_leche'] ?? '');
        $intensidad = in_array($_POST['intensidad_sabor'] ?? '', ['suave','medio','fuerte','muy fuerte'])
                      ? $_POST['intensidad_sabor'] : 'medio';
        $tiempoMaduracion = sanitize($_POST['tiempo_maduracion'] ?? '');
        $pesoPresentacion = sanitize($_POST['peso_presentacion'] ?? '');
        $origen = sanitize($_POST['origen'] ?? '');
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

        // Procesar imagen (opcional)
        $rutaImagen = $this->procesarImagen($codigo, $errores);
        if (!empty($errores)) {
            set_flash('danger', implode('<br>', $errores));
            redirect('index.php?c=productos&a=crear');
        }

        $datos = [
            'codigo'             => $codigo,
            'nombre'             => $nombre,
            'tipo'               => $tipo,
            'categoria'          => $categoria ?: null,
            'tipo_leche'         => $tipoLeche ?: null,
            'intensidad_sabor'   => $intensidad,
            'tiempo_maduracion'  => $tiempoMaduracion ?: null,
            'peso_presentacion'  => $pesoPresentacion ?: null,
            'origen'             => $origen ?: null,
            'descripcion'        => $descripcion,
            'imagen'             => $rutaImagen,
            'precio_compra'      => $precioCompra,
            'precio_venta'       => $precioVenta,
            'cantidad'           => $cantidad,
            'stock_minimo'       => $stockMinimo,
            'unidad_medida'      => $unidadMedida,
            'estado'             => $estado,
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
        $categoria = sanitize($_POST['categoria'] ?? '');
        $tipoLeche = sanitize($_POST['tipo_leche'] ?? '');
        $intensidad = in_array($_POST['intensidad_sabor'] ?? '', ['suave','medio','fuerte','muy fuerte'])
                      ? $_POST['intensidad_sabor'] : 'medio';
        $tiempoMaduracion = sanitize($_POST['tiempo_maduracion'] ?? '');
        $pesoPresentacion = sanitize($_POST['peso_presentacion'] ?? '');
        $origen = sanitize($_POST['origen'] ?? '');
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

        // Procesar imagen (solo si se sube una nueva)
        $datos = [
            'codigo'             => $codigo,
            'nombre'             => $nombre,
            'tipo'               => $tipo,
            'categoria'          => $categoria ?: null,
            'tipo_leche'         => $tipoLeche ?: null,
            'intensidad_sabor'   => $intensidad,
            'tiempo_maduracion'  => $tiempoMaduracion ?: null,
            'peso_presentacion'  => $pesoPresentacion ?: null,
            'origen'             => $origen ?: null,
            'descripcion'        => $descripcion,
            'precio_compra'      => $precioCompra,
            'precio_venta'       => $precioVenta,
            'cantidad'           => $cantidad,
            'stock_minimo'       => $stockMinimo,
            'unidad_medida'      => $unidadMedida,
            'estado'             => $estado,
        ];

        // Si se sube archivo, procesar y sobreescribir imagen
        if (!empty($_FILES['imagen']['name'])) {
            $rutaImagen = $this->procesarImagen($codigo, $errores);
            if (!empty($errores)) {
                set_flash('danger', implode('<br>', $errores));
                redirect('index.php?c=productos&a=editar&id=' . $id);
            }
            $datos['imagen'] = $rutaImagen;
        }
        // Si no hay archivo nuevo, NO se incluye 'imagen' en $datos →
        // El modelo mantiene el valor existente (ver Producto::actualizar)

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
     * Alterna o asigna el estado del producto (Borrado lógico HU-07)
     */
    public function cambiarEstado(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?c=productos');
        }

        $id = intval($_POST['id'] ?? 0);
        $nuevoEstado = sanitize($_POST['estado'] ?? '');

        if ($id <= 0 || !in_array($nuevoEstado, ['activo', 'inactivo'], true)) {
            set_flash('danger', 'Parámetros inválidos para cambiar estado.');
            redirect('index.php?c=productos');
        }

        $producto = $this->modeloProducto->obtenerPorId($id);
        if (!$producto) {
            set_flash('danger', 'El producto no existe.');
            redirect('index.php?c=productos');
        }

        try {
            $this->modeloProducto->cambiarEstado($id, $nuevoEstado);
            $mensaje = ($nuevoEstado === 'activo')
                ? "El producto '{$producto['nombre']}' fue reactivado y está disponible para la venta."
                : "El producto '{$producto['nombre']}' fue desactivado (borrado lógico). Ya no saldrá en ventas.";
            set_flash('success', $mensaje);
        } catch (Exception $e) {
            set_flash('danger', "Error al cambiar estado: " . $e->getMessage());
        }

        redirect('index.php?c=productos');
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

    /**
     * Procesa el archivo de imagen subido por el formulario.
     * Valida tipo MIME real y tamaño (≤ 2 MB). Guarda en public/img/productos/.
     *
     * @param  string $codigo   Código del producto (usado como nombre de archivo).
     * @param  array  &$errores Array de errores al que se añaden los de imagen.
     * @return string|null      Ruta relativa lista para guardar en BD, o null.
     */
    private function procesarImagen(string $codigo, array &$errores): ?string {
        if (empty($_FILES['imagen']['name']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // No se subió imagen, sin error
        }

        $uploadError = $_FILES['imagen']['error'];
        if ($uploadError !== UPLOAD_ERR_OK) {
            $errores[] = "Error al subir la imagen (código {$uploadError}).";
            return null;
        }

        // Validar tamaño (≤ 2 MB)
        $maxSize = 2 * 1024 * 1024;
        if ($_FILES['imagen']['size'] > $maxSize) {
            $errores[] = "La imagen no puede superar los 2 MB.";
            return null;
        }

        // Validar tipo MIME real (no confiar solo en la extensión)
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeReal, $tiposPermitidos, true)) {
            $errores[] = "Tipo de imagen no permitido. Usa JPG, PNG o WEBP.";
            return null;
        }

        $extension = match($mimeReal) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        // Nombre de archivo seguro: solo alfanuméricos, guión y guión bajo
        $nombreSeguro  = preg_replace('/[^a-zA-Z0-9\-_]/', '_', strtolower($codigo));
        $nombreArchivo = $nombreSeguro . '.' . $extension;

        $dirDestino = __DIR__ . '/../public/img/productos/';
        if (!is_dir($dirDestino)) {
            mkdir($dirDestino, 0755, true);
        }

        $rutaCompleta = $dirDestino . $nombreArchivo;
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
            $errores[] = "No se pudo guardar la imagen. Verifica permisos del servidor.";
            return null;
        }

        return 'public/img/productos/' . $nombreArchivo;
    }
}

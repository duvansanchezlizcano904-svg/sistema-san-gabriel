<?php
/*
╔══════════════════════════════════════════════════════════════════════════════╗
║           GUÍA DE APRENDIZAJE — QUESOS SAN GABRIEL                         ║
║           ARCHIVO 03: AGREGAR PRODUCTOS (ADMINISTRADOR)                     ║
╚══════════════════════════════════════════════════════════════════════════════╝

¿QUÉ ES "AGREGAR PRODUCTOS"?
─────────────────────────────
Es el proceso mediante el cual el ADMINISTRADOR registra nuevos quesos
en el sistema. Solo el administrador puede hacerlo (los clientes no pueden).

¿CÓMO FUNCIONA EN ESTE PROYECTO?
─────────────────────────────────
  ADMINISTRADOR abre el formulario de nuevo producto (views/productos/crear.php)
       ↓
  Llena todos los campos: código, nombre, tipo, precio, stock, etc.
  (Opcionalmente sube una foto del queso)
       ↓
  El formulario envía los datos a ProductoController::guardar()
       ↓
  El controlador valida los datos y procesa la imagen (si hay)
       ↓
  El modelo Producto::crear() inserta el queso en MySQL
       ↓
  El administrador ve el producto en la lista del catálogo


╔══════════════════════════════════════════════════════════════════════════════╗
║  ARCHIVO PRINCIPAL: controllers/ProductoController.php                      ║
║  FUNCIONES: crear() → muestra el formulario                                 ║
║             guardar() → procesa y guarda en MySQL                           ║
╚══════════════════════════════════════════════════════════════════════════════╝
*/

// ─── PASO 1: SEGURIDAD — Solo el Administrador puede entrar ──────────────────
/*
    public function __construct() {
        require_admin();   ← Esta función verifica que sea administrador
        $this->modeloProducto = new Producto();
    }
*/

// 💡 require_admin() está en config/helpers.php:
//    - Llama a require_auth() para verificar que hay sesión activa.
//    - Luego verifica que el rol sea 'administrador'.
//    - Si no es admin → redirige al catálogo con mensaje de error.


// ─── PASO 2: MOSTRAR EL FORMULARIO ───────────────────────────────────────────
/*
    public function crear(): void {
        $tiposDisponibles = $this->modeloProducto->obtenerTipos();
        $tituloPagina = 'Registrar Nuevo Producto - San Gabriel';
        $menuActivo = 'productos';
        require_once __DIR__ . '/../views/productos/crear.php';
    }
*/

// 💡 Esta función solo MUESTRA el formulario.
//    Carga los "tipos" existentes de productos para mostrarlos en un <select>.
//    El formulario HTML está en views/productos/crear.php


// ─── PASO 3: PROCESAR EL FORMULARIO (GUARDAR) ────────────────────────────────
/*
    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?c=productos');
        }
*/

// 💡 Si alguien intenta acceder a guardar() sin enviar un formulario,
//    lo redirige a la lista de productos. Medida de seguridad básica.


// ─── PASO 4: OBTENER Y LIMPIAR TODOS LOS CAMPOS ──────────────────────────────
/*
    $codigo    = strtoupper(sanitize($_POST['codigo'] ?? ''));
    $nombre    = sanitize($_POST['nombre'] ?? '');
    $tipo      = sanitize($_POST['tipo'] ?? '');
    $categoria = sanitize($_POST['categoria'] ?? '');
    $intensidad = in_array($_POST['intensidad_sabor'] ?? '', ['suave','medio','fuerte','muy fuerte'])
                  ? $_POST['intensidad_sabor'] : 'medio';
    $precioVenta  = floatval($_POST['precio_venta'] ?? 0);
    $precioCompra = floatval($_POST['precio_compra'] ?? 0);
    $cantidad     = intval($_POST['cantidad'] ?? 0);
    $stockMinimo  = intval($_POST['stock_minimo'] ?? 5);
    $estado = in_array($_POST['estado'] ?? '', ['activo', 'inactivo']) ? $_POST['estado'] : 'activo';
*/

// 💡 FUNCIONES DE SANITIZACIÓN Y CONVERSIÓN:
//    sanitize()   → elimina caracteres peligrosos para XSS (htmlspecialchars)
//    strtoupper() → convierte a mayúsculas (el código del producto: QFR-001)
//    floatval()   → convierte a número decimal (para precios: 15.000,50)
//    intval()     → convierte a número entero (para cantidades: 10)
//    in_array()   → valida que el valor esté en una lista permitida


// ─── PASO 5: VALIDACIONES ────────────────────────────────────────────────────
/*
    $errores = [];

    if (empty($codigo))          $errores[] = "El código del producto es obligatorio.";
    if (empty($nombre))          $errores[] = "El nombre del producto es obligatorio.";
    if (empty($tipo))            $errores[] = "El tipo de producto es obligatorio.";
    if ($precioVenta <= 0)       $errores[] = "El precio de venta debe ser mayor a cero.";
    if ($precioCompra < 0)       $errores[] = "El precio de compra no puede ser negativo.";
    if ($cantidad < 0)           $errores[] = "La cantidad inicial no puede ser negativa.";
    if ($stockMinimo < 0)        $errores[] = "El stock mínimo no puede ser negativo.";

    // Verificar que el código no esté repetido en MySQL
    if ($this->modeloProducto->existeCodigo($codigo)) {
        $errores[] = "Ya existe un producto con el código '{$codigo}'.";
    }

    if (!empty($errores)) {
        set_flash('danger', implode('<br>', $errores));
        redirect('index.php?c=productos&a=crear');
    }
*/

// 💡 El array $errores acumula todos los problemas encontrados.
//    Al final, si hay errores, los muestra TODOS juntos (no uno por uno).
//    implode('<br>', $errores) une los mensajes con saltos de línea HTML.


// ─── PASO 6: PROCESAR IMAGEN (OPCIONAL) ──────────────────────────────────────
/*
    private function procesarImagen(string $codigo, array &$errores): ?string {

        // 1. Si no se subió imagen, retornar null (es opcional)
        if (empty($_FILES['imagen']['name'])) {
            return null;
        }

        // 2. Validar tamaño máximo (2 MB = 2 * 1024 * 1024 bytes)
        $maxSize = 2 * 1024 * 1024;
        if ($_FILES['imagen']['size'] > $maxSize) {
            $errores[] = "La imagen no puede superar los 2 MB.";
            return null;
        }

        // 3. Validar tipo de archivo REAL (no confiar en la extensión)
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeReal, $tiposPermitidos, true)) {
            $errores[] = "Tipo de imagen no permitido. Usa JPG, PNG o WEBP.";
            return null;
        }

        // 4. Crear nombre seguro y mover el archivo
        $nombreSeguro  = preg_replace('/[^a-zA-Z0-9\-_]/', '_', strtolower($codigo));
        $nombreArchivo = $nombreSeguro . '.jpg';   // Ej: qfr_001.jpg
        $dirDestino    = __DIR__ . '/../public/img/productos/';
        move_uploaded_file($_FILES['imagen']['tmp_name'], $dirDestino . $nombreArchivo);

        return 'public/img/productos/' . $nombreArchivo;
    }
*/

// 💡 $_FILES es el array de PHP con los archivos subidos por el formulario.
// 💡 finfo_file() verifica el tipo REAL del archivo (no la extensión del nombre).
//    Un hacker podría renombrar "virus.exe" como "foto.jpg" → finfo lo detecta.
// 💡 move_uploaded_file() mueve el archivo temporal al directorio definitivo.


// ─── PASO 7: GUARDAR EN MYSQL ────────────────────────────────────────────────
/*
    // En models/Producto.php, la función crear():
    
    public function crear(array $datos): bool {
        $sql = "INSERT INTO productos 
                (codigo, nombre, tipo, categoria, tipo_leche, intensidad_sabor,
                 tiempo_maduracion, peso_presentacion, origen, descripcion,
                 imagen, precio_compra, precio_venta, cantidad, stock_minimo,
                 unidad_medida, estado)
                VALUES
                (:codigo, :nombre, :tipo, :categoria, :tipo_leche, :intensidad_sabor,
                 :tiempo_maduracion, :peso_presentacion, :origen, :descripcion,
                 :imagen, :precio_compra, :precio_venta, :cantidad, :stock_minimo,
                 :unidad_medida, :estado)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([...todos los parámetros...]);
    }
*/


// ─── TABLA DE PRODUCTOS EN MYSQL ─────────────────────────────────────────────
/*

  TABLA: productos
  ┌──────────────────┬────────────┬──────────────────────────────────────────┐
  │ columna          │ tipo       │ descripción                              │
  ├──────────────────┼────────────┼──────────────────────────────────────────┤
  │ id               │ INT PK     │ Identificador automático                 │
  │ codigo           │ VARCHAR    │ Código único: QFR-001, QMA-002, etc.     │
  │ nombre           │ VARCHAR    │ Nombre del queso: "Queso Fresco Artesanal"│
  │ tipo             │ VARCHAR    │ Categoría general: "Queso", "Leche", etc.│
  │ categoria        │ VARCHAR    │ Subcategoría: "Quesos Frescos", etc.     │
  │ tipo_leche       │ VARCHAR    │ "Vaca", "Cabra", "Oveja", "Mixta"        │
  │ intensidad_sabor │ VARCHAR    │ "suave", "medio", "fuerte", "muy fuerte" │
  │ tiempo_maduracion│ VARCHAR    │ "Sin maduración", "6 meses", etc.        │
  │ peso_presentacion│ VARCHAR    │ "500g", "1kg", etc.                      │
  │ origen           │ VARCHAR    │ Región o país de origen                  │
  │ descripcion      │ TEXT       │ Descripción detallada del queso          │
  │ imagen           │ VARCHAR    │ Ruta a la imagen: "public/img/..."       │
  │ precio_compra    │ DECIMAL    │ Costo de adquisición (no visible al pub.)│
  │ precio_venta     │ DECIMAL    │ Precio que paga el cliente               │
  │ cantidad         │ INT        │ Stock actual disponible                  │
  │ stock_minimo     │ INT        │ Alerta cuando baje de este número        │
  │ unidad_medida    │ VARCHAR    │ "Kilogramo", "Unidad", "Gramo"           │
  │ estado           │ VARCHAR    │ "activo" o "inactivo"                    │
  │ creado_en        │ TIMESTAMP  │ Fecha de registro                        │
  └──────────────────┴────────────┴──────────────────────────────────────────┘

*/


// ─── RUTA COMPLETA EN EL SISTEMA ─────────────────────────────────────────────
/*

  URL para ver el formulario:  index.php?c=productos&a=crear
  URL para guardar (POST):     index.php?c=productos&a=guardar

  ARCHIVOS INVOLUCRADOS:
  ├── index.php                     → Enrutador principal
  ├── controllers/
  │   └── ProductoController.php   → crear() y guardar()
  ├── models/
  │   └── Producto.php             → crear() → INSERT en MySQL
  ├── views/
  │   └── productos/
  │       └── crear.php            → Formulario HTML
  └── public/
      └── img/
          └── productos/           → Donde se guardan las imágenes subidas

*/


// ─── RESUMEN PARA PRINCIPIANTES ─────────────────────────────────────────────
/*

  📌 CONCEPTOS QUE APRENDISTE AQUÍ:

  1. FORMULARIOS MULTIPART:
     - Para subir archivos, el formulario HTML debe tener:
       enctype="multipart/form-data"
     - Sin esto, PHP no recibe el archivo.

  2. VALIDACIÓN DE ARCHIVOS:
     - Verifica el tipo MIME real (no solo la extensión del nombre).
     - Limita el tamaño máximo.
     - Usa move_uploaded_file() para guardar de forma segura.

  3. CONTROL DE ACCESO:
     - require_admin() en el constructor bloquea a cualquiera que no sea admin.
     - Esto se verifica EN EL SERVIDOR, no solo en el HTML.

  4. SANITIZACIÓN:
     - sanitize() previene XSS (Cross-Site Scripting).
     - floatval() e intval() aseguran el tipo correcto de datos numéricos.

  5. CÓDIGOS DE PRODUCTO:
     - Formato: QFR-001 (Q=Queso, FR=Fresco, 001=número)
     - strtoupper() asegura que siempre sean mayúsculas.
     - Verificación de duplicados antes de guardar.

*/

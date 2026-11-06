# Módulo Productos

Este módulo permite a los administradores gestionar el catálogo de quesos: crear, editar, cambiar estado y eliminar productos.

## ¿Quién lo usa?

- **Administradores** exclusivamente. Los clientes solo pueden ver el catálogo, no modificarlo.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Listar productos | `index.php?c=productos` | GET | Sí (admin) |
| Mostrar formulario crear | `index.php?c=productos&a=crear` | GET | Sí (admin) |
| Guardar producto nuevo | `index.php?c=productos&a=guardar` | POST | Sí (admin) |
| Mostrar formulario editar | `index.php?c=productos&a=editar&id=XXX` | GET | Sí (admin) |
| Actualizar producto | `index.php?c=productos&a=actualizar&id=XXX` | POST | Sí (admin) |
| Cambiar estado | `index.php?c=productos&a=cambiarEstado&id=XXX&estado=XXX` | GET | Sí (admin) |
| Eliminar producto | `index.php?c=productos&a=eliminar&id=XXX` | GET | Sí (admin) |

## Archivos que lo componen

- **Controlador**: `controllers/ProductoController.php`
- **Modelo**: `models/Producto.php`
- **Vistas**:
  - `views/productos/index.php`
  - `views/productos/crear.php`
  - `views/productos/editar.php`

## Métodos del controlador

### index()
Lista todos los productos con filtros por búsqueda, tipo y estado. Usa `Producto->listarTodos()`.

### crear()
Muestra el formulario para registrar un nuevo queso. Carga la lista de tipos disponibles para el dropdown.

### guardar()
Recibe los datos del formulario POST. Valida que el código no exista (`existeCodigo`), que los precios y cantidades sean números positivos. Llama a `Producto->crear()` y redirige al listado con mensaje de éxito.

### editar()
Carga los datos del producto por su ID y muestra el formulario de edición.

### actualizar()
Procesa los cambios del formulario, validando igual que guardar pero permitiendo el ID actual.

### cambiarEstado()
Alterna el estado entre 'activo' e 'inactivo' (borrado lógico). Usa `Producto->cambiarEstado()`.

### eliminar()
Intenta borrar el producto físicamente. Si tiene ventas asociadas, lanza excepción y muestra mensaje de error. Solo permite borrado físico si no hay historial de ventas.

## Validaciones del servidor

- Código único: no puede existir otro producto con el mismo código.
- Todos los campos obligatorios: código, nombre, tipo, precio_compra, precio_venta, cantidad, stock_minimo, unidad_medida.
- Valores numéricos positivos para precios y cantidades.
- No se puede eliminar un producto que tenga ventas registradas.

## Cómo se protege

- `require_admin()` al inicio de cada método.

## Tablas de la base de datos

### productos
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT (PK) | Identificador |
| codigo | VARCHAR(30) UNIQUE | Código del producto |
| nombre | VARCHAR(120) | Nombre del queso |
| tipo | VARCHAR(60) | Tipo (Queso Fresco, Maduro, etc.) |
| categoria | VARCHAR(60) | Categoría para filtros |
| tipo_leche | VARCHAR(60) | Vaca, Cabra, Oveja... |
| intensidad_sabor | ENUM | suave, medio, fuerte, muy fuerte |
| tiempo_maduracion | VARCHAR(60) | Tiempo de maduración |
| peso_presentacion | VARCHAR(60) | Presentación del producto |
| origen | VARCHAR(100) | Región de origen |
| descripcion | TEXT | Descripción del producto |
| imagen_url | VARCHAR(255) | URL externa de imagen |
| imagen | VARCHAR(255) | Ruta de imagen local |
| precio_compra | DECIMAL(10,2) | Precio de costo |
| precio_venta | DECIMAL(10,2) | Precio de venta |
| cantidad | INT | Stock actual |
| stock_minimo | INT | Umbral de alerta |
| unidad_medida | VARCHAR(30) | kg, unidad, libra... |
| estado | ENUM | activo, inactivo |
| creado_en | TIMESTAMP | Fecha de creación |

## Ejemplo de código del modelo

```php
// Crear un nuevo producto
$producto = new Producto();
$ok = $producto->crear([
    'codigo' => 'LAC-011',
    'nombre' => 'Queso Azul Premium',
    'tipo' => 'Queso Azul',
    'categoria' => 'Quesos Azules',
    'tipo_leche' => 'Vaca',
    'intensidad_sabor' => 'muy fuerte',
    'precio_compra' => 18000,
    'precio_venta' => 25000,
    'cantidad' => 15,
    'stock_minimo' => 5,
    'unidad_medida' => 'Kilogramo',
    'estado' => 'activo'
]);
```

## Cómo probarlo

1. Iniciar sesión como admin: `admin` / `admin123`
2. Ir a `index.php?c=productos`
3. Ver el listado de productos. Hacer clic en "Crear Nuevo".
4. Llenar el formulario y guardar. Verificar que aparece en el listado.
5. Probar editar un producto existente.
6. Probar cambiar el estado (activer/inactivar).
7. Intentar eliminar un producto que tenga ventas (debe mostrar error).
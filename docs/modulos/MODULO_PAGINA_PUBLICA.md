# Módulo Página Pública

Este módulo muestra contenido a visitantes sin necesidad de iniciar sesión. Es la cara visible del negocio.

## ¿Quién lo usa?

- **Visitantes** sin sesión. Cualquier persona puede acceder.
- **Clientes** que aún no se han autenticado.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Página de inicio | `index.php` (sin parámetros) | GET | No |
| Catálogo público | `publico.php` | GET | No |
| Catálogo público (ruta alternativa) | `index.php?c=publico&a=index` | GET | No |
| Cargar más productos (AJAX) | `index.php?c=publico&a=cargarMasProductos` | GET | No |

## Archivos que lo compõem

- **Controlador**: `controllers/PublicoController.php`
- **Modelo**: `models/Producto.php`
- **Vistas**:
  - `views/inicio/home.php`
  - `views/publico/catalogo.php`
  - `views/partials/tarjeta_producto.php`

## Métodos del controlador

### inicio()
Muestra la página de bienvenida (landing page) con información de la quesería y un catálogo destacado. Carga productos de ejemplo si la base de datos está vacía o falla.

### index()
Muestra el catálogo público completo con filtros avanzados: búsqueda por texto, filtro por tipo, categoría, tipo de leche e intensidad de sabor. Muestra productos activos independientemente de la sesión.

### cargarMasProductos()
Endpoint AJAX para carga infinita ("Cargar más" button). Recibe parámetros de paginación y filtros, devuelve JSON con HTML de productos, total, página actual y si hay más páginas.

## Filtros disponibles en el catálogo público

- Búsqueda por nombre, código o descripción.
- Filtrar por tipo de queso.
- Filtrar por categoría.
- Filtrar por tipo de leche.
- Filtrar por intensidad de sabor.

## Cómo se protege

- No requiere autenticación. Es completamente público.
- El enrutador en `index.php` permite las acciones `inicio`, `index` y `cargarMasProductos` sin sesión.

## Diferencias con el catálogo del cliente autenticado

- El catálogo público muestra productos activos aunque tengan stock 0.
- No permite agregar al carrito directamente (el cliente debe iniciar sesión primero).
- Usa un diseño diferente optimizado para visitantes.

## Tablas de la base de datos

- **productos**: Solo lectura, productos con estado='activo'.

## Cómo probarlo

1. Cerrar sesión si hay una activa.
2. Ir a la raíz del proyecto (`index.php` sin parámetros).
3. Ver la página de bienvenida. Hacer scroll para ver productos.
4. Hacer clic en "Cargar más" para ver más productos.
5. Ir a `publico.php` o `index.php?c=publico&a=index`.
6. Probar los filtros de búsqueda, tipo, categoría.
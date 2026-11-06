# Módulo Catálogo (para clientes)

Este módulo muestra el catálogo de productos a los clientes autenticados.

## ¿Quién lo usa?

- **Clientes** autenticados. Los administradores tienen su propio módulo de productos.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Ver catálogo | `index.php?c=catalogo` | GET | Sí (cliente o admin) |

## Archivos que lo componen

- **Controlador**: `controllers/CatalogoController.php`
- **Modelo**: `models/Producto.php`
- **Vista**: `views/catalogo/index.php`

## Métodos del controlador

### index()
Lista los productos activos con sus precios y disponibilidad. Los clientes pueden ver el catálogo y agregar productos al carrito. Muestra filtros por tipo y búsqueda.

## Cómo se protege

- `require_auth()` al inicio (solo usuarios autenticados).

## Diferencias con el módulo de productos del admin

- El catálogo de clientes es de solo lectura.
- No permite crear, editar, eliminar o cambiar estado de productos.
- Está diseñado para la experiencia de compra del cliente.

## Tablas de la base de datos

- **productos**: Solo lectura, productos con estado='activo' y cantidad > 0.

## Cómo probarlo

1. Iniciar sesión como cliente: `cliente@gabriellacteos.com` / `cliente123`
2. Ir a `index.php?c=catalogo`
3. Ver los productos, buscar, filtrar por tipo.
4. Agregar productos al carrito haciendo clic en el botón de cantidad.
# Módulo Inventario

Este módulo muestra el control de stock de productos con alertas visuales según el nivel de existencias.

## ¿Quién lo usa?

- **Administradores** exclusivamente.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Ver inventario | `index.php?c=inventario` | GET | Sí (admin) |

## Archivos que lo componen

- **Controlador**: `controllers/InventarioController.php`
- **Modelo**: `models/Producto.php`
- **Vista**: `views/inventario/index.php`

## Métodos del控制器

### index()
Carga todos los productos activos y calcula el estado de stock de cada uno usando la función `calcularEstadoStock()`. Muestra una tabla con código, nombre, cantidad actual, stock mínimo, unidad de medida, valor total y un semáforo de colores (verde=disponible, amarillo=stock bajo, rojo=agotado).

## Cómo se protege

- `require_admin()` al inicio del método.

## Tablas de la base de datos

- **productos**: Para obtener cantidades, stock mínimo y precios.

## Lógica del semáforo

```php
function calcularEstadoStock(int $cantidad, int $stockMinimo): array {
    if ($cantidad <= 0) {
        return ['nombre' => 'Agotado', 'clase' => 'badge bg-danger'];
    } elseif ($cantidad <= $stockMinimo) {
        return ['nombre' => 'Stock Bajo', 'clase' => 'badge bg-warning text-dark'];
    } else {
        return ['nombre' => 'Disponible', 'clase' => 'badge bg-success'];
    }
}
```

## Cómo probarlo

1. Iniciar sesión como admin.
2. Ir a `index.php?c=inventario`.
3. Ver los productos con sus indicadores de color. Los productos con cantidad 0 aparecen en rojo, los que están bajo el mínimo en amarillo.
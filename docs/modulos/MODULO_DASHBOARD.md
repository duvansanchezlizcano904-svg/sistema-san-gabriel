# Módulo Dashboard

El Dashboard es el panel principal que muestra una visión general del negocio a los administradores.

## ¿Quién lo usa?

- **Administradores** exclusivamente. Los clientes no tienen acceso a este módulo.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Ver Dashboard | `index.php?c=dashboard` | GET | Sí (admin) |

## Archivos que lo componen

- **Controlador**: `controllers/DashboardController.php`
- **Modelo**: `models/Producto.php`, `models/Venta.php`
- **Vista**: `views/dashboard/index.php`

## Métodos del controlador

### index()
Carga las estadísticas globales de inventario (`Producto->obtenerEstadisticas()`), el resumen de ventas del día (`Venta->obtenerEstadisticas()`), la lista de productos con stock crítico y las últimas 6 ventas registradas. Verifica que el usuario sea administrador usando `require_admin()`.

## Qué muestra el Dashboard

- **Tarjetas de KPIs**: Total de productos, productos con stock bajo, total de ventas, ingresos acumulados.
- **Sección de alertas**: Tabla de productos cuyo stock está bajo el mínimo.
- **Ventas recientes**: Tabla con las últimas transacciones.

## Cómo se protege

- `require_admin()` al inicio del método. Si no es administrador, redirige al catálogo.

## Tablas de la base de datos

- **productos**: Para estadísticas de inventario.
- **ventas**: Para estadísticas de ventas y listado de ventas recientes.
- **detalle_ventas**: Para calcular totales.

## Cómo probarlo

1. Iniciar sesión como administrador: `admin` / `admin123`
2. Observar que la URL muestra `index.php?c=dashboard`
3. Ver las tarjetas con números y la tabla de productos con stock bajo.
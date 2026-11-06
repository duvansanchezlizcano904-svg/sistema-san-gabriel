# Módulo Reportes

Este módulo ofrece análisis financiero y statistics de ventas.

## ¿Quién lo usa?

- **Administradores** exclusivamente.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Ver reportes | `index.php?c=reportes` | GET | Sí (admin) |

## Archivos que lo componen

- **Controlador**: `controllers/ReporteController.php`
- **Modelo**: `models/Venta.php`
- **Vista**: `views/reportes/index.php`

## Métodos del controlador

### index()
Permite seleccionar un rango de fechas. Muestra:
- Total de ingresos en el período.
- Número de transacciones.
- Promedio por venta.
- Desglose diario de ventas.
- Ranking de productos más vendidos (`Venta->obtenerProductosMasVendidos()`).

## Cómo se protege

- `require_admin()` al inicio del método.

## Tablas de la base de datos

- **ventas**: Para estadísticas de ingresos.
- **detalle_ventas**: Para el ranking de productos más vendidos.
- **productos**: Para mostrar los nombres de los productos.

## Cómo probarlo

1. Iniciar sesión como admin.
2. Ir a `index.php?c=reportes`.
3. Seleccionar un rango de fechas.
4. Ver las tarjetas de resumen y el ranking de productos.
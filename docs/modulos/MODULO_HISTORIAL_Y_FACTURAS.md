# Módulo Historial y Facturas

Este módulo permite consultar el historial de ventas y generar comprobantes.

## ¿Quién lo usa?

- **Administradores** exclusivamente.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Ver historial | `index.php?c=historial` | GET | Sí (admin) |
| Ver detalle | `index.php?c=historial&a=detalle&id=XXX` | GET | Sí (admin) |
| Ver tiquete | `index.php?c=historial&a=tiquete&id=XXX` | GET | Sí (admin) |

## Archivos que lo componen

- **Controlador**: `controllers/HistorialController.php`
- **Modelo**: `models/Venta.php`
- **Vistas**:
  - `views/historial/index.php`
  - `views/historial/detalle.php`
  - `views/factura/tiquete.php`

## Métodos del controlador

### index()
Lista todas las ventas con filtros por rango de fechas y búsqueda por cliente o número de venta. Muestra una tabla con fecha, número, cliente, total y acciones.

### detalle()
Muestra el desglose completo de una venta: datos del cliente, productos adquiridos con cantidades y precios, subtotales, total y datos del cajero que atendió.

### tiquete()
Genera un tiquete térmico para impresión. Formato simple optimizado para impresoras POS o papel estándar.

## Cómo se protege

- `require_admin()` al inicio de cada método.

## Tablas de la base de datos

- **ventas**: Para el listado y detalles.
- **detalle_ventas**: Para los productos de cada venta.
- **usuarios**: Para el nombre del cajero.

## Cómo probarlo

1. Registrar una venta en el módulo de ventas.
2. Ir a `index.php?c=historial`.
3. Ver la venta en la lista. Hacer clic en "Ver".
4. Ver el detalle completo. Hacer clic en "Tiquete" para ver el formato de impresión.
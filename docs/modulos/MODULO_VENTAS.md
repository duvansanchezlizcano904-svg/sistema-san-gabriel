# Módulo Ventas (POS)

Este módulo es el punto de venta para registrar transacciones en mostrador.

## ¿Quién lo usa?

- **Administradores** exclusivamente.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Ver panel de ventas | `index.php?c=ventas` | GET | Sí (admin) |
| Procesar venta | `index.php?c=ventas&a=guardar` | POST | Sí (admin) |

## Archivos que lo compone

- **Controlador**: `controllers/VentaController.php`
- **Modelo**: `models/Venta.php`, `models/Producto.php`
- **Vista**: `views/ventas/index.php`

## Métodos del controlador

### index()
Genera el siguiente número correlativo de venta (VNT-00001), carga los productos activos con stock mayor a cero, y muestra el panel de venta dividido en dos columnas: selector de productos a la izquierda y carrito de venta a la derecha.

### guardar()
Procesa la venta enviada desde JavaScript. Ejecuta una transacción atómica que:
1. Valida que haya al menos un ítem.
2. Verifica stock disponible de cada producto.
3. Inserta el encabezado en la tabla `ventas`.
4. Inserta cada línea en `detalle_ventas`.
5. Descuenta el inventario en `productos`.
6. Confirma con `commit` o revierte con `rollBack` si hay error.

## Transacción atómica (código del modelo)

```php
public function registrarVenta(array $datosVenta, array $items): int {
    $this->db->beginTransaction();
    try {
        // Insertar encabezado
        $stmt = $this->db->prepare("INSERT INTO ventas ...");
        $stmt->execute(...);
        $ventaId = $this->db->lastInsertId();
        
        // Insertar detalles y descontar stock
        foreach ($items as $item) {
            $this->db->prepare("INSERT INTO detalle_ventas ...")->execute(...);
            // Descontar stock
            $producto->descontarStock($item['producto_id'], $item['cantidad'], $this->db);
        }
        
        $this->db->commit();
        return $ventaId;
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}
```

## Validaciones del servidor

- Al menos un producto en la venta.
- Cantidad mayor a cero.
- Stock suficiente para cada producto.
- Manejo de errores con rollback para consistencia.

## Cómo se protege

- `require_admin()` al inicio de cada método.

## Tablas de la base de datos

### ventas
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT (PK) | Identificador |
| numero_venta | VARCHAR(30) UNIQUE | Consecutivo (VNT-00001) |
| fecha | DATETIME | Fecha y hora |
| total | DECIMAL(10,2) | Total de la venta |
| cliente | VARCHAR(100) | Nombre del cliente |
| notas | TEXT | Notas adicionales |
| usuario_id | INT (FK) | Administrador que registró |

### detalle_ventas
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT (PK) | Identificador |
| venta_id | INT (FK) | Referencia a ventas |
| producto_id | INT (FK) | Referencia a productos |
| cantidad | INT | Cantidad Vendida |
| precio_unitario | DECIMAL(10,2) | Precio al momento |
| subtotal | DECIMAL(10,2) | Cantidad × Precio |

## Cómo probarlo

1. Iniciar sesión como admin.
2. Ir a `index.php?c=ventas`.
3. Seleccionar productos del dropdown, ajustar cantidades.
4. Ver cómo se actualiza el total en tiempo real.
5. Hacer clic en "Registrar Venta".
6. Verificar que el stock disminuye en el módulo de inventario.
7. Ver la venta en el historial.
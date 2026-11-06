# Esquema de Base de Datos

Este documento detalla cada tabla del sistema, sus campos, relaciones y ejemplos de uso.

## Tablas del sistema

1. [usuarios](#tabla-usuarios)
2. [productos](#tabla-productos)
3. [ventas](#tabla-ventas)
4. [detalle_ventas](#tabla-detalle_ventas)
5. [proveedores](#tabla-proveedores)
6. [pedidos](#tabla-pedidos)
7. [detalle_pedidos](#tabla-detalle_pedidos)

---

## Tabla: usuarios

Almacena administradores y clientes del sistema.

| Campo | Tipo | Restricción | Descripción |
|-------|------|-------------|-------------|
| id | INT | PK, AUTO_INCREMENT | Identificador único |
| nombre | VARCHAR(100) | NOT NULL | Nombre completo |
| usuario | VARCHAR(50) | UNIQUE, NOT NULL | Nombre de usuario para login |
| password | VARCHAR(255) | NOT NULL | Hash de contraseña (bcrypt) |
| email | VARCHAR(100) | NULL | Correo electrónico |
| telefono | VARCHAR(30) | NULL | Teléfono de contacto |
| rol | ENUM | NOT NULL, DEFAULT 'cliente' | 'administrador' o 'cliente' |
| token_recuperacion | VARCHAR(64) | NULL | Token para recuperar contraseña |
| token_expiracion | DATETIME | NULL | Fecha de expiración del token |
| creado_en | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Fecha de creación |

### Relaciones
- **1:N con ventas**: Un usuario puede registrar muchas ventas (como cajero).
- **1:N con pedidos**: Un cliente puede hacer muchos pedidos.

### Ejemplo de uso
```sql
-- Buscar usuario por email
SELECT * FROM usuarios WHERE email = 'cliente@gabriellacteos.com';

-- Validar token de recuperación
SELECT * FROM usuarios 
WHERE token_recuperacion = 'abc123...' 
  AND token_expiracion >= NOW();
```

---

## Tabla: productos

Catálogo de quesos y derivados lácteos.

| Campo | Tipo | Restricción | Descripción |
|-------|------|-------------|-------------|
| id | INT | PK, AUTO_INCREMENT | Identificador único |
| codigo | VARCHAR(30) | UNIQUE, NOT NULL | Código del producto (LAC-001) |
| nombre | VARCHAR(120) | NOT NULL | Nombre del queso |
| tipo | VARCHAR(60) | NOT NULL, INDEX | Tipo de queso |
| categoria | VARCHAR(60) | NULL, INDEX | Categoría principal |
| tipo_leche | VARCHAR(60) | NULL, INDEX | Vaca, Cabra, Oveja, Mezcla |
| intensidad_sabor | ENUM | NULL, INDEX | suave, medio, fuerte, muy fuerte |
| tiempo_maduracion | VARCHAR(60) | NULL | Tiempo de maduración |
| peso_presentacion | VARCHAR(60) | NULL | Presentación del producto |
| origen | VARCHAR(100) | NULL | Región de origen |
| descripcion | TEXT | NULL | Descripción del producto |
| imagen_url | VARCHAR(255) | NULL | URL externa de imagen |
| imagen | VARCHAR(255) | NULL, INDEX | Ruta de imagen local |
| precio_compra | DECIMAL(10,2) | NOT NULL, DEFAULT 0.00 | Precio de costo |
| precio_venta | DECIMAL(10,2) | NOT NULL, DEFAULT 0.00 | Precio de venta |
| cantidad | INT | NOT NULL, DEFAULT 0 | Stock actual |
| stock_minimo | INT | NOT NULL, DEFAULT 5 | Umbral de alerta |
| unidad_medida | VARCHAR(30) | NOT NULL, DEFAULT 'Kilogramo' | kg, unidad, libra |
| estado | ENUM | NOT NULL, DEFAULT 'activo', INDEX | activo, inactivo |
| creado_en | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Fecha de creación |

### Relaciones
- **1:N con detalle_ventas**: Un producto puede estar en muchas ventas (ON DELETE RESTRICT).
- **1:N con detalle_pedidos**: Un producto puede estar en muchos pedidos (ON DELETE RESTRICT).

### Por qué ON DELETE RESTRICT
No permitimos borrar productos que tienen historial de ventas porque:
- Se perdería la integridad del histórico contable.
- Las facturas quedarían incompletas.
- Los reportes de ventas serían inconsistentes.

Si un producto ya no se vende, se cambia su `estado` a 'inactivo' (borrado lógico).

### Ejemplo de uso
```sql
-- Productos con stock bajo
SELECT * FROM productos 
WHERE estado = 'activo' 
  AND cantidad <= stock_minimo 
ORDER BY cantidad ASC;

-- Búsqueda con filtros
SELECT * FROM productos 
WHERE estado = 'activo'
  AND categoria = 'Quesos Frescos'
  AND tipo_leche = 'Vaca'
  AND intensidad_sabor = 'suave';
```

---

## Tabla: ventas

Encabezado de ventas registradas en el punto de venta (POS).

| Campo | Tipo | Restricción | Descripción |
|-------|------|-------------|-------------|
| id | INT | PK, AUTO_INCREMENT | Identificador único |
| numero_venta | VARCHAR(30) | UNIQUE, NOT NULL | Consecutivo (VNT-00001) |
| fecha | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP, INDEX | Fecha y hora |
| total | DECIMAL(10,2) | NOT NULL, DEFAULT 0.00 | Total de la venta |
| cliente | VARCHAR(100) | NULL, DEFAULT 'Cliente Mostrador' | Nombre del cliente |
| notas | TEXT | NULL | Notas adicionales |
| usuario_id | INT | FK → usuarios(id) | Cajero que registró |

### Relaciones
- **N:1 con usuarios**: Muchas ventas pertenecen a un usuario (ON DELETE SET NULL, ON UPDATE CASCADE).
- **1:N con detalle_ventas**: Una venta tiene muchos detalles (ON DELETE CASCADE).

### Por qué ON DELETE SET NULL en usuario_id
Si se elimina un usuario administrador, las ventas históricas no se pierden, solo queda `usuario_id = NULL`. Esto mantiene el registro contable intacto.

### Ejemplo de uso
```sql
-- Ventas del día
SELECT * FROM ventas 
WHERE DATE(fecha) = CURDATE() 
ORDER BY fecha DESC;

-- Ingresos por rango de fechas
SELECT SUM(total) as total_ingresos 
FROM ventas 
WHERE fecha BETWEEN '2026-10-01' AND '2026-10-31';
```

---

## Tabla: detalle_ventas

Productos vendidos en cada transacción.

| Campo | Tipo | Restricción | Descripción |
|-------|------|-------------|-------------|
| id | INT | PK, AUTO_INCREMENT | Identificador único |
| venta_id | INT | FK → ventas(id), NOT NULL | Venta a la que pertenece |
| producto_id | INT | FK → productos(id), NOT NULL | Producto vendido |
| cantidad | INT | NOT NULL | Cantidad vendida |
| precio_unitario | DECIMAL(10,2) | NOT NULL | Precio al momento de venta |
| subtotal | DECIMAL(10,2) | NOT NULL | cantidad × precio_unitario |

### Relaciones
- **N:1 con ventas**: Muchos detalles pertenecen a una venta (ON DELETE CASCADE).
- **N:1 con productos**: Muchos detalles referencian un producto (ON DELETE RESTRICT).

### Por qué guardamos precio_unitario
El precio del producto puede cambiar con el tiempo. Guardamos el precio al momento de la venta para mantener registros contables precisos.

### Ejemplo de uso
```sql
-- Productos de una venta específica
SELECT p.nombre, dv.cantidad, dv.precio_unitario, dv.subtotal
FROM detalle_ventas dv
JOIN productos p ON dv.producto_id = p.id
WHERE dv.venta_id = 15;

-- Productos más vendidos
SELECT p.nombre, SUM(dv.cantidad) as total_vendido
FROM detalle_ventas dv
JOIN productos p ON dv.producto_id = p.id
GROUP BY dv.producto_id
ORDER BY total_vendido DESC
LIMIT 10;
```

---

## Tabla: proveedores

Empresas que suministran leche e insumos.

| Campo | Tipo | Restricción | Descripción |
|-------|------|-------------|-------------|
| id | INT | PK, AUTO_INCREMENT | Identificador único |
| nombre | VARCHAR(100) | NOT NULL | Nombre de la empresa |
| contacto | VARCHAR(100) | NULL | Persona de contacto |
| telefono | VARCHAR(30) | NULL | Teléfono |
| email | VARCHAR(100) | NULL | Correo electrónico |
| direccion | VARCHAR(255) | NULL | Dirección física |
| nit | VARCHAR(20) | NULL | Número de identificación fiscal |
| notas | TEXT | NULL | Observaciones |
| estado | ENUM | NOT NULL, DEFAULT 'activo' | activo, inactivo |

### Sin relaciones actualmente
Esta tabla está preparada para futuras funcionalidades como órdenes de compra y control de inventario de entrada.

---

## Tabla: pedidos

Pedidos realizados por clientes desde el catálogo en línea.

| Campo | Tipo | Restricción | Descripción |
|-------|------|-------------|-------------|
| id | INT | PK, AUTO_INCREMENT | Identificador único |
| numero_pedido | VARCHAR(30) | UNIQUE, NOT NULL | Consecutivo (PED-00001) |
| fecha | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Fecha y hora |
| total | DECIMAL(10,2) | NOT NULL, DEFAULT 0.00 | Total del pedido |
| cliente_id | INT | FK → usuarios(id) | Cliente que ordenó |
| notas | TEXT | NULL | Notas del cliente |
| estado | ENUM | NOT NULL, DEFAULT 'pendiente' | pendiente, confirmado, entregado, cancelado |

### Relaciones
- **N:1 con usuarios**: Muchos pedidos pertenecen a un cliente (ON DELETE CASCADE).
- **1:N con detalle_pedidos**: Un pedido tiene muchos detalles (ON DELETE CASCADE).

---

## Tabla: detalle_pedidos

Productos en cada pedido.

| Campo | Tipo | Restricción | Descripción |
|-------|------|-------------|-------------|
| id | INT | PK, AUTO_INCREMENT | Identificador único |
| pedido_id | INT | FK → pedidos(id), NOT NULL | Pedido al que pertenece |
| producto_id | INT | FK → productos(id), NOT NULL | Producto pedido |
| cantidad | INT | NOT NULL | Cantidad pedida |
| precio_unitario | DECIMAL(10,2) | NOT NULL | Precio al ordenar |
| subtotal | DECIMAL(10,2) | NOT NULL | cantidad × precio_unitario |

### Relaciones
- **N:1 con pedidos**: Muchos detalles pertenecen a un pedido (ON DELETE CASCADE).
- **N:1 con productos**: Muchos detalles referencian un producto (ON DELETE RESTRICT).

---

## Diagrama ER simplificado

```
usuarios (1) ──┐
               │
               ├── (N) ventas (1) ── (N) detalle_ventas (N) ── (1) productos
               │
               └── (N) pedidos (1) ── (N) detalle_pedidos (N) ──┘

proveedores (sin relaciones actualmente)
```

## Integridad referencial

El motor InnoDB de MySQL garantiza:
- **CASCADE**: Al borrar una venta, se borran sus detalles automáticamente.
- **RESTRICT**: No se puede borrar un producto que tenga ventas registradas.
- **SET NULL**: Al borrar un usuario, sus ventas quedan huérfanas pero no se pierden.

Esto asegura que los datos contables sean consistentes y no se pierdan registros históricos importantes.
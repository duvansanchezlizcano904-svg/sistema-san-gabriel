# Módulo Carrito y Pedidos

Este módulo gestiona el carrito de compras de los clientes y la confirmación de pedidos en línea.

## ¿Quién lo usa?

- **Clientes** autenticados que quieren comprar desde el catálogo.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Ver carrito | `index.php?c=carrito` | GET | Sí |
| Agregar producto | `index.php?c=carrito&a=agregar` | POST | No |
| Actualizar cantidad | `index.php?c=carrito&a=actualizar` | POST | No |
| Eliminar producto | `index.php?c=carrito&a=quitar` | GET | No |
| Vaciar carrito | `index.php?c=carrito&a=vaciar` | GET | No |
| Confirmar pedido | `index.php?c=carrito&a=confirmar` | POST | Sí |

## Archivos que lo componen

- **Controlador**: `controllers/CarritoController.php`
- **Modelo**: `models/Pedido.php`, `models/Producto.php`
- **Vistas**:
  - `views/carrito/index.php`
  - `views/carrito/gracias.php`

## Métodos del controlador

### agregar()
Recibe ID del producto y cantidad. Valida que el producto exista y tenga stock. Agrega al array `$_SESSION['carrito']`. Si el producto ya está, incrementa la cantidad.

### actualizar()
Modifica la cantidad de un producto en el carrito. Valida que no exceda el stock disponible.

### quitar()
Elimina un producto específico del carrito.

### vaciar()
Limpia completamente el array de sesión del carrito.

### ver()
Muestra el contenido del carrito con subtotales y total general. Permite modificar cantidades o eliminar productos.

### confirmar()
Procesa el pedido. Ejecuta una transacción atómica que:
1. Valida stock de cada producto.
2. Genera número de pedido (PED-00001).
3. Inserta el pedido en la tabla `pedidos`.
4. Inserta los detalles en `detalle_pedidos`.
5. Descuenta el inventario.
6. Limpia el carrito de sesión.
7. Muestra la pantalla de confirmación con el número de pedido.

## Cómo se protege

- `require_auth()` solo en el método `confirmar()`.
- Los métodos de manipulación del carrito permiten sesión pero no la requieren (para匿名 usuarios).

## Tablas de la base de datos

### pedidos
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT (PK) | Identificador |
| numero_pedido | VARCHAR(30) UNIQUE | Consecutivo (PED-00001) |
| fecha | DATETIME | Fecha del pedido |
| total | DECIMAL(10,2) | Total del pedido |
| cliente_id | INT (FK) | Cliente que ordenó |
| notas | TEXT | Notas del cliente |
| estado | ENUM | pendiente, confirmado, entregado, cancelado |

### detalle_pedidos
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT (PK) | Identificador |
| pedido_id | INT (FK) | Referencia a pedidos |
| producto_id | INT (FK) | Referencia a productos |
| cantidad | INT | Cantidad pedida |
| precio_unitario | DECIMAL(10,2) | Precio al ordenar |
| subtotal | DECIMAL(10,2) | Cantidad × Precio |

## Cómo probarlo

1. Iniciar sesión como cliente.
2. Ir al catálogo (`index.php?c=catalogo`).
3. Agregar productos al carrito.
4. Ir al carrito (`index.php?c=carrito`).
5. Modificar cantidades, eliminar productos.
6. Hacer clic en "Confirmar Pedido".
7. Ver la pantalla de agradecimiento con el número de pedido.
8. Ver el pedido en el historial (si existe módulo de historial de pedidos del cliente).
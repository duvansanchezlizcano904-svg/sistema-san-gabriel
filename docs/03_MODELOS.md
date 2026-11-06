# Módulo 03: Modelos de Datos

Este documento detalla las clases del directorio `models/`, encargadas de interactuar con la base de datos mediante consultas preparadas PDO.

---

## 1. `models/Usuario.php`

### Propósito
Gestiona la autenticación, registro, recuperación de credenciales y consulta de perfiles de usuario.

### Métodos:
* **`obtenerPorUsuario(string $usuario): ?array`**
  * Busca en la tabla `usuarios` por nombre de usuario.
* **`obtenerPorId(int $id): ?array`**
  * Obtiene datos del usuario por su ID.
* **`obtenerPorEmail(string $email): ?array`**
  * Busca un usuario por su correo electrónico.
* **`autenticar(string $emailOUsuario, string $password): ?array`**
  * Valida credenciales usando `password_verify`. Acepta email o nombre de usuario.
* **`registrar(string $nombre, string $email, string $telefono, string $password): bool`**
  * Registra un nuevo cliente. Genera nombre de usuario único a partir del email. Hashea la contraseña con `password_hash`.
* **`guardarTokenRecuperacion(string $email, string $token, string $expiracion): bool`**
  * Guarda el token y fecha de expiración (1 hora) para recuperación de contraseña. Crea las columnas si no existen.
* **`obtenerPorTokenRecuperacion(string $token): ?array`**
  * Busca usuario por token válido (no expirado).
* **`actualizarPasswordPorToken(string $token, string $nuevaPassword): bool`**
  * Actualiza la contraseña y limpia el token de recuperación.

---

## 2. `models/Producto.php`

### Propósito
Gestiona el catálogo de quesos, inventario, filtros avanzados y paginación.

### Métodos:
* **`listarTodos(...)`**
  * Lista productos con filtros por búsqueda, tipo, estado, categoría, tipo de leche e intensidad de sabor. Evita errores HY093 usando parámetros únicos (`:b1`, `:b2`, `:b3`).
* **`listarConPaginacion(...)`**
  * Lista productos con paginación para carga infinita ("Cargar más"). Soporta los mismos filtros que `listarTodos`.
* **`obtenerPorId(int $id): ?array`**
  * Obtiene un producto por su ID.
* **`existeCodigo(string $codigo, ?int $ignorarId = null): bool`**
  * Verifica si un código ya existe.
* **`crear(array $datos): bool`**
  * Inserta un nuevo producto con todas las columnas de categorización.
* **`actualizar(int $id, array $datos): bool`**
  * Actualiza un producto existente.
* **`cambiarEstado(int $id, string $nuevoEstado): bool`**
  * Borrado lógico entre 'activo' e 'inactivo'.
* **`eliminar(int $id): bool`**
  * Borra físicamente solo si no tiene ventas asociadas.
* **`descontarStock(int $id, int $cantidadVendida, ?PDO $conn = null): bool`**
  * Descuenta inventario dentro de una transacción. Usa `:cantidad_val` y `:cantidad_min` separados para evitar HY093.
* **`obtenerEstadisticas(): array`**
  * Métricas globales: total productos, disponibles, stock bajo, agotados, valor del inventario.
* **`obtenerProductosCriticos(): array`**
  * Productos con stock bajo o agotados.
* **`obtenerTipos(): array`**, **`obtenerCategorias(): array`**, **`obtenerTiposLeche(): array`**, **`obtenerIntensidades(): array`**
  * Listas para filtros dinámicos.
* **`imagenUrl(array $producto): string`**
  * Resuelve la URL de imagen del producto (BD, mapeo por nombre, o placeholder).
* **`obtenerEstadisticasPorCategoria(): array`**
  * Estadísticas agrupadas por categoría.

---

## 3. `models/Venta.php`

### Propósito
Controla el proceso de facturación POS, generación de consecutivos y transacciones atómicas.

### Métodos:
* **`generarNumeroVenta(): string`**
  * Genera código correlativo (VNT-00001).
* **`registrarVenta(array $datosVenta, array $items): int`**
  * Transacción atómica: valida stock, inserta venta, inserta detalles, descuenta inventario. Usa `beginTransaction`, `commit`, `rollBack`.
* **`listarHistorial(...)`**
  * Lista ventas con filtros por fecha y búsqueda.
* **`obtenerPorId(int $id): ?array`**
  * Obtiene una venta con datos del usuario que la registró.
* **`obtenerDetalle(int $ventaId): array`**
  * Lista los productos de una venta.
* **`obtenerUltimasVentas(int $limite): array`**
  * Ventas más recientes para el Dashboard.
* **`obtenerEstadisticas(): array`**
  * Total ventas, dinero recaudo, ventas del día.
* **`obtenerProductosMasVendidos(int $limite): array`**
  * Ranking de productos más vendidos.

---

## 4. `models/Proveedor.php`

### Propósito
Gestiona el catálogo de proveedores de leche y suministros.

### Métodos:
* **`listarTodos(string $busqueda = '', string $estado = ''): array`**
  * Lista proveedores con filtros. Evita HY093 con parámetros únicos.
* **`obtenerPorId(int $id): ?array`**
* **`existeNit(string $nit, ?int $ignorarId = null): bool`**
* **`crear(array $datos): int`**, **`actualizar(int $id, array $datos): bool`**
* **`cambiarEstado(int $id, string $nuevoEstado): bool`**, **`eliminar(int $id): bool`**
* **`obtenerEstadisticas(): array`**

---

## 5. `models/Pedido.php`

### Propósito
Controla los pedidos generados por clientes desde el carrito en línea.

### Métodos:
* **`generarNumeroPedido(): string`**
  * Genera correlativo PED-00001.
* **`registrarPedido(array $datosPedido, array $items): int`**
  * Transacción atómica que valida stock, descuenta inventario, crea el pedido y sus detalles.
* **`obtenerPorId(int $id): ?array`**
* **`obtenerDetalles(int $pedidoId): array`**
* **`listarPorCliente(int $clienteId): array`**
* **`actualizarEstado(int $id, string $estado): bool`**
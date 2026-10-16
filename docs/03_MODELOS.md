# Módulo 03: Modelos de Datos (Capa de Negocio y Persistencia)

Este documento detalla cada una de las clases del directorio `models/`, encargadas de interactuar con la base de datos MySQL mediante consultas preparadas PDO y aplicar las reglas de negocio del sistema lácteo.

---

## 1. `models/Usuario.php`

### Propósito
Gestiona la autenticación, recuperación de credenciales y consulta de perfiles de usuario (Administrador y Cliente).

### Métodos:
* **`obtenerPorUsuario(string $usuario): ?array`**
  * Busca en la tabla `usuarios` el registro que coincida exactamente con el nombre de usuario.
  * Retorna los campos `id`, `nombre`, `usuario`, `password`, `email`, `rol` y `creado_en`.
* **`obtenerPorId(int $id): ?array`**
  * Obtiene la información del usuario a partir de su identificador primario.
* **`autenticar(string $usuario, string $password): ?array`**
  * Busca al usuario por su nombre.
  * Compara la contraseña en texto plano ingresada contra el hash seguro en la base de datos utilizando `password_verify($password, $user['password'])`.
  * Retorna los datos del usuario si la verificación es exitosa o `null` si es incorrecta.

---

## 2. `models/Producto.php`

### Propósito
Gestiona el catálogo de variedades de queso, cálculo de estados de inventario, stock crítico y operaciones CRUD.

### Métodos:
* **`listarTodos(string $busqueda = '', string $tipo = '', string $estado = ''): array`**
  * Lista productos aplicando filtros dinámicos por término de búsqueda (nombre, código o descripción), variedad/tipo de queso y estado (`activo` o `inactivo`).
* **`obtenerPorId(int $id): ?array`**
  * Retorna los datos de un queso específico por su clave primaria.
* **`existeCodigo(string $codigo, ?int $ignorarId = null): bool`**
  * Comprueba si un código (ej: `LAC-001`) ya existe en la base de datos para evitar duplicados. Si se pasa `$ignorarId`, permite actualizar otros campos de un producto conservando su código.
* **`crear(array $datos): int`**
  * Inserta un nuevo producto lácteo con validación de tipos y campos (`codigo`, `nombre`, `tipo`, `descripcion`, `precio_compra`, `precio_venta`, `cantidad`, `stock_minimo`, `unidad_medida`, `estado`).
* **`actualizar(int $id, array $datos): bool`**
  * Modifica los datos de un queso existente en la base de datos.
* **`cambiarEstado(int $id, string $nuevoEstado): bool`**
  * Realiza el borrado lógico alternando el estado entre `'activo'` e `'inactivo'`.
* **`eliminar(int $id): bool`**
  * Ejecuta el borrado físico de la fila en la tabla `productos`.
* **`tieneVentasAsociadas(int $id): bool`**
  * Consulta en la tabla `detalle_ventas` si el queso ha sido vendido alguna vez. Protege la integridad referencial impidiendo el borrado físico si tiene histórico de ventas.
* **`obtenerTipos(): array`**
  * Retorna los tipos de quesos registrados actualmente (`DISTINCT tipo`).
* **`obtenerEstadisticas(): array`**
  * Calcula métricas globales de inventario: total de variedades, stock total en bodega, productos con stock bajo ($\le$ stock mínimo) y productos agotados ($= 0$).
* **`obtenerProductosStockBajo(): array`**
  * Retorna la lista de quesos cuyas existencias están en cero o por debajo del umbral mínimo de seguridad.

---

## 3. `models/Venta.php`

### Propósito
Controla el proceso de facturación en punto de venta (POS), generación de consecutivos correlativos, reportes financieros y auditoría con **transacciones atómicas PDO**.

### Métodos:
* **`generarNumeroVenta(): string`**
  * Consulta el último ID de venta y genera un código correlativo formateado (ej: `VNT-00001`, `VNT-00002`).
* **`registrarVenta(array $datosVenta, array $items): int`**
  * **Transacción Atómica (`beginTransaction`, `commit`, `rollBack`):**
    1. Valida que haya al menos un ítem y que las cantidades sean mayores a 0.
    2. Comprueba que el producto exista, esté activo y cuente con stock suficiente.
    3. Inserta el encabezado en la tabla `ventas` con número correlativo, fecha, total, cliente y notas.
    4. Inserta cada producto en `detalle_ventas` con su precio unitario y subtotal.
    5. Descuenta el inventario automáticamente mediante `UPDATE productos SET cantidad = cantidad - :cantidad`.
    6. Confirma la transacción y retorna el `id` de la venta creada. Si ocurre cualquier error, cancela todos los cambios con `rollBack()`.
* **`listarHistorial(string $fechaInicio = '', string $fechaFin = '', string $busqueda = ''): array`**
  * Retorna las ventas filtradas por fechas o por nombre de cliente / código de factura.
* **`obtenerPorId(int $id): ?array`**
  * Retorna el encabezado de una venta incluyendo el nombre del cajero/administrador que la registró.
* **`obtenerDetalle(int $ventaId): array`**
  * Retorna el desglose de ítems vendidos con sus cantidades, unidades de medida y precios.
* **`obtenerUltimasVentas(int $limite = 5): array`**
  * Consulta las ventas más recientes para los paneles informativos del Dashboard.
* **`obtenerEstadisticas(): array`**
  * Retorna el acumulado total de ventas, dinero recaudado, ventas del día y cantidad de transacciones.
* **`obtenerProductosMasVendidos(int $limite = 5): array`**
  * Calcula el ranking de quesos con mayor rotación ordenados por cantidad de unidades vendidas.

---

## 4. `models/Proveedor.php`

### Propósito
Gestiona el catálogo de empresas y productores lecheros que abastecen a la quesería.

### Métodos:
* **`listarTodos(string $busqueda = '', string $estado = ''): array`**
  * Retorna proveedores filtrados por nombre, contacto, teléfono, correo o NIT.
* **`obtenerPorId(int $id): ?array`**
  * Busca a un proveedor por su ID.
* **`existeNit(string $nit, ?int $ignorarId = null): bool`**
  * Valida que no existan dos proveedores con el mismo NIT o cédula fiscal.
* **`crear(array $datos): int`**
  * Registra un nuevo proveedor en la base de datos.
* **`actualizar(int $id, array $datos): bool`**
  * Actualiza la información de contacto y fiscal del proveedor.
* **`cambiarEstado(int $id, string $nuevoEstado): bool`**
  * Alterna el estado del proveedor entre `'activo'` e `'inactivo'`.
* **`eliminar(int $id): bool`**
  * Elimina físicamente un registro de proveedor.
* **`obtenerEstadisticas(): array`**
  * Retorna el conteo total de proveedores, activos e inactivos.

---

## 5. `models/Pedido.php`

### Propósito
Controla los pedidos generados por clientes desde el carrito de compras en línea.

### Métodos:
* **`generarNumeroPedido(): string`**
  * Genera el consecutivo único para pedidos en línea (ej: `PED-00001`).
* **`registrarPedido(array $datosPedido, array $items): int`**
  * Ejecuta una transacción atómica que valida las existencias en tiempo real, descuenta el stock de bodega, inserta la orden en `pedidos` y los renglones en `detalle_pedidos`.
* **`obtenerPorId(int $id): ?array`**
  * Consulta los datos de un pedido.
* **`obtenerDetalles(int $pedidoId): array`**
  * Consulta la lista de productos contenidos dentro de un pedido en línea.
* **`listarPorCliente(int $clienteId): array`**
  * Muestra el historial de compras hechas por un cliente particular.
* **`actualizarEstado(int $id, string $estado): bool`**
  * Permite cambiar el estado de la orden (`pendiente`, `confirmado`, `entregado`, `cancelado`).

# Módulo 04: Controladores (Capa de Coordinación y Control)

Este documento detalla los 11 controladores ubicados en la carpeta `controllers/`. Cada controlador orquesta la lógica entre los modelos y las vistas correspondientes, asegurando la verificación de permisos por rol.

---

## 1. `controllers/AuthController.php`
* **Propósito:** Maneja el ciclo de autenticación y cierre de sesión de usuarios.
* **Acciones:**
  * `login()`: Si el usuario ya está autenticado lo redirige al Dashboard o Catálogo. Si la petición es POST, valida campos requeridos, llama a `Usuario->autenticar()`, regenera el ID de sesión con `session_regenerate_id(true)` y guarda los datos en `$_SESSION`. Si la petición es GET, carga `views/login/login.php`.
  * `logout()`: Destruye las variables de sesión, elimina la cookie de sesión del navegador (`setcookie`), destruye la sesión con `session_destroy()` y redirige a `login.php`.

---

## 2. `controllers/DashboardController.php`
* **Propósito:** Presenta el panel principal con los indicadores clave de rendimiento (KPIs).
* **Seguridad:** Requiere rol administrador mediante `require_admin()`.
* **Acciones:**
  * `index()`: Consulta las estadísticas globales de inventario (`Producto->obtenerEstadisticas()`), resumen de ventas (`Venta->obtenerEstadisticas()`), productos con inventario crítico y las últimas 6 transacciones registradas. Renderiza `views/dashboard/index.php`.

---

## 3. `controllers/ProductoController.php`
* **Propósito:** CRUD completo de productos lácteos para el administrador.
* **Seguridad:** `require_admin()`.
* **Acciones:**
  * `index()`: Lista los quesos con filtros por búsqueda de texto, tipo y estado activo/inactivo. Renderiza `views/productos/index.php`.
  * `crear()`: Muestra el formulario de registro de un nuevo queso con los tipos de queso disponibles.
  * `guardar()`: Recibe el formulario POST, valida que el código no exista (`Producto->existeCodigo`), verifica valores numéricos positivos para precios y stock, y almacena el producto.
  * `editar()`: Carga los datos de un queso por su ID y renderiza el formulario de edición `views/productos/editar.php`.
  * `actualizar()`: Valida los cambios enviados por POST y actualiza el producto en la base de datos.
  * `cambiarEstado()`: Realiza el borrado lógico activando o desactivando el queso.
  * `eliminar()`: Verifica si el producto tiene ventas asociadas (`Producto->tieneVentasAsociadas`). Si no tiene historial, lo borra físicamente; si tiene ventas, muestra un mensaje flash impidiendo la eliminación para proteger la integridad contable.

---

## 4. `controllers/InventarioController.php`
* **Propósito:** Supervisa el nivel de existencias y productos bajo el stock mínimo.
* **Seguridad:** `require_admin()`.
* **Acciones:**
  * `index()`: Muestra la tabla de control de bodega con alertas por colores (verde: disponible, amarillo: bajo stock, rojo: agotado) y cálculo del valor total del inventario. Renderiza `views/inventario/index.php`.

---

## 5. `controllers/VentaController.php`
* **Propósito:** Gestiona el punto de venta (POS) para mostrador y facturación rápida.
* **Seguridad:** `require_admin()`.
* **Acciones:**
  * `index()`: Genera el siguiente número consecutivo de venta (`VNT-0000X`), lista los quesos activos con existencias mayores a cero y prepara el panel de venta rápida en `views/ventas/index.php`.
  * `guardar()`: Procesa la venta enviada desde el carrito JS. Extrae los ítems seleccionados, invoca la transacción atómica en `Venta->registrarVenta()` que descuenta el stock y redirige al tiquete de venta imprimible.

---

## 6. `controllers/HistorialController.php`
* **Propósito:** Consulta y auditoría de ventas anteriores.
* **Seguridad:** `require_admin()`.
* **Acciones:**
  * `index()`: Permite filtrar el historial de ventas por rango de fechas (desde / hasta) y buscador de cliente o número de recibo. Renderiza `views/historial/index.php`.
  * `detalle()`: Recibe el ID de una venta y muestra la vista detallada con los renglones facturados en `views/historial/detalle.php`.
  * `tiquete()`: Renderiza el tiquete térmico para impresión en papel estándar o impresora POS (`views/factura/tiquete.php`).

---

## 7. `controllers/ReporteController.php`
* **Propósito:** Análisis financiero y rotación de productos lácteos.
* **Seguridad:** `require_admin()`.
* **Acciones:**
  * `index()`: Genera reportes de ingresos totales en rangos de fechas personalizadas, desglose de ventas diarias y top de productos más vendidos (`Venta->obtenerProductosMasVendidos()`). Renderiza `views/reportes/index.php`.

---

## 8. `controllers/ProveedorController.php`
* **Propósito:** Gestión integral de proveedores de leche, cuajos y suministros.
* **Seguridad:** `require_admin()`.
* **Acciones:**
  * `index()`: Lista los proveedores registrados con buscador y filtro de estado.
  * `crear()`: Formulario para registrar un proveedor.
  * `guardar()`: Valida unicidad del NIT e inserta el proveedor.
  * `editar()`: Formulario precargado para editar información del proveedor.
  * `actualizar()`: Guarda las modificaciones.
  * `cambiarEstado()`: Activa o desactiva un proveedor.
  * `eliminar()`: Elimina el registro del proveedor.

---

## 9. `controllers/CatalogoController.php`
* **Propósito:** Catálogo digital de productos para clientes autenticados.
* **Seguridad:** `require_auth()`.
* **Acciones:**
  * `index()`: Carga los quesos disponibles en stock y permite a los clientes agregar productos a su carrito de compras. Renderiza `views/catalogo/index.php`.

---

## 10. `controllers/CarritoController.php`
* **Propósito:** Maneja el carrito de compras en sesión para los pedidos de clientes.
* **Acciones:**
  * `agregar()`: Agrega un producto y cantidad a `$_SESSION['carrito']`.
  * `actualizar()`: Modifica la cantidad de un ítem en el carrito.
  * `eliminar()`: Quita un queso del carrito.
  * `vaciar()`: Limpia todo el carrito de la sesión.
  * `ver()`: Muestra el resumen del pedido en `views/carrito/index.php`.
  * `confirmar()`: Crea el pedido definitivo llamando a `Pedido->registrarPedido()` y muestra la pantalla de agradecimiento `views/carrito/gracias.php`.

---

## 11. `controllers/PublicoController.php`
* **Propósito:** Acceso público para usuarios invitados sin requerir login previo.
* **Acciones:**
  * `inicio()`: Renderiza la página de bienvenida (*landing page*) [views/inicio/home.php](file:///c:/laragon/www/php%20mi%20proyecto/views/inicio/home.php) con información de la quesería y catálogo destacado.
  * `index()`: Renderiza el catálogo público general con buscador y filtros en `views/publico/catalogo.php`.

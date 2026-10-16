# Módulo 05: Vistas (Capa de Presentación e Interfaz de Usuario)

Este documento describe la estructura y propósito de los archivos contenidos en el directorio `views/`, organizados por sus respectivos submódulos.

---

## 1. Plantillas Globales (`views/layouts/`)

* **`header.php`**:
  * Incluye las cabeceras HTML5, metadatos, fuentes y hojas de estilo (Bootstrap 5.3, Bootstrap Icons y `public/css/style.css`).
  * Estructura el contenedor principal (`app-wrapper`).
  * Despliega la barra superior (*top navbar*) con botón para alternar el menú en dispositivos móviles, botón de acceso rápido para registrar ventas y el menú desplegable del perfil de usuario autenticado.
* **`sidebar.php`**:
  * Barra lateral de navegación con el logotipo de la quesería San Gabriel.
  * Renderiza los enlaces administrativos si el usuario es administrador (`Dashboard`, `Productos`, `Inventario`, `Ventas`, `Historial`, `Reportes`, `Proveedores`).
  * Muestra los enlaces de cliente si el rol es cliente (`Catálogo`, `Mi Carrito`).
  * Incluye el botón directo para cerrar sesión (`logout.php`).
* **`footer.php`**:
  * Pie de página unificado con texto de derechos reservados y versión.
  * Carga las librerías JavaScript de Bootstrap 5.3 y el archivo interactivo propio `public/js/app.js`.

---

## 2. Autenticación y Bienvenida

* **`views/login/login.php`**:
  * Página de aterrizaje (*Landing Hero*) con tipografía destacada y modal interactivo para ingreso de usuario y contraseña.
  * Reabre automáticamente el modal si hubo un fallo de autenticación gracias a un script inline que detecta variables de error o mensajes flash.
* **`views/inicio/home.php`**:
  * Página de inicio institucional para visitantes. Muestra la historia artesanal de Quesos San Gabriel, características de calidad y catálogo interactivo con opciones para comprar o iniciar sesión.
* **`views/publico/catalogo.php`**:
  * Catálogo de quesos accesible sin sesión requerida, con filtros rápidos por categoría, búsqueda por texto y visualización de precios y disponibilidad.

---

## 3. Panel de Administración (`views/dashboard/`)

* **`views/dashboard/index.php`**:
  * Cuatro tarjetas principales (KPIs): Total de Variedades, Productos con Stock Bajo, Total de Ventas Registradas e Ingresos Totales.
  * Sección de advertencia con tabla de quesos en nivel crítico de inventario.
  * Tabla con el historial de las últimas ventas realizadas.

---

## 4. Gestión de Productos (`views/productos/`)

* **`index.php`**:
  * Listado general de productos lácteos con insignias de estado (activo/inactivo), disponibilidad de stock, buscador y botones para editar, cambiar estado o eliminar.
* **`crear.php`**:
  * Formulario con validación para dar de alta un nuevo queso (código único, nombre, tipo, descripción, precios de compra y venta, existencias, stock mínimo y unidad de medida).
* **`editar.php`**:
  * Formulario precargado con los datos del producto seleccionado para actualización.

---

## 5. Control de Inventario (`views/inventario/`)

* **`index.php`**:
  * Tabla de inventario en tiempo real con semáforo visual de stock:
    * **Verde:** Existencias óptimas.
    * **Amarillo:** Stock igual o inferior al mínimo configurado.
    * **Rojo:** Agotado (0 unidades).
  * Soporte para impresión directa en papel mediante clase CSS `@media print`.

---

## 6. Registro de Ventas POS (`views/ventas/`)

* **`index.php`**:
  * Panel dividido en dos columnas:
    * **Izquierda:** Datos de la venta (número correlativo, fecha, cliente, notas) y panel selector de quesos con atributos HTML5 `data-*` que alimentan el script de JavaScript.
    * **Derecha:** Tabla interactiva del carrito de ventas con cálculo instantáneo de subtotales, total general y botón para finalizar la transacción.

---

## 7. Historial y Facturación (`views/historial/` y `views/factura/`)

* **`views/historial/index.php`**:
  * Registro cronológico de todas las ventas con filtros por fecha y búsqueda por cliente.
* **`views/historial/detalle.php`**:
  * Desglose completo de una venta específica: ítems adquiridos, valores unitarios, subtotales e información del usuario que atendió.
* **`views/factura/tiquete.php`**:
  * Formato de tiquete térmico para impresoras POS o impresión en PDF con datos fiscales, detalle de productos lácteos y pie de página de cortesía.

---

## 8. Reportes Financieros (`views/reportes/`)

* **`views/reportes/index.php`**:
  * Panel de estadísticas con selector de rangos de fechas.
  * Tarjetas de resumen financiero (monto recaudado, total de transacciones y promedio por venta).
  * Gráficas visuales y ranking de quesos más vendidos.

---

## 9. Proveedores (`views/proveedores/`)

* **`index.php`**: Lista de proveedores con buscador por NIT o nombre, y estadísticas de proveedores activos.
* **`crear.php`**: Formulario de registro de proveedor con campos de NIT, nombre de empresa, contacto, teléfono, correo y dirección.
* **`editar.php`**: Formulario de modificación de datos de proveedores.

---

## 10. Catálogo y Carrito de Clientes (`views/catalogo/` y `views/carrito/`)

* **`views/catalogo/index.php`**:
  * Cuadrícula de tarjetas con fotos/emojis, badges de disponibilidad y formulario para agregar cantidades al carrito del cliente.
* **`views/carrito/index.php`**:
  * Resumen del pedido en curso, modificación de cantidades, subtotal y botón de confirmación de pedido.
* **`views/carrito/gracias.php`**:
  * Pantalla de confirmación con el número de pedido correlativo (`PED-0000X`) y recomendaciones de entrega.

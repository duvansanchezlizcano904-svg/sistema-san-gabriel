# Módulo 06: Frontend, JavaScript y Estilos Visuales

Este documento detalla la capa de recursos estáticos (*assets*) ubicada en el directorio `public/`, encargada de la reactividad del cliente, la experiencia de usuario y el diseño estético de Quesos San Gabriel.

---

## 1. `public/js/app.js`

### Propósito
Es el archivo JavaScript principal que añade dinamismo a la interfaz sin necesidad de dependencias pesadas externas.

### Módulos funcionales implementados:
1. **Control del Menú Móvil (*Sidebar Toggle*):**
   * Escucha eventos de clic en `#sidebarToggle` y `#sidebarOverlay` para añadir o remover la clase `.show`, permitiendo abrir y cerrar el menú lateral en dispositivos móviles y tabletas.
2. **Confirmación antes de Eliminar:**
   * Intercepta el envío de formularios con la clase `.form-eliminar-producto`.
   * Muestra un diálogo de confirmación `confirm()` personalizado con el nombre del queso para prevenir eliminaciones accidentales.
3. **Punto de Venta POS y Carrito Dinámico:**
   * **Lectura de Atributos `data-*`:** Al seleccionar un queso en `#selectProductoVenta`, extrae `dataset.stock`, `dataset.precio`, `dataset.unidad` y `dataset.nombre`.
   * **Actualización en vivo:** Limita el valor máximo del campo de cantidad (`inputCantidad.max = stock`) y actualiza el texto informativo `#infoStockDisponible`.
   * **Adición de productos al carrito:** Comprueba que no se supere el stock máximo disponible. Si el producto ya estaba en la lista, incrementa su cantidad; de lo contrario, agrega una nueva fila en `#tablaItemsVenta`.
   * **Recálculo de Totales:** Recorre la matriz de ítems, calcula los subtotales (`precio * cantidad`), formatea la moneda en pesos colombianos y actualiza el elemento `#totalVentaSpan`.
   * **Generación de campos ocultos (`hidden inputs`):** Inyecta arrays `items[i][producto_id]` y `items[i][cantidad]` en el formulario antes de enviar el POST, garantizando que el servidor reciba la estructura de datos correcta para procesar la transacción atómica.

---

## 2. Hojas de Estilos CSS (`public/css/`)

### `public/css/style.css`
* **Propósito:** Estilos generales para el panel administrativo, componentes y utilidades del sistema.
* **Aspectos destacados:**
  * **Paleta de Colores Lácteos:** Variables CSS con tonos azules lácteos (`--primary: #1e3a8a`, `--primary-light: #3b82f6`), dorados para quesos y fondos limpios (`#f8fafc`).
  * **Componentes Personalizados:**
    * `.card-lacteo`: Tarjetas con sombra suave (`box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05)`), bordes redondeados y fondo blanco.
    * `.table-lacteo`: Tablas limpias con resaltado al pasar el cursor (*hover*) y alineación vertical media.
    * `.sidebar`: Barra lateral fija con navegación estilizada y estados activos.
  * **Reglas de Impresión (`@media print`):** Oculta la navegación, barra lateral y botones (`.no-print`), adaptando la vista para impresión limpia de reportes e inventarios.

### `public/css/login.css`
* **Propósito:** Diseño específico para la página de acceso y su ventana modal emergente.
* **Aspectos destacados:**
  * Hero banner a pantalla completa con fondo temático y gradiente semitransparente oscuro para garantizar legibilidad del texto.
  * Botones con microanimaciones de elevación (*hover translateY*).
  * Modal centrado con bordes redondeados, iconos en los campos de entrada (*input-groups*) y pie de página integrado.

### `public/css/home.css` y `public/css/publico.css`
* **Propósito:** Estilos para la página de bienvenida institucional (*home*) y el catálogo público para visitantes.
* **Aspectos destacados:**
  * Diseño tipo tienda electrónica moderna con tarjetas de productos (*product cards*).
  * Insignias flotantes de disponibilidad de stock.
  * Selector numérico de cantidades estilizado y botón de agregar al carrito con retroalimentación visual.
  * Barra de navegación adhesiva (*sticky navbar*) y pie de página corporativo.

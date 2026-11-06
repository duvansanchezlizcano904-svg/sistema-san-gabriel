# Registro de Cambios — Quesos San Gabriel

Cada entrada indica la fecha, el archivo modificado y qué se cambió y por qué.

---

## 2026-11-05

- **database/database.sql**: agregados UPDATE después del INSERT de los 10 productos iniciales (LAC-001 a LAC-010) para asignarles categoría, tipo_leche, intensidad_sabor, tiempo_maduracion, peso_presentacion y origen. Ahora los productos iniciales tienen todos los datos de categorización.

- **controllers/AuthController.php** método `recuperar()`: cambio en el mensaje de respuesta por seguridad. Ahora muestra "Si el correo está registrado, te enviamos un enlace para recuperar tu contraseña" independientemente de si el correo existe o no, evitando enumeration de usuarios.

- **.gitignore**: creado en la raíz del proyecto para excluir `vendor/` y `config/mail.php` del control de versiones.

- **GUIA_CODIGO_PHP/**: movida de la raíz a `docs/GUIA_CODIGO_PHP/` para mantener organizados los archivos de documentación.

- **config/helpers.php**: agregado `date_default_timezone_set('America/Bogota')` al inicio para que todas las fechas del sistema usen la zona horaria de Colombia.

---

## 2026-10-23

- **Documentación general creada**: Archivos `INSTALACION.md`, `BASE_DE_DATOS.md`, `SEGURIDAD.md` y `ERRORES_COMUNES.md` con guías completas del sistema.

- **Documentación por módulos creada**: 10 archivos en `docs/modulos/` explicando cada funcionalidad: Autenticación, Dashboard, Productos, Inventario, Ventas, Historial y Facturas, Reportes, Proveedores, Catálogo, Carrito y Pedidos, Página Pública.

- **Documentación técnica actualizada**: Archivos `01_CONFIGURACION_Y_BASE_DE_DATOS.md`, `02_ENRUTAMIENTO_Y_PUNTOS_DE_ENTRADA.md`, `03_MODELOS.md` y `04_CONTROLADORES.md` actualizados con información sobre PHPMailer, recuperación de contraseña, columnas de categorización en productos y PublicoController.

- **Índice general actualizado**: `00_INDICE_Y_ARQUITECTURA.md` ahora incluye enlaces a todos los documentos de módulos y documentación general.

- **config/mail.php**: cambiado `PHPMailer::ENCRYPTION_STARTTLS` por `'tls'` ( texto) para evitar error "Class PHPMailer not found" al cargar la config antes de incluir el autoload.

- **docs/ORDEN_IMPORTACION_BD.md**: simplificado el documento. Ahora solo hay dos pasos (database/database.sql + inserts_nuevos_quesos.sql) porque database.sql ya incluye todas las columnas. Agregada nota sobre migraciones para bases de datos antiguas.

- **composer.json + vendor/**: instalado PHPMailer v6.12.0 para envío de correos.

- **config/mail.php**: creado archivo de configuración SMTP con variables de entorno (SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD, FROM_EMAIL, FROM_NAME). Incluye modo debug para desarrollo.

- **config/helpers.php**: agregadas funciones `enviar_correo()` y `enviar_correo_recuperacion()` que usan PHPMailer con plantilla HTML profesional.

- **controllers/AuthController.php** — método `recuperar()`: ya no muestra el enlace de recuperación en pantalla (fallo de seguridad). Ahora llama a `enviar_correo_recuperacion()` que envía el enlace por correo electrónico.

- **database.sql (raíz)**: eliminado archivo duplicado y dañado (tenía comilla mal en línea 46: `"cliente@gabriellacteos.com',`).

- **database/database.sql**: corregida la comilla maldita en el INSERT del usuario 'cliente'. Agregadas las columnas faltantes a la tabla `productos`: `categoria`, `tipo_leche`, `intensidad_sabor`, `tiempo_maduracion`, `peso_presentacion`, `origen`, `imagen_url`, `imagen`. Agregados los índices correspondientes.

- **controllers/PublicoController.php** — línea 141: corregida la ruta del `require` de `'../partials/tarjeta_producto.php'` a `'../views/partials/tarjeta_producto.php'`. Esto causaba error 500 en el botón "Cargar más".

- **scratch/**: movida la carpeta de la raíz a `docs/pruebas/scratch`.

- **docs/00_INDICE_Y_ARQUITECTURA.md**: reemplazados los links absolutos `file:///c:/laragon/www/php%20mi%20proyecto/...` por rutas relativas `01_...md`, `02_...md`, etc.

- **docs/04_CONTROLADORES.md**: reemplazado el link absoluto `file:///c:/laragon/www/php%20mi%20proyecto/views/inicio/home.php` por la ruta relativa `../views/inicio/home.php`.

- **models/Producto.php** — `listarTodos()`: el parámetro `:busqueda` aparecia tres veces en la cláusula LIKE. Con `PDO::ATTR_EMULATE_PREPARES => false` MySQL lanza HY093. Se cambió a `:b1`, `:b2`, `:b3`.

- **models/Producto.php** — `listarConPaginacion()`: mismo problema que `listarTodos()`. Misma corrección.

- **models/Proveedor.php** — `listarTodos()`: el parámetro `:busqueda` aparecia cinco veces en la cláusula LIKE. Se cambió a `:b1`, `:b2`, `:b3`, `:b4`, `:b5`.

---

## 2026-11-05

- **models/Producto.php** — `descontarStock()`: el parámetro `:cantidad` aparecía dos veces en la misma sentencia UPDATE (`cantidad - :cantidad ... AND cantidad >= :cantidad`). Con `PDO::ATTR_EMULATE_PREPARES => false` MySQL 8 lanza HY093. Se separó en `:cantidad_val` y `:cantidad_min`.

- **models/Producto.php** — `listarTodos()`: el parámetro `:busqueda` se usaba tres veces en la cláusula LIKE (nombre, codigo, descripcion). Mismo error HY093. Se reemplazó por `:b_nombre`, `:b_codigo`, `:b_desc`.

- **models/Producto.php** — `listarConPaginacion()`: mismo problema que `listarTodos()`. Misma corrección de nombres únicos por columna LIKE.

- **models/Proveedor.php** — `listarTodos()`: el parámetro `:busqueda` aparecía cinco veces en la cláusula LIKE (nombre, contacto, telefono, email, nit). Se separó en `:b_nombre`, `:b_contacto`, `:b_tel`, `:b_email`, `:b_nit`.

- **database/migrations/migration_categorias_quesos.sql**: quitada la sintaxis `ADD COLUMN IF NOT EXISTS` que MySQL 8 no soporta (solo MariaDB). Ahora usa `ADD COLUMN` simple. Agregado comentario de ejecución única en la cabecera.

- **database/migrations/migration_imagen_productos.sql**: quitada la sintaxis `ADD COLUMN IF NOT EXISTS` y el comentario `CREATE INDEX IF NOT EXISTS` (que nunca existió como sintaxis válida, pero el texto sugería usarla). Ahora usa `ADD COLUMN` y `CREATE INDEX` directos. Agregado comentario de ejecución única.

- **database/migrations/migration_recuperar_password.sql**: quitada la sintaxis `ADD COLUMN IF NOT EXISTS` de ambas columnas (`token_recuperacion`, `token_expiracion`). Agregado comentario de ejecución única.

- **database/migrations/migration_telefono.sql**: quitada la sintaxis `ADD COLUMN IF NOT EXISTS` de la columna `telefono`. Agregado comentario de ejecución única.

- **docs/ORDEN_IMPORTACION_BD.md**: agregada sección sobre las tres migraciones adicionales (imagen, telefono, recuperar_password) y nota de que cada migración se ejecuta una sola vez.

- **docs/RESUMEN_CAMBIOS.md**: reescrito como changelog (este archivo). El contenido anterior describía la arquitectura del sistema, no los cambios realizados.

- **docs/pruebas/**: creada la carpeta y movidos desde la raíz los cinco archivos de prueba: `diagnostico.php`, `debug_carrito.php`, `test_carrito.php`, `test_agregar.php`, `test_simple.html`. La raíz solo debe contener archivos de producción.

---

## 2026-10-30

- **views/carrito/index.php** — botón "Continuar con la compra": faltaba el `#` en `data-bs-target="modalLogin"` (correcto: `data-bs-target="#modalLogin"`). Sin el `#` Bootstrap no encuentra el modal y el botón no abría nada.

- **views/carrito/index.php**: agregado `<link>` a `login.css` en el `<head>` y añadido el modal `#modalRegistro` que faltaba en la vista.

- **views/inicio/home.php**: eliminado el botón "Ver Catálogo Completo" que no debía verse en producción.

- **views/dashboard/index.php**: eliminado el botón "Ver Catálogo Completo" duplicado.

- **views/inicio/home.php**: eliminado el `<span class="queso-codigo">` de las tarjetas y del modal de detalle del producto (dato interno no visible para clientes).

- **models/Producto.php**: agregado el método `cambiarEstado()` para activar/desactivar productos sin eliminarlos.

- **controllers/ProductoController.php**: agregada la acción `cambiarEstado()` que llama al modelo y redirige al listado.

- **views/productos/index.php**: agregado botón de toggle de estado por producto y búsqueda en tiempo real con JavaScript.

- **config/helpers.php** — `redirect()`: agregada validación para bloquear Open Redirect. Ahora rechaza URLs absolutas externas y esquemas distintos de http/https.

- **index.php**: eliminada la acción `'checkout'` de la lista blanca de acciones sin sesión (el método no existe en ningún controlador).

- **index.php**: agregada la entrada `'publico' => 'PublicoController'` en `$controladoresValidos` para que las rutas públicas resuelvan correctamente.

- **models/Producto.php** — `listarConPaginacion()`: el `LIMIT` y `OFFSET` se pasaban como parámetros de `execute()`, pero con `ATTR_EMULATE_PREPARES => false` PDO los trata como strings y MySQL rechaza `LIMIT 'N'`. Se corrigió usando interpolación directa con cast `(int)`.

- **database/database.sql** y **database/database.sql** (raíz): agregado `SET FOREIGN_KEY_CHECKS = 0` antes de los DROP TABLE y `SET FOREIGN_KEY_CHECKS = 1` al final, para permitir reimportaciones sin error de clave foránea.

- **database/seeds/inserts_nuevos_quesos.sql**: cambiado `INSERT INTO` por `INSERT IGNORE INTO` para que no falle si los registros ya existen.

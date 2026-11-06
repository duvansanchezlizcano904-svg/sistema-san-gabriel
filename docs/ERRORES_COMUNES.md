# Errores Comunes y Soluciones

Este documento lista los errores más frecuentes que hemos encontrado en el proyecto y cómo solucionarlos.

## Errores de Base de Datos

### SQLSTATE[HY093]: Invalid parameter number

**Qué es:**
Error al usar el mismo parámetro varias veces en una consulta preparada cuando `PDO::ATTR_EMULATE_PREPARES` está en `false`.

**Cómo se ve:**
```
SQLSTATE[HY093]: Invalid parameter number: parameter was not defined
```

**Por qué pasa:**
MySQL nativo no permite reutilizar el mismo parámetro en una consulta. Con emulación activada PHP lo maneja, pero con emulación desactivada (más seguro) falla.

**Código problemático:**
```php
$sql = "SELECT * FROM productos 
        WHERE nombre LIKE :busqueda 
           OR codigo LIKE :busqueda 
           OR descripcion LIKE :busqueda";
$stmt->execute([':busqueda' => "%$busqueda%"]);
```

**Solución:**
Usar parámetros únicos:
```php
$sql = "SELECT * FROM productos 
        WHERE nombre LIKE :b1 
           OR codigo LIKE :b2 
           OR descripcion LIKE :b3";
$stmt->execute([
    ':b1' => "%$busqueda%",
    ':b2' => "%$busqueda%",
    ':b3' => "%$busqueda%"
]);
```

**Archivos afectados:**
- `models/Producto.php` en `listarTodos()` y `listarConPaginacion()`
- `models/Proveedor.php` en `listarTodos()`
- `models/Venta.php` en métodos con descuento de stock

---

### ERROR 1451: Cannot delete or update a parent row

**Qué es:**
Error al intentar eliminar un registro que tiene relaciones foráneas activas.

**Cómo se ve:**
```
ERROR 1451 (23000): Cannot delete or update a parent row: 
a foreign key constraint fails
```

**Por qué pasa:**
Intentaste eliminar un producto que tiene ventas registradas, o reimportar la base de datos sin desactivar las llaves foráneas primero.

**Solución para reimportar:**
En `database.sql` ya incluimos:
```sql
SET FOREIGN_KEY_CHECKS = 0;
-- DROP TABLE statements
SET FOREIGN_KEY_CHECKS = 1;
```

**Solución para productos:**
No borres productos con historial de ventas. Cambia su estado a 'inactivo':
```php
$producto->cambiarEstado($id, 'inactivo');
```

---

### ERROR 1060: Duplicate column name

**Qué es:**
Error al ejecutar una migración que intenta agregar una columna que ya existe.

**Cómo se ve:**
```
ERROR 1060 (42S21): Duplicate column name 'categoria'
```

**Por qué pasa:**
Ejecutaste una migración dos veces, o la columna ya existía en la base de datos.

**Solución:**
MySQL 8 no soporta `ADD COLUMN IF NOT EXISTS`. Antes de ejecutar una migración, verifica:
```sql
DESCRIBE productos;
```

Si la columna ya existe, no ejecutes la migración. Las migraciones solo se ejecutan una vez.

---

### ADD COLUMN IF NOT EXISTS no funciona

**Qué es:**
Sintaxis que funciona en MariaDB pero no en MySQL 8.

**Cómo se ve:**
```
ERROR 1064 (42000): You have an error in your SQL syntax near 'IF NOT EXISTS'
```

**Solución:**
Remueve `IF NOT EXISTS` de los ALTER TABLE. Las migraciones en `database/migrations/` ya están corregidas.

Usa este patrón:
```sql
ALTER TABLE productos ADD COLUMN categoria VARCHAR(60);
```

---

### Unknown column 'imagen' in 'field list'

**Qué es:**
La tabla `productos` no tiene las columnas de categorización.

**Cómo se ve:**
```
SQLSTATE[42S22]: Column not found: Unknown column 'imagen'
```

**Por qué pasa:**
Usaste un `database.sql` antiguo que no tenía las columnas nuevas.

**Solución:**
1. Borra la base de datos:
```sql
DROP DATABASE IF EXISTS gabriel_lacteos;
```

2. Importa el archivo actualizado `database/database.sql` que ya incluye todas las columnas.

---

## Errores de Código PHP

### Class "PHPMailer" not found

**Qué es:**
PHPMailer no está instalado o el autoload no se está cargando.

**Cómo se ve:**
```
Fatal error: Class 'PHPMailer\PHPMailer\PHPMailer' not found
```

**Solución:**
1. Ejecuta `composer install` en la terminal desde la raíz del proyecto.
2. Verifica que existe la carpeta `vendor/`.
3. Verifica que `config/helpers.php` incluye:
```php
require_once __DIR__ . '/../vendor/autoload.php';
```

---

### Error 500 al usar recuperar.php

**Qué es:**
Error al cargar `config/mail.php` que usa `PHPMailer::ENCRYPTION_STARTTLS` antes de cargar la clase.

**Cómo se ve:**
Página en blanco o error 500 sin mensaje visible.

**Por qué pasa:**
`config/mail.php` se carga antes del autoload de Composer, entonces la constante `PHPMailer::ENCRYPTION_STARTTLS` no existe.

**Solución:**
En `config/mail.php`, cambiar:
```php
'SMTP_SECURE' => getenv('SMTP_SECURE') ?: 'tls',
```

En lugar de:
```php
'SMTP_SECURE' => getenv('SMTP_SECURE') ?: PHPMailer::ENCRYPTION_STARTTLS,
```

---

### Require con ruta mal escrita da error 500

**Qué es:**
Un `require` o `include` apunta a una ruta que no existe.

**Ejemplo:**
```php
require __DIR__ . '/../partials/tarjeta_producto.php';
```

Pero la ruta correcta es:
```php
require __DIR__ . '/../views/partials/tarjeta_producto.php';
```

**Cómo se ve:**
Error 500 o mensaje "Failed opening required...".

**Solución:**
Verifica las rutas relativas con `__DIR__`. Usa la estructura:
- Controladores están en `controllers/`
- Vistas están en `views/`
- Modelos están en `models/`

---

### No se pudo enviar el correo

**Qué es:**
PHPMailer no puede conectarse al servidor SMTP.

**Cómo se ve:**
Mensaje flash: "No se pudo enviar el correo de recuperación. Por favor, contacta al administrador."

**Por qué pasa:**
Las credenciales SMTP en `config/mail.php` son incorrectas o de ejemplo.

**Solución:**
1. Edita `config/mail.php` con credenciales reales.
2. Para Gmail, usa una "Contraseña de aplicación", no tu contraseña normal.
3. Para desarrollo, activa el modo debug:
```php
'DEBUG' => true
```
Esto guarda los correos en sesión en lugar de enviarlos.

---

## Errores de Entorno

### MySQL apagado en Laragon

**Cómo se ve:**
```
Error de Conexión a la Base de Datos
SQLSTATE[HY000] [2002] No connection could be made...
```

**Solución:**
1. Abre Laragon.
2. Haz clic derecho en el icono de la bandeja.
3. Selecciona "Start All".
4. Verifica que aparezca "MySQL: Running".

---

### Credenciales incorrectas en database.php

**Cómo se ve:**
```
SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'
```

**Solución:**
Edita `config/database.php`:
```php
private static string $username = 'root';
private static string $password = ''; // Vacío en Laragon/XAMPP
```

En algunos entornos, MySQL tiene contraseña. Verifica con tu configuración local.

---

### Página en blanco sin errores

**Por qué pasa:**
PHP oculta los errores por defecto.

**Solución:**
Activa los errores temporalmente. Al inicio de `index.php` agrega:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

Recuerda quitarlo en producción.

---

### Headers already sent

**Cómo se ve:**
```
Warning: Cannot modify header information - headers already sent
```

**Por qué pasa:**
Hay salida (espacios, HTML, echo) antes de un `header()` o `redirect()`.

**Solución:**
1. Verifica que no haya espacios antes de `<?php` en los archivos PHP.
2. Usa `ob_start()` al inicio si necesitas buffer de salida.
3. Guarda archivos sin BOM en tu editor.

---

### Sesión que no se mantiene

**Cómo se ve:**
Te pide login constantemente, los datos de sesión no persisten.

**Por qué pasa:**
`session_start()` no se está llamando, o hay problemas con los permisos de la carpeta de sesiones.

**Solución:**
En `config/helpers.php` ya tenemos:
```php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
```

Verifica que este código se ejecute en cada request.

---

### Caracteres raros (tildes y ñ aparecen mal)

**Cómo se ve:**
"Queso" aparece como "Queso" o símbolos extraños.

**Solución:**
1. Guarda todos los archivos PHP en UTF-8 sin BOM.
2. Verifica que `database.sql` tenga:
```sql
CREATE DATABASE ... CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
3. En `config/database.php` tenemos:
```php
PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
```

---

### Permisos al subir imágenes

**Cómo se ve:**
```
Warning: move_uploaded_file(): failed to open stream: Permission denied
```

**Solución:**
La carpeta de destino debe tener permisos de escritura. En Linux/Mac:
```bash
chmod 755 public/img/productos/
```

En Windows con Laragon, normalmente no hay problemas de permisos.

---

## Errores de Lógica de Negocio

### "No se puede eliminar el producto porque tiene ventas"

**Qué es:**
Protección de integridad referencial.

**Por qué pasa:**
El producto tiene registros en `detalle_ventas`, y la llave foránea tiene `ON DELETE RESTRICT`.

**Solución:**
No es un error, es el comportamiento correcto. Usa borrado lógico:
```php
$producto->cambiarEstado($id, 'inactivo');
```

---

### Venta guardada pero inventario no disminuyó

**Por qué pasa:**
Probablemente hubo un error en la transacción y se hizo rollback, pero el mensaje de error no se mostró.

**Solución:**
Revisa los logs de error de PHP. La transacción en `Venta->registrarVenta()` debe ejecutar `descontarStock()` dentro del bloque try-catch.

---

## Cómo reportar un error nuevo

Si encuentras un error que no está en esta lista:

1. Copia el mensaje de error completo.
2. Anota qué estabas haciendo cuando ocurrió.
3. Revisa el log de errores de PHP (en Laragon: `C:\laragon\www\php mi proyecto\error.log`).
4. Documenta los pasos para reproducirlo.
5. Agrega la solución a este archivo cuando la encuentres.
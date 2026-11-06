# Módulo 01: Configuración y Base de Datos

Este documento describe detalladamente los archivos encargados de la conexión con el motor de base de datos, las utilidades globales, el envío de correos y la definición del esquema relacional.

---

## 1. `config/database.php`

### Propósito
Proporciona la clase `Database`, encargada de instanciar y entregar una única conexión persistente hacia el servidor MySQL utilizando el patrón de diseño **Singleton**.

### Componentes clave
* **Propiedades estáticas privadas:**
  * `$host`: Dirección del host de la base de datos (por defecto `127.0.0.1`).
  * `$dbName`: Nombre de la base de datos (`gabriel_lacteos`).
  * `$username`: Usuario de acceso (`root`).
  * `$password`: Contraseña del usuario (por defecto vacía para Laragon/XAMPP).
  * `$charset`: Codificación de caracteres (`utf8mb4`).
  * `$connection`: Instancia estática de `PDO` que almacena la conexión activa.

* **Método `getConnection(): PDO`:**
  * Evalúa si `$connection === null`.
  * Configura opciones de PDO:
    * `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`: Convierte cualquier error de SQL en una excepción `PDOException`.
    * `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`: Hace que los resultados se retornen como arreglos asociativos.
    * `PDO::ATTR_EMULATE_PREPARES => false`: Desactiva la emulación para garantizar consultas preparadas nativas en MySQL.
    * `PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"`: Soporte completo para tildes y caracteres internacionales.
  * Si la conexión falla, muestra una alerta visual HTML indicando si MySQL está encendido y si la base de datos existe.

---

## 2. `config/helpers.php`

### Propósito
Contiene las funciones auxiliares de uso global en toda la aplicación (sesiones, autenticación, sanitización de datos, mensajes flash, formateo y envío de correos).

### Configuración inicial
Al inicio del archivo se configura la zona horaria de Colombia:
```php
date_default_timezone_set('America/Bogota');
```

Esto asegura que todas las fechas y horas del sistema (ventas, pedidos, logs) usen la zona horaria correcta.

### Funciones de autenticación y sesión
1. **`is_authenticated(): bool`** — Retorna `true` si existe `$_SESSION['usuario_id']`.
2. **`is_admin(): bool`** — Verifica si el usuario tiene el rol 'administrador'.
3. **`get_user_role(): string`** — Retorna el rol del usuario actual.
4. **`require_auth(): void`** — Redirige a `login.php` si no hay sesión.
5. **`require_admin(): void`** — Verifica que el usuario sea administrador.

### Funciones de utilidad
6. **`set_flash(string $tipo, string $mensaje): void`** — Guarda un mensaje temporal en sesión.
7. **`get_flash(): ?array`** — Recupera y elimina el mensaje flash.
8. **`sanitize(string $data): string`** — Limpia cadenas con `htmlspecialchars` y `trim` para prevenir XSS.
9. **`redirect(string $url): void`** — Envía cabecera HTTP Location. Incluye protección contra Open Redirect.
10. **`formatearMoneda(float|int|string $valor): string`** — Formatea números como moneda colombiana.
11. **`formatearFecha(string $fecha, bool $conHora = true): string`** — Formatea fechas en dd/mm/YYYY.

### Funciones de correo electrónico
12. **`enviar_correo(...)`** — Envía correos usando PHPMailer. Modo debug guarda en sesión.
13. **`enviar_correo_recuperacion(...)`** — Envía el correo de recuperación de contraseña con plantilla HTML.

### Funciones de presentación
14. **`calcularEstadoStock(...)`** — Devuelve estado del inventario (disponible, stock bajo, agotado).
15. **`resolverImagenProducto(array $p): string`** — Resuelve la ruta de imagen del producto.

---

## 3. `config/mail.php`

### Propósito
Archivo de configuración para PHPMailer con las credenciales SMTP.

### Estructura
Retorna un arreglo con:
* `SMTP_HOST`: Servidor SMTP (por defecto `smtp.gmail.com`).
* `SMTP_PORT`: Puerto (por defecto `587`).
* `SMTP_USERNAME`: Usuario del correo.
* `SMTP_PASSWORD`: Contraseña de aplicación.
* `SMTP_SECURE`: Protocolo ('tls' o 'ssl').
* `FROM_EMAIL`, `FROM_NAME`: Datos del remitente.
* `DEBUG`: Si true, los correos se guardan en sesión en lugar de enviarse.

---

## 4. Composer y PHPMailer

### Archivos
* **`composer.json`**: Dependencia `phpmailer/phpmailer` versión ^6.9.
* **`vendor/autoload.php`**: Generado por Composer.

### Instalación
```bash
composer install
```

### Configuración de Gmail
1. Habilita verificación en dos pasos.
2. Genera una "Contraseña de aplicación" en seguridad de Google.
3. Edita `config/mail.php` con esa contraseña.

---

## 5. `database/database.sql`

### Propósito
Script de inicialización DDL/DML para crear la base de datos `gabriel_lacteos`. Se encuentra en `database/` (ya no en la raíz).

### Tablas definidas:
1. **`usuarios`**: `id`, `nombre`, `usuario`, `password`, `email`, `telefono`, `rol`, `token_recuperacion`, `token_expiracion`, `creado_en`. Rol es ENUM('administrador', 'cliente').

2. **`productos`**: Catalogo completo con columnas de categorización:
   * `id`, `codigo`, `nombre`, `tipo`, `categoria`, `tipo_leche`, `intensidad_sabor`, `tiempo_maduracion`, `peso_presentacion`, `origen`, `descripcion`, `imagen_url`, `imagen`, `precio_compra`, `precio_venta`, `cantidad`, `stock_minimo`, `unidad_medida`, `estado`, `creado_en`.
   * Índices en `tipo`, `categoria`, `tipo_leche`, `intensidad_sabor`, `estado`, `imagen`.

3. **`ventas`**, **`detalle_ventas`**, **`proveedores`**, **`pedidos`**, **`detalle_pedidos`**: Tablas para ventas POS, proveedores y pedidos en línea.

---

## 6. `schema.dbml`

### Propósito
Especificación del modelo de datos en formato DBML para generar diagramas ER en dbdiagram.io.
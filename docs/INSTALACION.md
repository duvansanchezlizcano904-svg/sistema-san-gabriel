# Instalación del Sistema Quesos San Gabriel

Este documento explica cómo instalar y configurar el sistema en un entorno local de desarrollo.

## Requisitos previos

- **PHP 8.0 o superior** con las extensiones:
  - `pdo_mysql` (para conexión a MySQL)
  - `mbstring` (para manejo de caracteres UTF-8)
  - `openssl` (para funciones de seguridad)
- **MySQL 8.0 o superior** / MariaDB 10.5+
- **Laragon** (recomendado para Windows) o **XAMPP**
- **Composer** (para instalar PHPMailer)

## Paso 1: Copiar el proyecto

Copia la carpeta del proyecto a la raíz de documentos de Laragon:

```
C:\laragon\www\php mi proyecto
```

Si usas XAMPP, cópiala a:

```
C:\xampp\htdocs\php mi proyecto
```

## Paso 2: Iniciar MySQL

En Laragon, haz clic derecho en el icono de la bandeja y selecciona "Start All". Verifica que MySQL esté corriendo.

## Paso 3: Crear la base de datos

Sigue las instrucciones del archivo [ORDEN_IMPORTACION_BD.md](ORDEN_IMPORTACION_BD.md):

1. Abre phpMyAdmin en `http://localhost/phpmyadmin`
2. Selecciona el servidor MySQL (sin elegir ninguna base de datos)
3. Ve a la pestaña "Importar"
4. Selecciona el archivo `database/database.sql`
5. Haz clic en "Continuar"

Esto crea la base de datos `gabriel_lacteos` con:
- Las tablas necesarias
- Dos usuarios de prueba (admin y cliente)
- 10 productos iniciales

Opcionalmente, importa también `database/seeds/inserts_nuevos_quesos.sql` para agregar 25 productos más.

## Paso 4: Instalar dependencias con Composer

Abre la terminal en la carpeta del proyecto y ejecuta:

```bash
composer install
```

Esto descarga e instala PHPMailer en la carpeta `vendor/`.

Si Composer no está instalado, descárgalo de [getcomposer.org](https://getcomposer.org/).

## Paso 5: Configurar el correo electrónico (opcional pero recomendado)

El sistema usa PHPMailer para enviar correos de recuperación de contraseña. Edita el archivo `config/mail.php`:

### Para Gmail:

1. Ve a tu cuenta de Google → Seguridad
2. Activa la verificación en dos pasos
3. Genera una "Contraseña de aplicación":
   - Selecciona "Correo" y "Otro"
   - Copia la contraseña de 16 caracteres
4. Edita `config/mail.php`:

```php
'SMTP_HOST'     => 'smtp.gmail.com',
'SMTP_PORT'     => 587,
'SMTP_USERNAME' => 'tu-correo@gmail.com',
'SMTP_PASSWORD' => 'xxxx xxxx xxxx xxxx', // Contraseña de aplicación
'SMTP_SECURE'   => 'tls',
'FROM_EMAIL'    => 'tu-correo@gmail.com',
'FROM_NAME'     => 'Quesos San Gabriel',
```

### Para desarrollo (sin correo real):

Cambia `'DEBUG' => true` en `config/mail.php`. Esto guardará los correos en sesión en lugar de enviarlos.

## Paso 6: Verificar la conexión a MySQL

Revisa el archivo `config/database.php` y asegúrate de que las credenciales sean correctas:

```php
private static string $host = '127.0.0.1';
private static string $dbName = 'gabriel_lacteos';
private static string $username = 'root';
private static string $password = ''; // Vacío en Laragon y XAMPP por defecto
```

## Paso 7: Abrir el sistema

Abre tu navegador y ve a:

```
http://localhost/php%20mi%20proyecto/
```

O si configuraste un virtual host:

```
http://quesosgabriel.test/
```

Deberías ver la página de bienvenida pública.

## Paso 8: Probar el inicio de sesión

### Usuario administrador:
- Usuario: `admin`
- Contraseña: `admin123`
- Te redirige a: `index.php?c=dashboard`

### Usuario cliente:
- Email: `cliente@gabriellacteos.com`
- Contraseña: `cliente123`
- Te redirige a: `index.php?c=catalogo`

## Solución de problemas comunes

### "Error de Conexión a la Base de Datos"
- Verifica que MySQL esté corriendo en Laragon.
- Revisa que la base de datos `gabriel_lacteos` exista en phpMyAdmin.
- Verifica las credenciales en `config/database.php`.

### Página en blanco
- Activa los errores de PHP. Agrega al inicio de `index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### "Class 'PHPMailer' not found"
- Ejecuta `composer install` en la terminal.
- Verifica que exista la carpeta `vendor/`.

### Caracteres raros (tildes y ñ)
- Verifica que tu editor guarde los archivos en UTF-8.
- Asegúrate de que la base de datos use `utf8mb4_unicode_ci`.

## Estructura de carpetas

```
php mi proyecto/
├── config/          # Configuración (database, helpers, mail)
├── controllers/     # Controladores MVC
├── models/          # Modelos de datos
├── views/           # Vistas HTML
├── public/          # Assets (CSS, JS, imágenes)
├── database/        # SQL de base de datos
│   ├── migrations/  # Migraciones para BDs antiguas
│   └── seeds/       # Datos de prueba
├── docs/            # Documentación (incluye GUIA_CODIGO_PHP)
├── vendor/          # Dependencias de Composer (no en git)
├── .gitignore       # Archivos excluidos de git
├── composer.json    # Definición de dependencias
├── index.php        # Enrutador principal
├── login.php        # Entrada de login
├── logout.php       # Cierre de sesión
├── publico.php      # Catálogo público
└── recuperar.php    # Recuperación de contraseña
```

## Próximos pasos

1. Lee [BASE_DE_DATOS.md](BASE_DE_DATOS.md) para entender el esquema.
2. Revisa [SEGURIDAD.md](SEGURIDAD.md) para conocer las medidas implementadas.
3. Explora los módulos en `docs/modulos/` para entender cada funcionalidad.
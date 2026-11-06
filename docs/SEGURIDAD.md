# Seguridad del Sistema

Este documento explica las medidas de seguridad implementadas en Quesos San Gabriel.

## 1. Autenticación y contraseñas

### Almacenamiento seguro de contraseñas
Las contraseñas nunca se guardan en texto plano. Usamos `password_hash()` con el algoritmo BCRYPT:

```php
// Al registrar un usuario
$hash = password_hash($password, PASSWORD_BCRYPT);
```

BCRYPT genera un hash único incluso si dos usuarios tienen la misma contraseña, gracias al "salt" aleatorio que incluye automáticamente.

### Verificación de contraseñas
Para validar las credenciales:

```php
// Al hacer login
if (password_verify($passwordIngresado, $hashGuardado)) {
    // Contraseña correcta
}
```

`password_verify()` compara de forma segura sin exponer el hash.

### Prevención de Session Fixation
Cada vez que un usuario inicia sesión, regeneramos el ID de sesión:

```php
session_regenerate_id(true);
```

Esto previene que un atacante fije un ID de sesión conocido antes del login.

## 2. Prevención de inyección SQL

### Consultas preparadas con PDO
Todas las consultas a la base de datos usan prepared statements:

```php
$sql = "SELECT * FROM productos WHERE codigo = :codigo";
$stmt = $this->db->prepare($sql);
$stmt->execute([':codigo' => $codigoIngresado]);
```

PDO envía la consulta y los datos por separado, evitando que un atacante inyecte código SQL malicioso.

### Emulación desactivada
En `config/database.php` configuramos:

```php
PDO::ATTR_EMULATE_PREPARES => false
```

Esto garantiza que las consultas preparadas se ejecuten nativamente en MySQL, no en PHP.

### Parámetros únicos para evitar HY093
Cuando un parámetro se usa varias veces en la misma consulta, usamos nombres diferentes:

```php
// Incorrecto (causa error HY093 con ATTR_EMULATE_PREPARES = false)
$sql = "... WHERE nombre LIKE :busqueda OR codigo LIKE :busqueda";

// Correcto
$sql = "... WHERE nombre LIKE :b1 OR codigo LIKE :b2";
$stmt->execute([':b1' => "%$busqueda%", ':b2' => "%$busqueda%"]);
```

## 3. Prevención de XSS (Cross-Site Scripting)

### Sanitización de entrada
La función `sanitize()` limpia todos los datos del usuario:

```php
function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
```

`htmlspecialchars()` convierte caracteres especiales en entidades HTML, evitando que se ejecute código JavaScript inyectado.

### Escapado en las vistas
En todas las vistas usamos `htmlspecialchars()` o la función `sanitize()`:

```php
<h1><?= htmlspecialchars($producto['nombre']) ?></h1>
```

Nunca imprimimos datos del usuario directamente con `<?= $variable ?>` sin escapar.

## 4. Control de acceso basado en roles (RBAC)

### Verificación de sesión
Funciones helper validan el acceso:

```php
function require_auth(): void {
    if (!is_authenticated()) {
        redirect('login.php');
    }
}

function require_admin(): void {
    require_auth();
    if (!is_admin()) {
        set_flash('danger', 'Acceso denegado.');
        redirect('index.php?c=catalogo');
    }
}
```

Todos los controladores administrativos llaman a `require_admin()` al inicio.

### Sesión segura
Configuramos headers para impedir caché de páginas protegidas:

```php
header("Cache-Control: no-store, no-cache, must-revalidate");
```

Al cerrar sesión, destruimos completamente la sesión y expiramos la cookie.

## 5. Recuperación de contraseña segura

### Token criptográficamente seguro
Generamos tokens con `random_bytes()`:

```php
$token = bin2hex(random_bytes(32)); // 64 caracteres hexadecimales
```

Este token es impredecible y único.

### Expiración del token
El token es válido por 1 hora:

```php
$expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
```

Al validar, verificamos que no haya expirado:

```php
WHERE token_recuperacion = :token AND token_expiracion >= NOW()
```

### Un solo uso
Después de usar el token para cambiar la contraseña, se elimina de la base de datos:

```php
UPDATE usuarios 
SET password = :password, 
    token_recuperacion = NULL, 
    token_expiracion = NULL
```

### Nunca se muestra el enlace en pantalla
El enlace de recuperación se envía exclusivamente por correo electrónico usando PHPMailer. Nunca se muestra en un mensaje flash.

### Prevención de enumeración de usuarios
El sistema muestra el mismo mensaje independientemente de si el correo existe o no en la base de datos: "Si el correo está registrado, te enviamos un enlace para recuperar tu contraseña". Esto previene que un atacante pueda determinar qué correos están registrados en el sistema.

## 6. Protección contra Open Redirect

La función `redirect()` valida que las URLs sean internas:

```php
function redirect(string $path): void {
    $trimmed = trim($path);
    if (preg_match('#^(https?:)?//#i', $trimmed) || 
        preg_match('#^[a-z0-9+.-]+:#i', $trimmed) || 
        $trimmed === '') {
        $path = 'index.php'; // Forzar URL segura
    }
    header("Location: " . $path);
    exit;
}
```

Esto previene que un atacante rediriga a los usuarios a sitios maliciosos.

## 7. Transacciones atómicas

Las ventas y pedidos usan transacciones PDO para garantizar consistencia:

```php
$this->db->beginTransaction();
try {
    // Insertar venta
    // Insertar detalles
    // Descontar inventario
    $this->db->commit();
} catch (Exception $e) {
    $this->db->rollBack();
    throw $e;
}
```

Si algo falla, todos los cambios se revierten. Esto previene:
- Ventas sin detalles
- Detalles sin descontar inventario
- Estados inconsistentes de la base de datos

## 8. Token CSRF contra pedidos duplicados

En el módulo de carrito, usamos un token de sesión para evitar que se procese el mismo pedido dos veces:

```php
// Al mostrar el formulario
$_SESSION['pedido_token'] = bin2hex(random_bytes(16));

// Al procesar el pedido
if ($_POST['token'] !== $_SESSION['pedido_token']) {
    die('Token inválido');
}
unset($_SESSION['pedido_token']); // Consumir token
```

## 9. Validación de subida de imágenes

Aunque actualmente no implementamos subida de archivos, cuando se agregue esta funcionalidad, se validará:

- Tipo MIME real del archivo (no solo la extensión)
- Tamaño máximo permitido
- Renombrado del archivo para evitar sobrescritura
- Almacenamiento fuera del directorio público si contienen datos sensibles

## 10. Headers de seguridad

En producción se recomienda agregar headers adicionales en el servidor web:

```apache
# .htaccess
Header set X-Frame-Options "SAMEORIGIN"
Header set X-Content-Type-Options "nosniff"
Header set X-XSS-Protection "1; mode=block"
```

## Buenas prácticas seguidas

- Separación de credenciales: `config/database.php` y `config/mail.php` no están en el repositorio público.
- Sin información sensible en errores: Los mensajes de error no revelan detalles técnicos al usuario.
- Principio de mínimo privilegio: Los clientes solo pueden ver su catálogo, no modificar productos.
- Validación en servidor: Nunca confiamos en validaciones del cliente (JavaScript).
- Uso de HTTPS: En producción, todo el tráfico debe ser HTTPS.

## Qué NO hace este sistema (y debería en producción)

- Rate limiting en login (prevenir fuerza bruta)
- Captcha en formularios públicos
- Registro de intentos de login fallidos
- Bloqueo de cuentas después de X intentos
- Auditoría completa de acciones administrativas
- Cifrado de datos sensibles en reposo
- Validación de integridad de archivos subidos

Para un entorno de producción real, considera implementar estas medidas adicionales.
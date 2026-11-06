# Módulo de Autenticación

Este módulo gestiona todo lo relacionado con el acceso de usuarios: inicio de sesión, registro, cierre de sesión y recuperación de contraseña.

## ¿Quién lo usa?

- **Visitantes** pueden ver la página pública sin sesión, pero necesitan autenticarse para comprar.
- **Clientes** acceden con su cuenta para ver el catálogo, agregar productos al carrito y hacer pedidos.
- **Administradores** acceden al panel completo con todas las funcionalidades.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Mostrar login | `login.php` | GET | No |
| Procesar login | `login.php` (form POST) | POST | No |
| Mostrar registro | `registro.php` | GET | No |
| Procesar registro | `registro.php` (form POST) | POST | No |
| Cerrar sesión | `logout.php` | GET | Sí |
| Solicitar recuperación | `recuperar.php` (form POST) | POST | No |
| Restablecer contraseña | `login.php?a=restablecer&token=XXX` | GET/POST | No |

## Archivos que lo componen

- **Controlador**: `controllers/AuthController.php`
- **Modelo**: `models/Usuario.php`
- **Vistas**:
  - `views/login/login.php`
  - `views/login/restablecer.php`
  - `views/registro/registro.php`
- **Helpers**: `config/helpers.php` (funciones `is_authenticated`, `is_admin`, `require_auth`, `require_admin`, `set_flash`, `get_flash`)
- **Config de correo**: `config/mail.php`

## Métodos del controlador

### login()
Muestra el formulario de inicio de sesión. Si la petición es POST con `_action=login`, valida las credenciales usando `Usuario->autenticar()`. Si son correctas, regenera el ID de sesión con `session_regenerate_id(true)` y guarda los datos del usuario en `$_SESSION`. Redirige al Dashboard si es admin, o al catálogo si es cliente.

### registro()
Procesa el formulario de registro de nuevos clientes. Valida que todos los campos estén llenos, que el email sea válido, que las contraseñas coincidan y que el email no esté registrado. Genera un nombre de usuario único a partir del email, hashea la contraseña con `password_hash` y guarda el registro.

### logout()
Destruye todas las variables de sesión, elimina la cookie de sesión y redirige a `login.php`. Envía headers para impedir caché.

### recuperar()
Procesa la solicitud de recuperación de contraseña. Recibe el email del formulario, genera un token cryptográficamente seguro con `bin2hex(random_bytes(32))`, guarda el token y su expiración (1 hora) en la tabla usuarios, y envía un correo con PHPMailer usando la función `enviar_correo_recuperacion()`. Por seguridad, muestra el mismo mensaje independientemente de si el correo existe o no, evitando que un atacante pueda enumerar usuarios registrados.

### restablecer()
Muestra el formulario para establecer una nueva contraseña. Valida que el token sea válido y no haya expirado. Procesa el cambio de contraseña, hasheándola y limpiando el token de recuperación.

## Validaciones del servidor

- Campos obligatorios en login: email y password.
- Campos obligatorios en registro: nombre, email, teléfono, password, confirmación de password.
- Validación de formato de email con `FILTER_VALIDATE_EMAIL`.
- Longitud mínima de contraseña: 6 caracteres.
- Las contraseñas se comparan antes de cualquier operación de base de datos.
- El email debe ser único en la tabla usuarios.

## Cómo se protege

- `session_regenerate_id(true)` en cada login para prevenir Session Fixation.
- Las contraseñas nunca se almacenan en texto plano, se usan `password_hash` con BCRYPT.
- El token de recuperación es de un solo uso y expira en 1 hora.
- La función `redirect()` en helpers.php incluye protección contra Open Redirect.
- Headers de no-caché en logout para seguridad.

## Tablas de la base de datos

### usuarios
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT (PK) | Identificador |
| nombre | VARCHAR(100) | Nombre completo |
| usuario | VARCHAR(50) | Nombre de usuario único |
| password | VARCHAR(255) | Hash de la contraseña |
| email | VARCHAR(100) | Correo electrónico |
| telefono | VARCHAR(30) | Teléfono de contacto |
| rol | ENUM | 'administrador' o 'cliente' |
| token_recuperacion | VARCHAR(64) | Token para recuperar contraseña |
| token_expiracion | DATETIME | Fecha de expiración del token |
| creado_en | TIMESTAMP | Fecha de creación |

## Cómo probarlo

### Usuario administrador
1. Ir a `login.php`
2. Ingresar: usuario `admin`, contraseña `admin123`
3. Verificar que redirige al Dashboard (`index.php?c=dashboard`)

### Usuario cliente
1. Ir a `login.php`
2. Ingresar: email `cliente@gabriellacteos.com`, contraseña `cliente123`
3. Verificar que redirige al catálogo (`index.php?c=catalogo`)

### Recuperar contraseña
1. Ir a `login.php`, hacer clic en "¿Olvidaste tu contraseña?"
2. Ingresar un email registrado (ej: `cliente@gabriellacteos.com`) o uno que no exista
3. Verificar que el mensaje siempre dice "Si el correo está registrado, te enviamos un enlace para recuperar tu contraseña"
4. Si SMTP está configurado y el email existe, llegará el correo con el enlace
5. Si SMTP no está configurado, el correo no se enviará pero el mensaje seguirá siendo el mismo

### Registro de nuevo cliente
1. Ir a `registro.php`
2. Llenar el formulario con datos válidos
3. Verificar que se crea la sesión automáticamente.
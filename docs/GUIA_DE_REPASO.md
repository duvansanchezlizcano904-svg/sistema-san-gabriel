# 📚 MEMORIA DE REPASO PARA EVALUACIÓN
## Sistema de Gestión de Inventario — Quesos San Gabriel

---

## 🗺️ MAPA DEL PROYECTO

### **Puntos de Entrada Principales**
| Archivo | Descripción | Requiere Auth |
|---------|-------------|---------------|
| `index.php` | Enrutador principal (?c=controlador&a=accion) | Mixto |
| `login.php` | Formulario de login directo | No |
| `registro.php` | Formulario de registro directo | No |
| `recuperar.php` | Formulario recuperación contraseña | No |
| `logout.php` | Cerrar sesión | Sí |
| `publico.php` | Vista pública de inicio (deprecada, usa index.php) | No |

### **Estructura de Directorios**
```
/config/         → Configuración de BD, helpers, mail
/controllers/    → 11 controladores con lógica de negocio
/models/         → 5 modelos (Usuario, Producto, Venta, Proveedor, Pedido)
/views/          → Vistas PHP organizadas por módulo
/public/         → Assets estáticos (CSS, JS, imágenes)
/database/       → SQL base + migraciones + seeds
/docs/           → Documentación técnica del proyecto
```

---

## 🎯 CONTROLADORES Y RUTAS

### **1. AuthController** (Autenticación)
- **login**: `login.php` o `?c=auth&a=login`
- **registro**: `registro.php` o `?c=auth&a=registro`
- **recuperar**: `recuperar.php` (POST) → Envía token por correo
- **restablecer**: `login.php?a=restablecer&token=...` → Cambiar contraseña con token
- **logout**: `logout.php`

### **2. DashboardController** (Panel Admin)
- **index**: `?c=dashboard` → Métricas generales (ventas, inventario, alertas)

### **3. ProductoController** (Gestión Catálogo Admin)
- **index**: `?c=productos` → Listado con filtros y paginación
- **crear**: `?c=productos&a=crear` → Formulario nuevo producto
- **guardar**: `?c=productos&a=guardar` (POST) → Registrar producto
- **editar**: `?c=productos&a=editar&id=X` → Formulario edición
- **actualizar**: `?c=productos&a=actualizar` (POST) → Guardar cambios
- **cambiarEstado**: `?c=productos&a=cambiarEstado` (POST) → Activar/Inactivar
- **eliminar**: `?c=productos&a=eliminar` (POST) → Borrado físico (solo si no tiene ventas)

### **4. InventarioController** (Visualización Inventario Admin)
- **index**: `?c=inventario` → Tabla de stock actual con alertas

### **5. VentaController** (Registro de Ventas Admin)
- **index**: `?c=ventas` → Formulario nueva venta
- **guardar**: `?c=ventas&a=guardar` (POST) → Registrar venta con transacción

### **6. HistorialController** (Consulta Ventas Admin)
- **index**: `?c=historial` → Listado de ventas con filtros
- **detalle**: `?c=historial&a=detalle&id=X` → Detalle completo de venta
- **factura**: `?c=historial&a=factura&id=X` → Tiquete PDF/imprimible

### **7. ReporteController** (Reportes Analíticos Admin)
- **index**: `?c=reportes` → Gráficas de ventas, top productos, etc.

### **8. ProveedorController** (Gestión Proveedores Admin)
- **index**: `?c=proveedores` → Listado de proveedores
- **crear**: `?c=proveedores&a=crear` → Formulario nuevo proveedor
- **editar**: `?c=proveedores&a=editar&id=X` → Formulario edición

### **9. CatalogoController** (Vista de Productos Cliente)
- **index**: `?c=catalogo` → Catálogo de productos disponibles (solo para clientes auth)

### **10. PublicoController** (Vista Pública Sin Auth)
- **inicio**: `?c=publico` o `index.php` (sin params) → Página home con productos
- **cargarMasProductos**: `?c=publico&a=cargarMasProductos` (AJAX) → Paginación infinita
- **index**: Alias de inicio

### **11. CarritoController** (Carrito de Compras)
**IMPORTANTE:** Funciona SIN autenticación, pero confirmar requiere login
- **index**: `?c=carrito` → Vista del carrito
- **agregar**: `?c=carrito&a=agregar` (POST/AJAX) → Agregar producto
- **actualizar**: `?c=carrito&a=actualizar` (POST/AJAX) → Cambiar cantidad
- **quitar**: `?c=carrito&a=quitar` (POST/AJAX) → Eliminar producto
- **vaciar**: `?c=carrito&a=vaciar` (POST) → Vaciar carrito completo
- **confirmar**: `?c=carrito&a=confirmar` (POST) → Registrar pedido (requiere auth + CSRF)
- **gracias**: `?c=carrito&a=gracias&id=X` → Confirmación post-compra
- **factura**: `?c=carrito&a=factura&id=X` → Tiquete de pedido

---

## 🗄️ BASE DE DATOS

### **Nombre:** `gabriel_lacteos`

### **Tablas Principales**

#### **usuarios**
```sql
id, nombre, usuario, password, email, telefono, rol, 
token_recuperacion, token_expiracion, creado_en
```
- **rol**: `administrador` | `cliente`
- **Usuarios de prueba:**
  - Admin: `admin` / `admin123`
  - Cliente: `cliente` / `cliente123`

#### **productos**
```sql
id, codigo, nombre, tipo, categoria, tipo_leche, intensidad_sabor, 
tiempo_maduracion, peso_presentacion, origen, descripcion, 
imagen_url, imagen, precio_compra, precio_venta, cantidad, 
stock_minimo, unidad_medida, estado, creado_en
```
- **estado**: `activo` | `inactivo` (borrado lógico)
- **intensidad_sabor**: `suave` | `medio` | `fuerte` | `muy fuerte`

#### **ventas**
```sql
id, numero_venta, fecha, total, cliente, notas, usuario_id
```
- **número_venta**: Formato `VNT-00001` (autoincremental)
- **FK:** `usuario_id` → `usuarios.id`

#### **detalle_ventas**
```sql
id, venta_id, producto_id, cantidad, precio_unitario, subtotal
```
- **FK:** `venta_id` → `ventas.id` (CASCADE)
- **FK:** `producto_id` → `productos.id` (RESTRICT)

#### **proveedores**
```sql
id, nombre, contacto, telefono, email, direccion, nit, 
notas, estado, creado_en
```
- **estado**: `activo` | `inactivo`

#### **pedidos** (Compras de Clientes)
```sql
id, numero_pedido, fecha, total, cliente_id, notas, estado
```
- **número_pedido**: Formato `PED-00001`
- **estado**: `pendiente` | `confirmado` | `entregado` | `cancelado`
- **FK:** `cliente_id` → `usuarios.id`

#### **detalle_pedidos**
```sql
id, pedido_id, producto_id, cantidad, precio_unitario, subtotal
```
- **FK:** `pedido_id` → `pedidos.id` (CASCADE)
- **FK:** `producto_id` → `productos.id` (RESTRICT)

---

## 🛠️ FUNCIONES HELPERS (config/helpers.php)

### **Autenticación y Seguridad**
- `is_authenticated()` → bool: ¿Usuario logueado?
- `require_auth()` → Forzar login o redirigir
- `is_admin()` → bool: ¿Es administrador?
- `is_cliente()` → bool: ¿Es cliente?
- `require_admin()` → Forzar admin o negar acceso
- `get_user_role()` → string: 'administrador' | 'cliente'
- `enviar_headers_sin_cache()` → Headers HTTP para evitar caché

### **Validación y Sanitización**
- `sanitize($data)` → string: Limpia XSS con htmlspecialchars()

### **Mensajes Flash**
- `set_flash($tipo, $mensaje)` → Guarda mensaje en sesión
  - **Tipos:** `success` | `danger` | `warning` | `info`
- `get_flash()` → array|null: Obtiene y elimina mensaje

### **Navegación**
- `base_url($path)` → string: URL completa del proyecto
- `redirect($path)` → Redirige con protección Open Redirect

### **Formateo**
- `formatearMoneda($valor)` → string: `$ 15.000,00`
- `formatearFecha($fecha, $conHora)` → string: `23/10/2026 02:30 PM`
- `calcularEstadoStock($cantidad, $stockMinimo)` → array: Badge y estado
- `resolverImagenProducto($producto)` → string: Ruta de imagen

### **Correo Electrónico (PHPMailer)**
- `enviar_correo($destinatario, $nombre, $asunto, $cuerpoHtml)` → bool
- `enviar_correo_recuperacion($email, $nombre, $token)` → bool

### **Configuración**
- `date_default_timezone_set('America/Bogota')` → Zona horaria Colombia

---

## ⚠️ PUNTOS FRÁGILES Y ERRORES COMUNES

### **1. PDO sin Emulación (ATTR_EMULATE_PREPARES => false)**
- **Problema:** MySQL requiere nombres ÚNICOS de parámetros en prepared statements
- **Error común:**
  ```php
  // ❌ MAL - parámetro repetido
  WHERE nombre LIKE :busqueda OR descripcion LIKE :busqueda
  
  // ✅ BIEN - parámetros únicos
  WHERE nombre LIKE :b1 OR descripcion LIKE :b2
  ```
- **Ubicación:** Modelos (Usuario, Producto, Venta, Proveedor, Pedido)

### **2. Sesiones y Autenticación**
- **Error:** No llamar `require_auth()` o `require_admin()` en constructores
- **Consecuencia:** Usuarios no autenticados acceden a módulos protegidos
- **Ubicación:** Todos los controladores excepto Auth, Carrito (parcial), Publico

### **3. Carrito Sin Sesión**
- **Error:** Bloquear carrito si no hay sesión activa
- **Correcto:** Carrito funciona ANÓNIMO hasta "Confirmar compra"
- **Protección:** Solo `confirmar()` requiere autenticación + CSRF token

### **4. Validación de Stock**
- **Error:** Confiar en cantidad del carrito sin validar en BD
- **Correcto:** Siempre verificar `productos.cantidad` en MySQL antes de vender
- **Ubicación:** `CarritoController::confirmar()`, `VentaController::guardar()`

### **5. Transacciones Atómicas**
- **Error:** No usar `beginTransaction()` / `commit()` / `rollBack()`
- **Consecuencia:** Venta registrada pero stock no descontado (inconsistencia)
- **Ubicación:** `Venta::registrarVenta()`, `Pedido::registrarPedido()`

### **6. CSRF en Pedidos**
- **Error:** No verificar token antes de confirmar compra
- **Consecuencia:** Doble compra por F5 o doble clic
- **Protección:** Token en `$_SESSION['pedido_csrf_token']`, validar con `hash_equals()`

### **7. Borrado Lógico vs Físico**
- **Error:** Eliminar productos que tienen ventas históricas (FK RESTRICT)
- **Correcto:** Cambiar estado a 'inactivo' en lugar de DELETE
- **Ubicación:** `Producto::eliminar()` verifica detalle_ventas primero

### **8. Imágenes de Productos**
- **Error:** No validar tipo MIME real (confiar solo en extensión)
- **Correcto:** Usar `finfo_file()` para verificar tipo real
- **Ubicación:** `ProductoController::procesarImagen()`

### **9. Open Redirect**
- **Error:** Aceptar cualquier URL en parámetro `redirect`
- **Protección:** Validar con regex en `redirect()` helper
- **Ubicación:** `helpers.php::redirect()`

### **10. Recuperación de Contraseña**
- **Error:** Revelar si un email existe en la BD
- **Correcto:** Mensaje genérico siempre (seguridad)
- **Ubicación:** `AuthController::recuperar()`

### **11. Timezone**
- **Error:** No configurar zona horaria → fechas en UTC
- **Correcto:** `date_default_timezone_set('America/Bogota')` en helpers.php
- **Impacto:** Fechas de ventas y pedidos con hora incorrecta

### **12. Headers de Caché**
- **Error:** Navegador guarda páginas de login/admin y permite volver con Back
- **Correcto:** `enviar_headers_sin_cache()` en páginas protegidas
- **Ubicación:** `require_auth()`, `logout()`

### **13. Regeneración de ID de Sesión**
- **Error:** No regenerar ID tras login → Session Fixation
- **Correcto:** `session_regenerate_id(true)` después de autenticar
- **Ubicación:** `AuthController::login()`, `AuthController::registro()`

### **14. Rutas de Imagen**
- **Error:** Hardcodear rutas absolutas → no funcionan en subdirectorios
- **Correcto:** Usar rutas relativas desde raíz del proyecto
- **Ejemplo:** `public/img/productos/queso.jpg` (sin `/` inicial)

### **15. Paginación con PDO Nativo**
- **Error:** Pasar variables directas a LIMIT/OFFSET con prepare nativo
- **Correcto:** Castear a int y usar interpolación directa o bindValue con PDO::PARAM_INT
- **Ubicación:** `Producto::listarConPaginacion()`

---

## 📋 FORMATO DE RESPUESTA PARA DEBUGGEAR

Cuando el usuario reporte un error, responder en este formato ESTRICTO:

```
Error: [Descripción breve del problema identificado]

Corrección: [Código exacto o cambio a realizar]

Por qué: [Explicación técnica en 1-2 líneas]
```

### **REGLAS DE RESPUESTA:**
- ❌ NO usar emojis
- ❌ NO cambiar archivos automáticamente sin confirmar
- ❌ NO dar explicaciones largas, ser DIRECTO
- ✅ Código listo para copiar y pegar
- ✅ Indicar archivo y línea exacta
- ✅ Explicación técnica corta

### **Ejemplo:**
```
Error: La venta se registra pero el inventario no se descuenta

Corrección: En models/Venta.php línea 89, falta llamar $this->db->beginTransaction() antes del INSERT

Por qué: Sin transacción PDO, si falla el descuento de stock la venta queda registrada causando inconsistencia
```

---

## 🔍 FLUJOS CRÍTICOS A REPASAR

### **Flujo 1: Compra Anónima (Sin Login)**
1. Usuario entra a `index.php` (sin params) → `PublicoController::inicio()`
2. Navega productos, clic "Agregar al Carrito" → AJAX a `?c=carrito&a=agregar`
3. Carrito guarda en `$_SESSION['carrito']` (sin requerir auth)
4. Ve carrito: `?c=carrito` → Lista productos + botón "Confirmar Compra"
5. Clic confirmar → Redirige a `login.php` con mensaje "Debes iniciar sesión"
6. Usuario se registra o loguea
7. Tras login exitoso, redirige a `?c=carrito` automáticamente
8. Confirma compra → `?c=carrito&a=confirmar` (POST con CSRF)
9. Se crea pedido en BD, descuenta stock con transacción
10. Redirige a `?c=carrito&a=gracias&id=X`

### **Flujo 2: Login y Seguridad**
1. Usuario en `login.php`, envía POST con email/password
2. `AuthController::login()` llama `Usuario::autenticar()`
3. Valida con `password_verify()` contra hash en BD
4. Si OK: `session_regenerate_id(true)` (anti Session Fixation)
5. Guarda datos en `$_SESSION`: id, nombre, email, rol
6. Redirige según rol:
   - Admin → `?c=dashboard`
   - Cliente → `?c=catalogo` (o `?c=carrito` si hay productos pendientes)

### **Flujo 3: Recuperación de Contraseña**
1. Usuario en `login.php`, clic "¿Olvidaste tu contraseña?"
2. POST a `recuperar.php` con email
3. `AuthController::recuperar()` genera token aleatorio (64 chars hex)
4. Guarda en `usuarios.token_recuperacion` + expira en 1 hora
5. Envía correo con PHPMailer: `enviar_correo_recuperacion()`
6. Usuario clic en enlace: `login.php?a=restablecer&token=XXXX`
7. `AuthController::restablecer()` valida token y expiracion
8. Usuario ingresa nueva password (POST)
9. `Usuario::actualizarPasswordPorToken()` hashea y actualiza
10. Limpia token y redirige a login

### **Flujo 4: Venta con Transacción (Admin)**
1. Admin en `?c=ventas`, selecciona productos y cantidades
2. POST a `?c=ventas&a=guardar`
3. `VentaController::guardar()` llama `Venta::registrarVenta()`
4. **Inicia transacción:** `$this->db->beginTransaction()`
5. Valida stock disponible para TODOS los productos primero
6. INSERT en `ventas` (cabecera)
7. Para cada producto:
   - INSERT en `detalle_ventas`
   - UPDATE `productos SET cantidad = cantidad - X`
8. Si todo OK: `commit()`, si error: `rollBack()`
9. Redirige a `?c=historial&a=detalle&id=X`

---

## 🎓 RECORDATORIOS FINALES

- **Siempre validar en el servidor:** No confiar en validaciones JavaScript
- **Stock real desde MySQL:** Nunca confiar en sesión o input del usuario
- **Transacciones obligatorias:** Para operaciones que modifican múltiples tablas
- **CSRF tokens:** En formularios que modifican datos críticos
- **Sanitización:** `sanitize()` en TODOS los inputs de usuario
- **Mensajes genéricos:** No revelar información sensible en errores públicos
- **Testing de rutas:** Probar URLs directas sin pasar por frontend
- **Roles bien definidos:** Admin y Cliente tienen accesos distintos

---

**✅ Archivo de memoria creado. Úsalo para consultas rápidas durante la evaluación.**


---

## 💥 20 ERRORES TÍPICOS DE EVALUACIÓN (VISIBLE EN PANTALLA)

### 1. Falta punto y coma al final de require
Error: En controllers/ProductoController.php línea 7, require_once __DIR__ . '/../models/Producto.php' sin punto y coma final

Corrección: Agregar ; al final: require_once __DIR__ . '/../models/Producto.php';

Por qué: PHP interpreta la siguiente línea como continuación causando error de sintaxis

Cómo se ve: Parse error: syntax error, unexpected 'class' (T_CLASS) in ProductoController.php on line 9

### 2. Nombre de base de datos mal escrito
Error: En config/database.php línea 9, $dbName = 'gabriel_lacteo' (sin s final)

Corrección: Cambiar por $dbName = 'gabriel_lacteos';

Por qué: PDO no puede conectar a una base de datos que no existe

Cómo se ve: Pantalla de error roja con mensaje Error de Conexión a la Base de Datos y SQLSTATE[HY000] [1049] Unknown database 'gabriel_lacteo'

### 3. Contraseña de BD incorrecta
Error: En config/database.php línea 11, $password = '123456' en lugar de cadena vacía

Corrección: Cambiar por $password = '';

Por qué: Laragon usa root sin contraseña por defecto

Cómo se ve: Error de conexión con Access denied for user 'root'@'localhost' (using password: YES)

### 4. Controlador mal escrito en array de index.php
Error: En index.php línea 45, 'productos' => 'ProductoControler' (falta doble l)

Corrección: Cambiar por 'productos' => 'ProductoController';

Por qué: PHP busca una clase que no existe

Cómo se ve: Fatal error: Uncaught Error: Class 'ProductoControler' not found in index.php:57

### 5. Ruta de require mal escrita
Error: En controllers/VentaController.php línea 6, require_once __DIR__ . '/../model/Venta.php' (falta s en models)

Corrección: Cambiar por require_once __DIR__ . '/../models/Venta.php';

Por qué: PHP no encuentra el archivo en la ruta especificada

Cómo se ve: Warning: require_once(c:\laragon\www\php mi proyecto\model\Venta.php): Failed to open stream: No such file or directory

### 6. Nombre de columna mal escrito en SELECT
Error: En models/Usuario.php línea 19, SELECT id, nombre, usuario, passworld (en lugar de password)

Corrección: Cambiar passworld por password

Por qué: La columna no existe en la tabla usuarios

Cómo se ve: Fatal error: Uncaught PDOException: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'passworld' in 'field list'

### 7. Name del input diferente al $_POST
Error: En views/productos/crear.php línea 85, input name="precio_compras" pero en ProductoController.php usa $_POST['precio_compra']

Corrección: Cambiar name="precio_compras" por name="precio_compra" en la vista

Por qué: PHP no encuentra la clave en el array $_POST y devuelve valor vacío o null

Cómo se ve: Validación falla con El precio de compra no puede ser negativo (porque floatval(null) = 0) o producto se registra con precio 0.00

### 8. Parámetro PDO no coincide con execute
Error: En models/Producto.php línea 52, SQL usa WHERE id = :id pero execute recibe [':producto_id' => $id]

Corrección: Cambiar execute([':producto_id' => $id]) por execute([':id' => $id])

Por qué: PDO no encuentra el parámetro nombrado en el array

Cómo se ve: Fatal error: Uncaught PDOException: SQLSTATE[HY093]: Invalid parameter number: parameter was not defined

### 9. Método del controlador no existe
Error: En index.php línea 67, se llama $instancia->$accion() pero ProductoController no tiene el método listar, solo index

Corrección: Cambiar URL de ?c=productos&a=listar a ?c=productos&a=index

Por qué: PHP intenta llamar un método inexistente

Cómo se ve: Error interno: La acción 'listar' no existe en ProductoController (o si no hay validación: Fatal error: Call to undefined method ProductoController::listar())

### 10. Variable no enviada desde controlador a vista
Error: En controllers/DashboardController.php línea 35, no se define $menuActivo = 'dashboard' pero la vista lo usa en el menú de navegación

Corrección: Agregar $menuActivo = 'dashboard'; antes del require de la vista

Por qué: La vista intenta usar una variable indefinida

Cómo se ve: Warning: Undefined variable $menuActivo in views/layouts/header.php on line 42 y el menú no marca ninguna opción como activa

### 11. Llave de cierre faltante en función
Error: En models/Producto.php línea 180, la función obtenerEstadisticas() no tiene llave de cierre }

Corrección: Agregar } al final de la función antes de la siguiente

Por qué: PHP interpreta todo el resto del archivo como parte de la función

Cómo se ve: Parse error: syntax error, unexpected end of file, expecting "}" in Producto.php

### 12. Paréntesis sin cerrar en llamada de función
Error: En controllers/CarritoController.php línea 125, set_flash('success', 'Producto agregado' sin paréntesis final

Corrección: Agregar ); al final: set_flash('success', 'Producto agregado');

Por qué: PHP espera que continúe la expresión

Cómo se ve: Parse error: syntax error, unexpected ';', expecting ')' in CarritoController.php on line 126

### 13. session_start faltante
Error: En config/helpers.php línea 8, comentado o eliminado el if (session_status() === PHP_SESSION_NONE) session_start();

Corrección: Descomentar o agregar el bloque de session_start al inicio del archivo

Por qué: PHP no puede leer ni escribir variables de sesión sin inicializarla

Cómo se ve: Warning: Undefined variable $_SESSION y el login no funciona, el carrito se vacía entre páginas

### 14. Nombre de tabla mal escrito en consulta
Error: En models/Venta.php línea 95, INSERT INTO venta (sin s) VALUES...

Corrección: Cambiar INSERT INTO venta por INSERT INTO ventas

Por qué: La tabla no existe en la base de datos

Cómo se ve: Fatal error: Uncaught PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 Table 'gabriel_lacteos.venta' doesn't exist

### 15. Error en JavaScript del carrito
Error: En public/js/app.js línea 45, document.getElementById('btn-agregar').addEventListner (falta e en addEventListener)

Corrección: Cambiar addEventListner por addEventListener

Por qué: El método no existe en JavaScript

Cómo se ve: Consola del navegador muestra Uncaught TypeError: document.getElementById(...).addEventListner is not a function y el botón Agregar al Carrito no responde al clic

### 16. Tag PHP sin cerrar
Error: En views/productos/index.php línea 125, <?php echo $producto['nombre']; (falta ?>)

Corrección: Agregar ?> al final o mejor usar sintaxis corta <?= $producto['nombre'] ?>

Por qué: PHP interpreta todo el HTML siguiente como código PHP

Cómo se ve: Parse error: syntax error, unexpected end of file, expecting ";" or "}" in views/productos/index.php

### 17. Falta comilla en query SQL
Error: En models/Proveedor.php línea 38, UPDATE proveedores SET nombre = :nombre WHERE id = :id' (comilla simple extra al final)

Corrección: Quitar la comilla simple final después de :id

Por qué: La cadena SQL queda mal formada

Cómo se ve: Fatal error: Uncaught PDOException: SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax

### 18. Método de modelo no llamado correctamente
Error: En controllers/VentaController.php línea 48, $this->modeloVenta->registrar($datosVenta, $items) pero el método se llama registrarVenta

Corrección: Cambiar ->registrar por ->registrarVenta

Por qué: El método registrar no existe en el modelo

Cómo se ve: Fatal error: Uncaught Error: Call to undefined method Venta::registrar() in VentaController.php

### 19. Variable en loop no inicializada
Error: En views/historial/index.php línea 58, <?php foreach($ventas as $venta): ?> pero el controlador envía $listaVentas

Corrección: Cambiar foreach($ventas as $venta) por foreach($listaVentas as $venta) o cambiar la variable en el controlador

Por qué: La variable $ventas no existe en el scope de la vista

Cómo se ve: Warning: Undefined variable $ventas in views/historial/index.php on line 58 y Warning: foreach() argument must be of type array, null given, la tabla queda vacía

### 20. Operador de concatenación mal escrito
Error: En controllers/ProductoController.php línea 145, $sql = "UPDATE productos SET codigo = :codigo" + "nombre = :nombre" (usa + en lugar de .)

Corrección: Cambiar + por . : $sql = "UPDATE productos SET codigo = :codigo" . " nombre = :nombre"

Por qué: En PHP el operador de concatenación de strings es punto, no suma

Cómo se ve: Fatal error: Uncaught TypeError: Unsupported operand types: string + string en ProductoController.php (PHP 8+) o Warning y SQL mal formado (PHP 7)

---

## 🎯 PRÁCTICA DE DIAGNÓSTICO

Reglas:
1. Te describo el síntoma (lo que aparece en pantalla)
2. Tú diagnosticas dónde está el error (archivo, línea aproximada, qué buscar)
3. Yo te digo si acertaste o te doy pistas
4. Repetimos hasta que domines el diagnóstico rápido

**Listos para empezar la práctica.**

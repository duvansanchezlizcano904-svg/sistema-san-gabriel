# Flujo de Compra con Login - Quesos San Gabriel

## 📋 Resumen del Flujo

Este documento describe el flujo completo para que un usuario **agregue productos al carrito SIN estar autenticado** y luego **se le pida login al momento de confirmar la compra**.

---

## 🎯 Casos de Uso

### Caso 1: Usuario Anónimo → Agregar Productos → Login → Comprar

```
1. Usuario visita index.php o publico.php (sin login)
2. Navega por el catálogo de productos
3. Agrega productos al carrito
4. Ve su carrito con productos agregados
5. Intenta confirmar pedido
6. Sistema solicita iniciar sesión
7. Usuario inicia sesión o se registra
8. Sistema redirige automáticamente al carrito
9. Usuario completa la compra
```

### Caso 2: Usuario Autenticado desde el Inicio

```
1. Usuario inicia sesión primero
2. Navega por el catálogo
3. Agrega productos al carrito
4. Confirma pedido directamente (sin solicitar login de nuevo)
```

### Caso 3: Administrador intenta usar el carrito

```
1. Admin inicia sesión
2. Puede ver el catálogo
3. NO puede agregar al carrito de cliente
4. Debe usar el módulo de Ventas para facturar
```

---

## 🔄 Flujo Técnico Detallado

### 1️⃣ Agregar Productos (SIN AUTENTICACIÓN)

**Usuario**: Anónimo (sin sesión iniciada)

**Página**: `index.php` o `publico.php`

**Acción**: Click en botón "Comprar" o "Agregar al carrito"

```http
POST index.php?c=carrito&a=agregar
Content-Type: application/x-www-form-urlencoded

producto_id=5
cantidad=2
redirect=index.php
```

**Procesamiento**:
1. `index.php` recibe la petición
2. Detecta que el usuario NO está autenticado
3. Verifica lista blanca (líneas 10-25):
   - Controlador: `carrito`
   - Acción: `agregar` ✅ PERMITIDA
4. Instancia `CarritoController`
5. Ejecuta método `agregar()`
6. `CarritoController->__construct()` inicia sesión PHP si no existe
7. Valida `producto_id` y `cantidad`
8. Consulta producto en BD
9. Verifica stock disponible
10. Agrega/actualiza `$_SESSION['carrito']`
11. Establece mensaje flash de éxito
12. Redirige a la página con mensaje de confirmación

**Resultado**: 
- ✅ Producto agregado al carrito
- ✅ Sesión PHP creada con carrito
- ✅ Usuario sigue sin autenticación
- ✅ Puede seguir agregando productos

---

### 2️⃣ Ver Carrito (SIN AUTENTICACIÓN)

**URL**: `index.php?c=carrito` o `index.php?c=carrito&a=index`

**Vista**: `views/carrito/index.php`

**Contenido mostrado**:
- ✅ Lista de productos agregados
- ✅ Controles para modificar cantidades (+/-)
- ✅ Botón para eliminar productos
- ✅ Subtotal por producto
- ✅ Total general
- ✅ **Panel lateral con opciones de autenticación**:
  - Botón "Iniciar Sesión" (redirige a `login.php`)
  - Botón "Crear Cuenta Nueva" (redirige a `registro.php`)
  - Mensaje explicativo de por qué necesita una cuenta

**Código implementado** (líneas 140-192 de `views/carrito/index.php`):

```php
<?php if (is_authenticated() && !is_admin()): ?>
    <!-- Formulario de confirmación -->
    <form method="POST" action="index.php?c=carrito&a=confirmar">
        <!-- campos del formulario -->
        <button type="submit">Confirmar pedido</button>
    </form>

<?php elseif (is_authenticated() && is_admin()): ?>
    <!-- Mensaje para administrador -->
    <div class="alert alert-warning">
        Los administradores no pueden realizar pedidos como clientes.
    </div>

<?php else: ?>
    <!-- Usuario NO autenticado: solicitar login -->
    <div class="alert alert-info">
        Para completar tu compra necesitas iniciar sesión.
    </div>
    <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
    <a href="registro.php" class="btn btn-outline-primary">Crear Cuenta</a>
<?php endif; ?>
```

---

### 3️⃣ Modificar Cantidades (SIN AUTENTICACIÓN)

**Acción**: Click en botones +/- en el carrito

```http
POST index.php?c=carrito&a=actualizar
Content-Type: application/x-www-form-urlencoded

producto_id=5
cantidad=3
ajax=1
```

**Procesamiento**:
- Lista blanca: `actualizar` ✅ PERMITIDA sin autenticación
- Actualiza `$_SESSION['carrito']`
- Responde con JSON (si es AJAX) o redirige
- Usuario sigue sin autenticación

**Resultado**: Cantidad actualizada en sesión

---

### 4️⃣ Eliminar Productos (SIN AUTENTICACIÓN)

**Acción**: Click en botón "Quitar" (icono de basura)

```http
POST index.php?c=carrito&a=quitar
producto_id=5
```

**Procesamiento**:
- Lista blanca: `quitar` ✅ PERMITIDA sin autenticación
- Elimina producto de `$_SESSION['carrito']`
- Redirige con mensaje de confirmación

---

### 5️⃣ Intentar Confirmar Pedido (SIN AUTENTICACIÓN)

**Escenario**: Usuario anónimo tiene productos en el carrito y hace click en "Confirmar pedido"

**Comportamiento ACTUAL**:
- El botón "Confirmar pedido" **NO está visible** si no está autenticado
- En su lugar, ve botones de "Iniciar Sesión" y "Crear Cuenta"

**Si el usuario intenta acceder directamente a la URL**:
```http
POST index.php?c=carrito&a=confirmar
```

**Procesamiento**:
1. `CarritoController->confirmar()` línea 256
2. Ejecuta `require_auth()` (helper.php línea 73)
3. `require_auth()` verifica `is_authenticated()`
4. Como retorna `false`, ejecuta:
   ```php
   set_flash('warning', 'Debes iniciar sesión para acceder al sistema.');
   redirect('login.php');
   ```
5. Usuario es redirigido a `login.php`
6. **El carrito se mantiene en `$_SESSION['carrito']`** ✅

---

### 6️⃣ Usuario Inicia Sesión

**Página**: `login.php`

**Formulario**:
```html
<form method="POST" action="login.php">
    <input type="hidden" name="_action" value="login">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Iniciar Sesión</button>
</form>
```

**Procesamiento** (AuthController.php líneas 27-52):
1. Valida credenciales
2. Consulta usuario en BD
3. Si es válido:
   - Regenera ID de sesión (seguridad)
   - Guarda datos en `$_SESSION['usuario_*']`
   - **NUEVO**: Verifica si hay carrito
   ```php
   $hayCarrito = !empty($_SESSION['carrito']);
   ```
4. Establece mensaje flash de bienvenida
5. Redirige según rol y carrito:
   - **Admin**: → `index.php?c=dashboard`
   - **Cliente CON carrito**: → `index.php?c=carrito` ✅
   - **Cliente SIN carrito**: → `index.php`

**Resultado**: 
- Usuario autenticado
- Redirigido automáticamente al carrito si tenía productos
- Mensaje: "¡Bienvenido de nuevo, [Nombre]! Ahora puedes completar tu compra."

---

### 7️⃣ Usuario se Registra (Alternativa al Login)

**Página**: `registro.php`

**Procesamiento** (AuthController.php líneas 93-128):
1. Valida datos del formulario
2. Verifica que el email no esté registrado
3. Crea nuevo usuario en BD
4. Inicia sesión automáticamente
5. **NUEVO**: Verifica si hay carrito
6. Redirige:
   - **CON carrito**: → `index.php?c=carrito` ✅
   - **SIN carrito**: → `index.php#productos`

**Mensaje**: "¡Bienvenido a Quesos San Gabriel, [Nombre]! Ahora puedes completar tu compra."

---

### 8️⃣ Confirmar Pedido (AUTENTICADO)

**Requisito**: Usuario debe estar autenticado como cliente

**Vista del carrito ahora muestra**:
```php
<form method="POST" action="index.php?c=carrito&a=confirmar">
    <textarea name="notas" placeholder="Dirección de entrega..."></textarea>
    <button type="submit">Confirmar pedido</button>
</form>
```

**Procesamiento** (CarritoController.php líneas 256-295):
1. `require_auth()` → ✅ Usuario autenticado
2. Valida que el método sea POST
3. Verifica que el carrito no esté vacío
4. Verifica que NO sea administrador
5. Prepara datos del pedido
6. Llama a `$this->modeloPedido->registrarPedido()`
7. El modelo:
   - Inserta en tabla `pedidos`
   - Inserta detalles en `pedidos_detalles`
   - Descuenta stock de productos
   - Retorna ID del pedido
8. Vacía `$_SESSION['carrito']`
9. Establece mensaje de éxito
10. Redirige a página de confirmación:
    ```
    index.php?c=carrito&a=gracias&id=[pedido_id]
    ```

**Resultado**:
- ✅ Pedido registrado en BD
- ✅ Stock descontado
- ✅ Carrito vaciado
- ✅ Usuario ve página de confirmación con número de pedido

---

## 🧪 Casos de Prueba

### ✅ CP-01: Agregar productos sin login

**Pasos**:
1. Abrir navegador en modo incógnito
2. Navegar a `http://localhost/php%20mi%20proyecto/index.php`
3. NO iniciar sesión
4. Buscar producto "Queso Campesino"
5. Seleccionar cantidad: 2
6. Click en "Comprar"

**Resultado Esperado**:
- ✅ Producto agregado al carrito
- ✅ Mensaje: "Queso Campesino agregado al carrito"
- ✅ Contador de carrito muestra: 2
- ✅ Usuario sigue sin autenticación

---

### ✅ CP-02: Ver carrito sin login

**Pasos**:
1. Continuar desde CP-01
2. Click en botón "Carrito" en navbar

**Resultado Esperado**:
- ✅ Se muestra vista del carrito
- ✅ Lista muestra "Queso Campesino x2"
- ✅ Se muestra total calculado
- ✅ Panel lateral muestra:
  - ❌ NO hay botón "Confirmar pedido"
  - ✅ HAY botón "Iniciar Sesión"
  - ✅ HAY botón "Crear Cuenta Nueva"
  - ✅ Mensaje explicativo: "Para completar tu compra..."

---

### ✅ CP-03: Agregar más productos sin login

**Pasos**:
1. Continuar desde CP-02
2. Click en "Seguir comprando"
3. Agregar otro producto diferente
4. Volver al carrito

**Resultado Esperado**:
- ✅ Carrito muestra 2 productos
- ✅ Total actualizado
- ✅ Sigue sin poder confirmar (sin login)

---

### ✅ CP-04: Modificar cantidades sin login

**Pasos**:
1. En el carrito, click en botón "+"
2. Cantidad incrementa de 2 a 3

**Resultado Esperado**:
- ✅ Cantidad actualizada
- ✅ Subtotal recalculado
- ✅ Total general actualizado
- ✅ Sin recargar página (AJAX)

---

### ✅ CP-05: Intentar confirmar sin login

**Pasos**:
1. Intentar acceder directamente:
   ```
   http://localhost/php%20mi%20proyecto/index.php?c=carrito&a=confirmar
   ```

**Resultado Esperado**:
- ✅ Redirige a `login.php`
- ✅ Mensaje: "Debes iniciar sesión para acceder al sistema"
- ✅ Carrito se mantiene en sesión

---

### ✅ CP-06: Login con carrito activo

**Pasos**:
1. Tener productos en carrito sin login
2. Click en "Iniciar Sesión"
3. Ingresar credenciales válidas de cliente
4. Submit

**Resultado Esperado**:
- ✅ Login exitoso
- ✅ Mensaje: "¡Bienvenido de nuevo, [Nombre]! Ahora puedes completar tu compra."
- ✅ Redirige automáticamente a `index.php?c=carrito`
- ✅ Carrito mantiene los productos agregados
- ✅ Ahora SÍ muestra botón "Confirmar pedido"

---

### ✅ CP-07: Confirmar pedido autenticado

**Pasos**:
1. Continuar desde CP-06
2. Revisar productos en carrito
3. Agregar nota: "Entregar en la tarde"
4. Click en "Confirmar pedido"

**Resultado Esperado**:
- ✅ Pedido creado en BD
- ✅ Stock descontado
- ✅ Carrito vaciado
- ✅ Redirige a página de confirmación
- ✅ Muestra número de pedido
- ✅ Opción para ver/imprimir factura

---

### ✅ CP-08: Registro con carrito activo

**Pasos**:
1. Agregar productos sin login
2. Click en "Crear Cuenta Nueva"
3. Llenar formulario de registro
4. Submit

**Resultado Esperado**:
- ✅ Cuenta creada
- ✅ Login automático
- ✅ Redirige a `index.php?c=carrito`
- ✅ Mensaje: "¡Bienvenido a Quesos San Gabriel, [Nombre]! Ahora puedes completar tu compra."
- ✅ Carrito mantiene productos

---

### ✅ CP-09: Admin no puede usar carrito de cliente

**Pasos**:
1. Agregar productos sin login
2. Iniciar sesión como administrador
3. Ver carrito

**Resultado Esperado**:
- ✅ Carrito muestra productos
- ✅ Panel lateral muestra mensaje:
  "Los administradores no pueden realizar pedidos como clientes"
- ✅ NO hay botón "Confirmar pedido"
- ✅ Admin debe usar módulo "Ventas"

---

### ✅ CP-10: Persistencia del carrito

**Pasos**:
1. Agregar productos sin login
2. Navegar a `publico.php`
3. Agregar más productos
4. Navegar a `index.php`
5. Ver carrito

**Resultado Esperado**:
- ✅ Todos los productos están en el carrito
- ✅ Contador correcto en todas las páginas
- ✅ Carrito se mantiene mientras la sesión esté activa

---

## 📁 Archivos Modificados

### 1. `views/carrito/index.php` (líneas 140-192)
**Cambio**: Condicional para mostrar botones según estado de autenticación

```php
<?php if (is_authenticated() && !is_admin()): ?>
    <!-- Formulario de confirmación -->
<?php elseif (is_authenticated() && is_admin()): ?>
    <!-- Mensaje para admin -->
<?php else: ?>
    <!-- Botones de login/registro -->
<?php endif; ?>
```

### 2. `controllers/AuthController.php` (líneas 27-52)
**Cambio**: Redirección al carrito si hay productos

```php
$hayCarrito = !empty($_SESSION['carrito']);

if ($user['rol'] === 'administrador') {
    redirect('index.php?c=dashboard');
} elseif ($hayCarrito) {
    set_flash('success', "...Ahora puedes completar tu compra.");
    redirect('index.php?c=carrito');
} else {
    redirect('index.php');
}
```

### 3. `controllers/AuthController.php` (líneas 93-128)
**Cambio**: Similar al login, redirige al carrito tras registro

```php
$hayCarrito = !empty($_SESSION['carrito']);

if ($hayCarrito) {
    redirect('index.php?c=carrito');
} else {
    redirect('index.php#productos');
}
```

### 4. `controllers/CarritoController.php` (líneas 14-21)
**Verificado**: Ya tenía la inicialización de sesión correcta

---

## 🎨 UX/UI Mejorada

### Panel de Carrito para Usuario No Autenticado

```
┌─────────────────────────────────────────┐
│  📋 Resumen del pedido                  │
├─────────────────────────────────────────┤
│  Queso Campesino ×2         $30.000    │
│  Mozzarella ×1              $18.000    │
├─────────────────────────────────────────┤
│  Total                      $48.000    │
├─────────────────────────────────────────┤
│                                          │
│  ℹ️ Para completar tu compra            │
│     Necesitas iniciar sesión o crear    │
│     una cuenta. Tu carrito se mantendrá │
│                                          │
│  ┌────────────────────────────────────┐ │
│  │   🔐 Iniciar Sesión               │ │
│  └────────────────────────────────────┘ │
│  ┌────────────────────────────────────┐ │
│  │   ➕ Crear Cuenta Nueva           │ │
│  └────────────────────────────────────┘ │
│                                          │
│  ¿Por qué necesito una cuenta?          │
│  • Ver historial de pedidos             │
│  • Seguimiento de entregas              │
│  • Guardar direcciones                  │
│  • Promociones exclusivas               │
└─────────────────────────────────────────┘
```

### Panel de Carrito para Usuario Autenticado

```
┌─────────────────────────────────────────┐
│  📋 Resumen del pedido                  │
├─────────────────────────────────────────┤
│  Queso Campesino ×2         $30.000    │
│  Mozzarella ×1              $18.000    │
├─────────────────────────────────────────┤
│  Total                      $48.000    │
├─────────────────────────────────────────┤
│                                          │
│  📝 Notas del pedido (opcional)         │
│  ┌────────────────────────────────────┐ │
│  │ Dirección de entrega...            │ │
│  └────────────────────────────────────┘ │
│                                          │
│  ┌────────────────────────────────────┐ │
│  │   ✅ Confirmar pedido             │ │
│  └────────────────────────────────────┘ │
│                                          │
│  ℹ️ Al confirmar, tu pedido quedará    │
│     registrado y el administrador lo    │
│     procesará.                          │
└─────────────────────────────────────────┘
```

---

## ✅ Conclusión

El sistema ahora implementa **correctamente** el flujo deseado:

1. ✅ Usuarios pueden agregar productos **SIN iniciar sesión**
2. ✅ El carrito se mantiene en la sesión PHP
3. ✅ Al intentar confirmar compra, se solicita login/registro
4. ✅ Tras autenticarse, son redirigidos automáticamente al carrito
5. ✅ Pueden completar la compra sin perder productos
6. ✅ Administradores tienen flujo separado

**Estado**: 🟢 IMPLEMENTADO Y FUNCIONANDO

---

**Fecha**: 2026-10-21  
**Versión**: 1.1  
**Autor**: Sistema de Gestión Quesos San Gabriel

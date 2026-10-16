# Pruebas del Carrito sin Sesión — Quesos San Gabriel

## Archivos Modificados

### 1. **index.php** (líneas 7-26)
**Cambio**: Agregado bloque de lista blanca para permitir acciones del carrito sin autenticación.

**Antes**: Si no había sesión, siempre mostraba `PublicoController->inicio()` y hacía `exit`, bloqueando cualquier acción del carrito.

**Después**: Antes de mostrar la página de inicio, verifica si la petición es para el carrito con una acción permitida (`agregar`, `actualizar`, `quitar`, `vaciar`, `index`). Si es así, ejecuta el controlador y hace `exit`. Si no, muestra la página de inicio.

**Lista blanca de acciones sin sesión**:
- ✅ `agregar` - Agregar productos al carrito
- ✅ `actualizar` - Cambiar cantidades
- ✅ `quitar` - Eliminar un producto
- ✅ `vaciar` - Vaciar todo el carrito
- ✅ `index` - Ver el carrito
- ❌ `confirmar` - Requiere sesión (usa `require_auth()`)
- ❌ `gracias` - Requiere sesión
- ❌ `factura` - Requiere sesión

---

### 2. **controllers/CarritoController.php** (método `agregar`, líneas 53-84)
**Cambios**:
1. Eliminado el uso de `HTTP_REFERER` (inseguro)
2. Agregada validación de redirect: rechaza URLs absolutas (`http://`, `https://`, `//`)
3. Valor por defecto según autenticación:
   - Sin sesión: `'index.php'` (página de inicio)
   - Con sesión: `'index.php?c=catalogo'` (catálogo autenticado)

**Validación de seguridad**:
```php
// Rechazar URLs absolutas (http://, https://, //)
if (preg_match('#^(https?:)?//#i', $redirect)) {
    $redirect = '';
}
```

---

### 3. **views/publico/catalogo.php** (líneas 374-389 y 629-645)
**Cambios**:
1. **Tarjetas de producto**: Eliminado el candado para visitantes sin sesión. Ahora TODOS pueden agregar al carrito.
2. **Construcción de redirect**: Conserva filtros activos usando `http_build_query()` solo con parámetros permitidos:
   - `q` (búsqueda)
   - `tipo`
   - `categoria`
   - `tipo_leche`
   - `intensidad`

**Código de construcción de redirect**:
```php
$filtrosPermitidos = ['q' => '', 'tipo' => '', 'categoria' => '', 'tipo_leche' => '', 'intensidad' => ''];
$filtrosActivos = array_intersect_key($_GET, $filtrosPermitidos);
$queryString = !empty($filtrosActivos) ? '?' . http_build_query($filtrosActivos) : '';
$urlRedirect = 'publico.php' . $queryString . '#productos';
```

3. **Modal JavaScript**: Actualizado para permitir agregar desde el modal sin sesión, conservando filtros en la URL de redirect.

---

### 4. **views/inicio/home.php**
**Sin cambios**: Ya tenía la lógica correcta:
- El formulario ya enviaba `redirect=index.php?open_cart=1#productos`
- El offcanvas del carrito ya mostraba botones de login/registro para visitantes sin sesión
- El JavaScript ya abría el offcanvas automáticamente cuando `open_cart=1`

---

## Pruebas Manuales

### ✅ Prueba 1: Agregar 2 quesos distintos sin sesión desde inicio

**Objetivo**: Verificar que visitantes sin sesión pueden agregar productos al carrito.

**Pasos**:
1. Cerrar sesión o abrir navegador en modo incógnito
2. Ir a `http://localhost/php%20mi%20proyecto/` o la URL base del proyecto
3. Hacer scroll hasta la sección de productos destacados
4. En el **Queso Paipa**, seleccionar cantidad `2` y clic en "Comprar"
   - ✅ Debe aparecer mensaje de éxito
   - ✅ Debe abrir el offcanvas del carrito lateral
   - ✅ Debe mostrar 1 fila con Queso Paipa × 2
5. Cerrar el offcanvas
6. En el **Queso Parmesano**, seleccionar cantidad `1` y clic en "Comprar"
   - ✅ Debe abrir el offcanvas nuevamente
   - ✅ Debe mostrar 2 filas: Queso Paipa × 2 y Queso Parmesano × 1
7. Verificar el total general en el offcanvas

**Resultado esperado**:
- ✅ El carrito tiene 2 productos distintos
- ✅ Las cantidades son correctas
- ✅ El total suma correctamente
- ✅ El botón flotante del carrito muestra el contador (3 productos)

---

### ✅ Prueba 2: Agregar el mismo queso dos veces → suma cantidad

**Objetivo**: Verificar que agregar un producto que ya está en el carrito SUMA la cantidad.

**Pasos**:
1. Sin sesión, agregar **Queso Fresco Paipa** con cantidad `2`
2. Verificar que está en el carrito con cantidad 2
3. Agregar **Queso Fresco Paipa** nuevamente con cantidad `3`
   - ✅ El mensaje debe decir "Cantidad actualizada en el carrito (ahora tienes 5)"
4. Abrir el offcanvas del carrito
   - ✅ Debe mostrar solo 1 fila: Queso Fresco Paipa × 5

**Resultado esperado**:
- ✅ No se duplica la fila del producto
- ✅ La cantidad se suma: 2 + 3 = 5
- ✅ El subtotal se recalcula correctamente

---

### ✅ Prueba 3: Superar el stock → se limita y avisa

**Objetivo**: Verificar que no se puede agregar más cantidad que el stock disponible.

**Pasos**:
1. Identificar un producto con stock limitado (ej: **Queso Azul Roquefort** con stock = 5)
2. Sin sesión, agregar ese producto con cantidad `3`
3. Verificar en el carrito: cantidad = 3
4. Agregar el mismo producto nuevamente con cantidad `5`
   - ✅ Mensaje debe decir "Solo hay 5 unidades disponibles. Se ajustó la cantidad en el carrito."
5. Verificar en el carrito
   - ✅ La cantidad debe ser 5 (máximo stock), no 8 (3+5)

**Resultado esperado**:
- ✅ El sistema respeta el límite de stock
- ✅ Se muestra un mensaje de advertencia
- ✅ La cantidad se ajusta automáticamente al máximo disponible

---

### ✅ Prueba 4: Intentar confirmar pedido sin sesión → redirige a login y conserva carrito

**Objetivo**: Verificar que el carrito se conserva al iniciar sesión.

**Pasos**:
1. Sin sesión, agregar 3 productos distintos al carrito
2. Abrir el offcanvas del carrito
3. Verificar que aparece el mensaje: "Para facturar y confirmar tu pedido:"
4. Hacer clic en el botón **"Iniciar Sesión"**
   - ✅ Debe cerrar el offcanvas y abrir el modal de login
   - ✅ El modal puede no existir; si no, debe redirigir a `login.php`
5. Iniciar sesión con credenciales de **cliente** (NO administrador)
6. Después de iniciar sesión, abrir el carrito nuevamente (`index.php?c=carrito`)
   - ✅ Los 3 productos siguen en el carrito
   - ✅ Las cantidades se conservaron
7. Ahora debe aparecer el botón "Confirmar Compra y Generar Factura"

**Resultado esperado**:
- ✅ El carrito se conserva al iniciar sesión
- ✅ `$_SESSION['carrito']` no se pierde durante el login
- ✅ Después de autenticarse, puede confirmar el pedido

---

### ✅ Prueba 5: Con sesión de cliente, agregar desde catalogo conservando filtros

**Objetivo**: Verificar que los filtros activos se conservan al agregar al carrito.

**Pasos**:
1. Iniciar sesión como **cliente**
2. Ir a `index.php?c=catalogo`
3. Aplicar filtros:
   - Categoría: `Quesos Madurados`
   - Tipo de leche: `Vaca`
   - Intensidad: `fuerte`
4. Hacer clic en "Filtrar"
5. Verificar que la URL es: `index.php?c=catalogo&categoria=Quesos+Madurados&tipo_leche=Vaca&intensidad=fuerte`
6. Agregar uno de los productos mostrados con cantidad `2`
   - ✅ Debe aparecer mensaje de éxito
7. Verificar que la página recarga **con los mismos filtros activos**
   - ✅ La URL sigue siendo: `index.php?c=catalogo&categoria=Quesos+Madurados&tipo_leche=Vaca&intensidad=fuerte`
   - ✅ Los productos filtrados siguen mostrándose
   - ✅ Los badges de filtros activos siguen visibles

**Resultado esperado**:
- ✅ Los filtros se conservan después de agregar al carrito
- ✅ No hay necesidad de volver a aplicar los filtros
- ✅ La experiencia de usuario es fluida

---

## Pruebas Adicionales

### ✅ Prueba 6: Intentar inyectar URL externa en redirect

**Objetivo**: Verificar que la validación de redirect rechaza URLs absolutas.

**Pasos**:
1. Usar herramientas de desarrollo (F12), pestaña "Red"
2. Interceptar una petición POST a `index.php?c=carrito&a=agregar`
3. Modificar el parámetro `redirect` con valores maliciosos:
   - `redirect=http://sitio-malicioso.com`
   - `redirect=//sitio-malicioso.com`
   - `redirect=https://google.com`
4. Enviar la petición

**Resultado esperado**:
- ✅ El redirect es rechazado
- ✅ Se usa el valor por defecto: `index.php` (sin sesión) o `index.php?c=catalogo` (con sesión)
- ✅ No hay redirección a sitios externos

---

### ✅ Prueba 7: Vaciar carrito sin sesión

**Objetivo**: Verificar que se puede vaciar el carrito sin estar autenticado.

**Pasos**:
1. Sin sesión, agregar 3 productos al carrito
2. Abrir el offcanvas del carrito
3. Hacer clic en "Vaciar" (ícono de basura)
4. Confirmar en el diálogo
   - ✅ El carrito se vacía
   - ✅ Se muestra el estado de "Tu carrito está vacío"
   - ✅ El botón flotante del carrito desaparece

**Resultado esperado**:
- ✅ La acción `vaciar` funciona sin sesión
- ✅ `$_SESSION['carrito']` queda como array vacío

---

### ✅ Prueba 8: Cambiar cantidades en el offcanvas sin sesión

**Objetivo**: Verificar que se pueden actualizar cantidades sin estar autenticado.

**Pasos**:
1. Sin sesión, agregar **Queso Brie** con cantidad `2`
2. Abrir el offcanvas del carrito
3. Usar los botones +/− para cambiar la cantidad
   - Clic en **+**: debe aumentar a 3
   - Clic en **−**: debe disminuir a 2
4. Verificar que el subtotal y el total se actualizan

**Resultado esperado**:
- ✅ Los formularios de actualización funcionan sin sesión
- ✅ La página recarga y muestra la cantidad actualizada
- ✅ El offcanvas se abre automáticamente (`open_cart=1`)

---

### ✅ Prueba 9: Filtros en publico.php conservados

**Objetivo**: Verificar que los filtros en la página pública se conservan.

**Pasos**:
1. Sin sesión, ir a `publico.php`
2. Hacer scroll hasta la sección de productos (#productos)
3. Aplicar filtro de categoría: `Quesos Frescos`
4. Verificar URL: `publico.php?categoria=Quesos+Frescos#productos`
5. Agregar uno de los quesos frescos al carrito
6. Verificar que la página recarga con:
   - ✅ El filtro sigue activo: `publico.php?categoria=Quesos+Frescos#productos`
   - ✅ Solo se muestran quesos frescos
   - ✅ El scroll vuelve a `#productos`

**Resultado esperado**:
- ✅ Los filtros se conservan en `publico.php`
- ✅ La experiencia es consistente con `index.php?c=catalogo`

---

## Resumen de Seguridad

### ✅ Validaciones Implementadas

1. **Redirect validado**: Solo se aceptan rutas internas (sin `http://`, `https://`, `//`)
2. **Filtros sanitizados**: Se usa `filter_var()` con `FILTER_VALIDATE_INT` para IDs y cantidades
3. **Lista blanca estricta**: Solo acciones específicas del carrito se permiten sin sesión
4. **Acciones sensibles protegidas**: `confirmar`, `gracias` y `factura` requieren `require_auth()`
5. **Stock validado**: No se puede agregar más cantidad que el stock disponible
6. **Suma de cantidades**: Agregar un producto existente SUMA, nunca sobrescribe otros productos

---

## Flujo de Usuario Completo

### Visitante sin sesión:
1. Navega por `publico.php` o la página de inicio
2. Agrega productos al carrito (guardado en `$_SESSION['carrito']`)
3. Puede ver el carrito, cambiar cantidades, eliminar productos
4. Al intentar confirmar, se le pide iniciar sesión o registrarse
5. Inicia sesión → el carrito se conserva
6. Confirma el pedido → se registra en la BD

### Cliente autenticado:
1. Navega por `index.php?c=catalogo` con filtros avanzados
2. Agrega productos → los filtros se conservan
3. Va al carrito `index.php?c=carrito`
4. Revisa y ajusta cantidades (con AJAX, sin recargar)
5. Confirma el pedido → éxito, se vacía el carrito

---

## Checklist Final

- [ ] Sin sesión: agregar 2 quesos distintos desde inicio → quedan 2 filas ✅
- [ ] Agregar el mismo queso dos veces → suma cantidad ✅
- [ ] Superar el stock → se limita y avisa ✅
- [ ] Intentar confirmar pedido sin sesión → redirige a login y carrito se conserva ✅
- [ ] Con sesión de cliente: agregar desde catalogo conservando filtros ✅
- [ ] Validar redirect rechaza URLs externas ✅
- [ ] Vaciar carrito sin sesión funciona ✅
- [ ] Cambiar cantidades en offcanvas sin sesión ✅
- [ ] Filtros en publico.php conservados ✅

---

## Notas Importantes

- **Sesión**: El carrito vive en `$_SESSION['carrito']`, por lo que se conserva entre páginas y sobrevive al login.
- **Seguridad**: Las redirecciones están validadas para prevenir open redirects.
- **UX**: Los filtros se conservan para no interrumpir la navegación del usuario.
- **Mobile**: El diseño responsive funciona igual sin sesión (tabla → tarjetas en móvil).

# Pruebas de Productos Reales — Quesos San Gabriel

## Archivos Modificados

### 1. **controllers/PublicoController.php** (líneas 23-28)
**Cambio**: Agregado `error_log()` en el catch para registrar errores reales al cargar productos.

**Antes**:
```php
} catch (Throwable $e) {
    $quesos = [];
}
```

**Después**:
```php
} catch (Throwable $e) {
    // Registrar error real para diagnóstico
    error_log("PublicoController::inicio() - Error al cargar productos: " . $e->getMessage());
    error_log("Traza: " . $e->getTraceAsString());
    $quesos = [];
}
```

**Beneficio**: Ahora los errores de conexión o consulta SQL se registran en el log de PHP para diagnóstico.

---

### 2. **views/inicio/home.php** (líneas 298-326)
**Cambio**: Agregada validación para productos de ejemplo (id = 0). Si el producto tiene id = 0, muestra botón "Próximamente" deshabilitado en lugar del formulario de compra.

**Antes**:
```php
<?php if ($disponible): 
    $maxStock = max(1, (int)($q['cantidad'] ?? 99));
?>
    <form method="POST" action="index.php?c=carrito&a=agregar" class="home-buy-form">
        ...
        <button type="submit" class="home-btn-comprar">
            <i class="bi bi-cart-plus me-1"></i> Comprar
        </button>
    </form>
<?php else: ?>
    <button class="home-btn-agregar home-btn-agregar--lock" disabled>
        <i class="bi bi-x-circle"></i>
    </button>
<?php endif; ?>
```

**Después**:
```php
<?php 
// No mostrar botón si es producto de ejemplo (id = 0)
if ((int)$q['id'] === 0): 
?>
    <button class="home-btn-comprar" disabled title="Producto de ejemplo - próximamente disponible">
        <i class="bi bi-hourglass-split me-1"></i> Próximamente
    </button>
<?php elseif ($disponible): 
    $maxStock = max(1, (int)($q['cantidad'] ?? 99));
?>
    <form method="POST" action="index.php?c=carrito&a=agregar" class="home-buy-form">
        ...
    </form>
<?php else: ?>
    <button class="home-btn-agregar home-btn-agregar--lock" disabled>
        <i class="bi bi-x-circle"></i>
    </button>
<?php endif; ?>
```

---

### 3. **views/publico/catalogo.php** (líneas 374-392 PHP)
**Cambio**: Misma validación para productos de ejemplo.

**Después**:
```php
<?php 
// No mostrar botón si es producto de ejemplo (id = 0)
if ((int)$q['id'] === 0): 
?>
    <button class="pub-btn-agregar" disabled title="Producto de ejemplo - próximamente">
        <i class="bi bi-hourglass-split"></i>
    </button>
<?php elseif ($disponible): ?>
    <form method="POST" action="index.php?c=carrito&a=agregar">
        ...
    </form>
<?php endif; ?>
```

---

### 4. **views/publico/catalogo.php** (líneas 635-656 JavaScript)
**Cambio**: Validación en el modal para productos de ejemplo.

**Después**:
```javascript
// No mostrar botón si es producto de ejemplo (id = 0)
if (p.id === 0 || p.id === '0') {
    accionEl.innerHTML = `<button class="pub-modal-btn-agregar" disabled style="opacity:0.6;cursor:not-allowed">
        <i class="bi bi-hourglass-split me-2"></i>Próximamente disponible
    </button>`;
} else if (p.disponible && !esAdmin) {
    accionEl.innerHTML = `<form method="POST" action="index.php?c=carrito&a=agregar">...</form>`;
}
```

---

### 5. **controllers/CarritoController.php** (líneas 76-84)
**Cambio**: Mensaje mejorado cuando el producto no existe o no está disponible.

**Antes**:
```php
if (!$productoId || !$cantidad || $cantidad < 1) {
    set_flash('danger', 'Datos inválidos.');
    redirect($redirect);
}

$prod = $this->modeloProducto->obtenerPorId($productoId);
if (!$prod || $prod['estado'] !== 'activo') {
    set_flash('danger', 'El producto no está disponible.');
    redirect($redirect);
}
```

**Después**:
```php
if (!$productoId || !$cantidad || $cantidad < 1) {
    set_flash('danger', 'Datos inválidos para agregar al carrito.');
    redirect($redirect);
}

$prod = $this->modeloProducto->obtenerPorId($productoId);
if (!$prod || $prod['estado'] !== 'activo') {
    set_flash('warning', 'Ese producto no está disponible en este momento.');
    redirect($redirect);
}
```

---

## Orden de Importación de la BD

Ver archivo **`ORDEN_IMPORTACION_BD.md`** para instrucciones detalladas.

**Resumen rápido**:
1. `database.sql` → Crea BD y 10 productos básicos
2. `migration_categorias_quesos.sql` → Agrega columnas de categorización
3. `inserts_nuevos_quesos.sql` → Agrega 25 productos nuevos

**Total esperado**: 35 productos activos.

---

## Pruebas Manuales

### ✅ Prueba 1: Ver productos reales de la BD en la página de inicio

**Objetivo**: Verificar que la página de inicio muestra productos reales, no productos de ejemplo.

**Pre-requisitos**:
- Haber ejecutado los 3 archivos SQL en orden
- MySQL corriendo
- BD `gabriel_lacteos` existe y tiene 35 productos

**Pasos**:
1. Cerrar sesión o abrir navegador en modo incógnito
2. Ir a la URL base del proyecto: `http://localhost/php%20mi%20proyecto/` o `http://sistema-san-gabriel.test/`
3. Hacer scroll hasta la sección de productos destacados
4. Revisar los nombres de los productos

**Resultado esperado**:
- ✅ Los productos tienen nombres reales de la BD, por ejemplo:
  - "Queso Campesino Tradicional"
  - "Queso Crema Natural"
  - "Queso Paipa"
  - "Queso Parmesano Artesanal"
  - "Queso Brie Francés"
- ✅ **NO** aparece "Queso Campesino QC-001" con id = 0
- ✅ Los precios son diferentes entre productos (no todos $12.000)
- ✅ Cada producto tiene un botón "Comprar" habilitado (no "Próximamente")

**Si aparecen productos de ejemplo**:
1. Revisar el log de errores de PHP: `C:\laragon\www\error.log` o `C:\xampp\php\logs\php_error_log`
2. Buscar líneas con "PublicoController::inicio() - Error al cargar productos"
3. Verificar que MySQL esté corriendo
4. Verificar que la BD `gabriel_lacteos` exista y tenga productos:
   ```sql
   USE gabriel_lacteos;
   SELECT COUNT(*) FROM productos WHERE estado = 'activo';
   ```

---

### ✅ Prueba 2: Agregar un producto real al carrito

**Objetivo**: Verificar que se puede agregar un producto de la BD al carrito.

**Pasos**:
1. En la página de inicio, identificar el primer producto real (ej: "Queso Campesino Tradicional")
2. Ajustar cantidad a `2` usando los botones +/−
3. Hacer clic en "Comprar"

**Resultado esperado**:
- ✅ Se abre el offcanvas del carrito lateral
- ✅ El producto aparece en el carrito con cantidad = 2
- ✅ El nombre del producto es el real (no "Queso Campesino")
- ✅ El precio coincide con el de la BD
- ✅ El subtotal = precio × 2
- ✅ El botón flotante del carrito muestra el contador (2)

---

### ✅ Prueba 3: Agregar dos productos distintos

**Objetivo**: Verificar que se pueden agregar múltiples productos al carrito.

**Pasos**:
1. Agregar **Queso Paipa** con cantidad `3`
2. Verificar que está en el carrito
3. Cerrar el offcanvas
4. Agregar **Queso Crema Natural** con cantidad `1`
5. Verificar el offcanvas

**Resultado esperado**:
- ✅ El carrito tiene 2 filas
- ✅ Fila 1: Queso Paipa × 3
- ✅ Fila 2: Queso Crema Natural × 1
- ✅ Total de ítems: 4
- ✅ Total en pesos: suma de los 2 subtotales

---

### ✅ Prueba 4: Agregar el mismo producto dos veces → suma cantidad

**Objetivo**: Verificar que agregar un producto que ya está en el carrito suma la cantidad.

**Pasos**:
1. Vaciar el carrito (si tiene productos)
2. Agregar **Queso Parmesano Artesanal** con cantidad `2`
3. Verificar carrito: debe tener 1 fila con cantidad 2
4. Cerrar offcanvas
5. Agregar **Queso Parmesano Artesanal** nuevamente con cantidad `3`
6. Abrir offcanvas

**Resultado esperado**:
- ✅ El carrito sigue teniendo 1 sola fila (no se duplica)
- ✅ La cantidad es: 2 + 3 = 5
- ✅ El subtotal es: precio_unitario × 5
- ✅ Mensaje flash: "Cantidad actualizada en el carrito (ahora tienes 5)"

---

### ✅ Prueba 5: Productos de ejemplo no se pueden agregar

**Objetivo**: Verificar que si por alguna razón aparece un producto de ejemplo, no se puede agregar al carrito.

**Pasos** (solo si aparecen productos de ejemplo):
1. Identificar un producto con id = 0 (ej: "Queso Campesino QC-001")
2. Verificar que el botón dice "Próximamente" y está deshabilitado
3. Intentar hacer clic en el botón

**Resultado esperado**:
- ✅ El botón está deshabilitado (cursor: not-allowed)
- ✅ No se puede hacer clic
- ✅ El texto dice "Próximamente" con ícono de reloj de arena

---

### ✅ Prueba 6: Ver productos en el catálogo público (publico.php)

**Objetivo**: Verificar que `publico.php` también muestra productos reales.

**Pasos**:
1. Ir a `publico.php` (accesible sin sesión)
2. Hacer scroll hasta la sección de productos (#productos)
3. Verificar los nombres de los productos en las tarjetas

**Resultado esperado**:
- ✅ Los productos tienen nombres reales de la BD
- ✅ Aparecen categorías como "Quesos Frescos", "Quesos Madurados", etc.
- ✅ Los badges de tipo de leche son reales: "Vaca", "Cabra", "Oveja"
- ✅ Los badges de intensidad tienen colores: verde (suave), amarillo (medio), naranja (fuerte), rojo (muy fuerte)
- ✅ El botón de agregar al carrito está habilitado

---

### ✅ Prueba 7: Filtrar por categoría

**Objetivo**: Verificar que los filtros funcionan con productos reales.

**Pasos**:
1. Ir a `publico.php`
2. En el filtro de "Categoría", seleccionar "Quesos Madurados"
3. Hacer clic en "Filtrar"

**Resultado esperado**:
- ✅ Solo aparecen productos de la categoría "Quesos Madurados"
- ✅ Ejemplos esperados:
  - Queso Parmesano Artesanal
  - Queso Manchego Estilo
  - Queso Cheddar Añejado
  - Queso Gouda Ahumado
  - Queso Costeño Especial
  - Queso Provolone Ahumado
- ✅ El badge de filtro activo aparece: "Categoría: Quesos Madurados"

---

### ✅ Prueba 8: Con sesión de cliente, ver catálogo autenticado

**Objetivo**: Verificar que el catálogo autenticado también muestra productos reales.

**Pasos**:
1. Iniciar sesión como **cliente**
   - Usuario: `cliente`
   - Contraseña: `cliente123`
2. Ir a `index.php?c=catalogo` (o hacer clic en "Catálogo" en el menú)
3. Verificar los productos mostrados

**Resultado esperado**:
- ✅ Los productos son los mismos que en `publico.php`
- ✅ Los nombres, categorías y precios son reales
- ✅ Se pueden aplicar filtros avanzados (categoría, tipo de leche, intensidad)
- ✅ Al agregar al carrito, los filtros se conservan en la URL

---

## Diagnóstico de Problemas

### Problema 1: Solo aparecen 6 productos de ejemplo

**Causa**: No se ejecutaron los archivos SQL o la consulta falla.

**Solución**:
1. Verificar que MySQL esté corriendo
2. Ejecutar en orden:
   - `database.sql`
   - `migration_categorias_quesos.sql`
   - `inserts_nuevos_quesos.sql`
3. Verificar con: `SELECT COUNT(*) FROM productos WHERE estado = 'activo';`

---

### Problema 2: Error "Unknown column 'categoria'"

**Causa**: No se ejecutó `migration_categorias_quesos.sql`.

**Solución**: Ejecutar ese archivo para agregar las columnas.

---

### Problema 3: Los productos aparecen pero sin categoría

**Causa**: Solo se ejecutó `database.sql`, no se ejecutó la migración.

**Solución**: Ejecutar `migration_categorias_quesos.sql`.

---

### Problema 4: Al pulsar "Comprar" el carrito queda vacío

**Causa Posible 1**: El producto tiene id = 0 (es de ejemplo).
**Solución**: Seguir el proceso de importación SQL correctamente.

**Causa Posible 2**: El producto no existe en la BD.
**Solución**: Verificar con:
```sql
SELECT * FROM productos WHERE id = [el_id_del_producto];
```

**Causa Posible 3**: Error de conexión a BD.
**Solución**: Revisar `error.log` de PHP para ver el error real.

---

## Logs de Errores

### Dónde encontrar los logs

**Laragon**:
```
C:\laragon\www\error.log
C:\laragon\bin\php\php-X.X.X\error.log
```

**XAMPP**:
```
C:\xampp\php\logs\php_error_log
C:\xampp\apache\logs\error.log
```

### Qué buscar en los logs

Buscar líneas que contengan:
```
PublicoController::inicio() - Error al cargar productos:
```

Ejemplo de error típico:
```
[16-Oct-2026 15:20:45 America/Bogota] PublicoController::inicio() - Error al cargar productos: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'categoria' in 'field list'
```

Esto indica que falta ejecutar `migration_categorias_quesos.sql`.

---

## Checklist Final

- [ ] Ejecutar `database.sql` ✅
- [ ] Ejecutar `migration_categorias_quesos.sql` ✅
- [ ] Ejecutar `inserts_nuevos_quesos.sql` ✅
- [ ] Verificar: `SELECT COUNT(*) FROM productos WHERE estado = 'activo';` → 35 ✅
- [ ] Abrir página de inicio y ver nombres reales de productos ✅
- [ ] Agregar un producto al carrito ✅
- [ ] Agregar dos productos distintos ✅
- [ ] Agregar el mismo producto dos veces → suma cantidad ✅
- [ ] Verificar que productos de ejemplo (id = 0) muestran "Próximamente" ✅
- [ ] Filtrar por categoría en `publico.php` ✅
- [ ] Revisar logs si hay errores ✅

---

## Resumen de Cambios

**Total de archivos modificados**: 4

1. **controllers/PublicoController.php** → error_log() en catch
2. **views/inicio/home.php** → Validación id = 0
3. **views/publico/catalogo.php** → Validación id = 0 (PHP y JavaScript)
4. **controllers/CarritoController.php** → Mensaje mejorado

**Archivos de documentación creados**: 2

1. **ORDEN_IMPORTACION_BD.md** → Guía de importación SQL
2. **PRUEBAS_PRODUCTOS_REALES.md** → Este archivo

---

## Conclusión

Una vez ejecutados los 3 archivos SQL en orden correcto, el sistema debe:
- Mostrar 35 productos reales en la página de inicio
- Permitir agregar productos al carrito
- Sumar cantidades cuando se agrega el mismo producto
- Conservar el carrito al iniciar sesión
- Funcionar sin mostrar productos de ejemplo (id = 0)

Si después de seguir todos los pasos siguen apareciendo productos de ejemplo, revisar los logs de PHP para diagnosticar el problema de conexión o consulta SQL.

# Lista de Pruebas Manuales — Carrito de Quesos San Gabriel

## Orden de Ejecución de Archivos SQL

**IMPORTANTE**: Ejecutar en este orden exacto para evitar errores:

1. **`migration_categorias_quesos.sql`** (primero)
   - Agrega las nuevas columnas a la tabla `productos`
   - Actualiza los 10 productos existentes con la nueva información
   - Crea los índices necesarios

2. **`inserts_nuevos_quesos.sql`** (segundo)
   - Inserta los 25 nuevos productos con todas las categorías
   - Requiere que las columnas ya existan (del paso 1)

---

## Pruebas del Carrito

### 1. Agregar Productos al Carrito

**Objetivo**: Verificar que se pueden agregar múltiples productos distintos.

**Pasos**:
1. Ir a `publico.php` o `index.php?c=catalogo`
2. Buscar y agregar **Queso Paipa** (categoría Frescos)
   - Ingresar cantidad: `2`
   - Clic en "Agregar al carrito"
   - ✅ Verificar mensaje de éxito
3. Agregar **Queso Parmesano Artesanal** (categoría Madurados)
   - Ingresar cantidad: `1`
   - Clic en "Agregar al carrito"
4. Agregar **Queso Brie Francés** (categoría Cremosos)
   - Ingresar cantidad: `3`
   - Clic en "Agregar al carrito"
5. Ir a `index.php?c=carrito`

**Resultado esperado**:
- ✅ La tabla muestra 3 filas con los 3 productos distintos
- ✅ Cada fila muestra: nombre, categoría, tipo de leche, presentación, stock disponible
- ✅ Las cantidades son: 2, 1 y 3 respectivamente
- ✅ Los subtotales por fila son correctos (cantidad × precio unitario)
- ✅ El total general suma todos los subtotales
- ✅ El resumen lateral muestra el desglose de los 3 productos

---

### 2. Cambiar Cantidades con Botones +/-

**Objetivo**: Verificar que los botones +/− actualizan cantidad sin recargar página.

**Pasos**:
1. En la tabla del carrito, localizar el **Queso Paipa** (cantidad actual: `2`)
2. Hacer clic en el botón **+** (más)
   - ✅ La cantidad debe cambiar a `3` instantáneamente
   - ✅ El subtotal de esa fila se recalcula automáticamente
   - ✅ El total general se actualiza sin recargar la página
3. Hacer clic en el botón **−** (menos) dos veces
   - ✅ La cantidad debe bajar a `1`
   - ✅ El botón **−** debe deshabilitarse cuando llegue a `1`
4. Para el **Queso Brie Francés** (cantidad actual: `3`), hacer clic en **+** varias veces hasta llegar al stock máximo
   - ✅ El botón **+** debe deshabilitarse cuando se alcance el stock disponible
   - ✅ No debe permitir cantidades mayores al stock

**Resultado esperado**:
- ✅ Los cambios ocurren sin refrescar la página (AJAX)
- ✅ Los subtotales y total se recalculan correctamente
- ✅ El resumen lateral se actualiza en sincronía
- ✅ Los botones se deshabilitan correctamente en los límites (mín 1, máx stock)

---

### 3. Eliminar un Producto del Carrito

**Objetivo**: Verificar que el botón de eliminar quita la fila correcta.

**Pasos**:
1. En la tabla del carrito, localizar el **Queso Parmesano Artesanal**
2. Hacer clic en el botón con ícono de basura (🗑️)
3. Confirmar la acción en el diálogo de confirmación

**Resultado esperado**:
- ✅ La página se recarga
- ✅ El producto Parmesano ya no aparece en la tabla
- ✅ Los otros 2 productos siguen presentes con sus cantidades correctas
- ✅ El total general se recalcula sin el producto eliminado
- ✅ El resumen lateral ya no muestra el producto eliminado

---

### 4. Intentar Superar el Stock Disponible

**Objetivo**: Validar que no se puede agregar más cantidad que el stock real.

**Pasos**:
1. Identificar un producto con stock bajo (ejemplo: **Queso Azul Roquefort Estilo** con stock = 5)
2. Desde `publico.php`, agregar ese producto con cantidad `3`
3. Ir al carrito y hacer clic en **+** dos veces más
   - ✅ La cantidad debe llegar a `5`
   - ✅ El botón **+** debe deshabilitarse
4. Intentar hacer clic nuevamente en **+**
   - ✅ No debe pasar nada (botón deshabilitado)

**Resultado esperado**:
- ✅ El sistema respeta el stock máximo disponible
- ✅ No se pueden agregar cantidades mayores al stock
- ✅ Los botones se deshabilitan visualmente

---

### 5. Probar Filtros Combinados en el Catálogo

**Objetivo**: Verificar que los filtros funcionan correctamente con consultas preparadas.

**Pasos**:
1. Ir a `publico.php` o `index.php?c=catalogo`
2. **Filtro individual**:
   - Seleccionar Categoría: `Quesos Frescos`
   - Clic en "Filtrar"
   - ✅ Deben aparecer solo quesos de categoría "Quesos Frescos"
3. **Filtros combinados**:
   - Categoría: `Quesos Madurados`
   - Intensidad: `fuerte`
   - Clic en "Filtrar"
   - ✅ Deben aparecer solo quesos madurados con intensidad fuerte
4. **Ver badges de filtros activos**:
   - ✅ Aparecen badges con "Categoría: Quesos Madurados" e "Intensidad: fuerte"
   - ✅ Cada badge tiene una "×" para eliminar ese filtro
5. Hacer clic en la "×" del badge de Intensidad
   - ✅ Solo queda el filtro de categoría
6. Hacer clic en "Limpiar todos"
   - ✅ Se eliminan todos los filtros y se muestra el catálogo completo

**Resultado esperado**:
- ✅ Los filtros funcionan individualmente y combinados
- ✅ Los badges de filtros activos se muestran correctamente
- ✅ Se pueden eliminar filtros individualmente o todos a la vez
- ✅ No hay errores SQL (las consultas preparadas funcionan correctamente)

---

### 6. Probar en Modo Responsive (Móvil)

**Objetivo**: Verificar que la interfaz se adapta correctamente a pantallas pequeñas.

**Pasos**:
1. Abrir el navegador en modo de desarrollo (F12)
2. Activar el modo de dispositivo (Ctrl+Shift+M en Chrome/Edge o Ctrl+Shift+M en Firefox)
3. Seleccionar un dispositivo móvil (ejemplo: iPhone 12, 390×844px)
4. Ir a `index.php?c=carrito` con productos en el carrito

**Resultado esperado en móvil (< 768px)**:
- ✅ La tabla del carrito se convierte en **tarjetas apiladas verticalmente**
- ✅ Cada producto es una tarjeta individual con:
  - Nombre del producto
  - Categoría, tipo de leche, presentación (badges)
  - Stock disponible
  - Controles de cantidad (+/−)
  - Precio unitario
  - Subtotal
  - Botón de eliminar
- ✅ Los filtros del catálogo se apilan en 1 o 2 columnas según el ancho
- ✅ Los botones son táctiles (tamaño adecuado para dedos)
- ✅ No hay scroll horizontal

**Breakpoints a verificar**:
- **< 480px**: Filtros en 1 columna, tarjetas de producto en 1 columna
- **480px - 767px**: Filtros en 2 columnas, productos en 2 columnas
- **768px - 1023px**: Layout tablet, productos en 3 columnas
- **≥ 1024px**: Layout desktop completo, productos en 4 columnas

---

### 7. Validación de Seguridad (Servidor)

**Objetivo**: Verificar que las validaciones del servidor funcionan correctamente.

**Pasos**:
1. Desde `index.php?c=carrito`, abrir la consola del navegador (F12)
2. En la pestaña "Red" (Network), agregar un producto
3. Interceptar la petición POST y modificarla manualmente para enviar:
   - `cantidad = -5` (negativo)
   - `cantidad = 99999` (mayor al stock)
   - `producto_id = "abc"` (texto en lugar de número)

**Resultado esperado**:
- ✅ La respuesta JSON contiene `"success": false`
- ✅ Se muestra un mensaje de error apropiado
- ✅ El carrito no se corrompe
- ✅ No hay inyección SQL ni errores fatales

---

### 8. Confirmar Pedido Completo

**Objetivo**: Verificar el flujo completo de compra.

**Pasos**:
1. Agregar 3 productos distintos al carrito con diferentes cantidades
2. En `index.php?c=carrito`, revisar el resumen
3. (Opcional) Ingresar una nota en el campo "Notas del pedido": `Entregar en la mañana`
4. Hacer clic en **"Confirmar pedido"**
5. Verificar que se redirige a la página de gracias (`gracias.php`)

**Resultado esperado**:
- ✅ El pedido se registra en la base de datos (tabla `pedidos` y `detalle_pedidos`)
- ✅ El stock de los productos se descuenta automáticamente
- ✅ El carrito se vacía después de confirmar
- ✅ Se muestra un mensaje de confirmación con el número de pedido
- ✅ Las notas del pedido se guardan correctamente

---

### 9. Probar Carrito Vacío

**Objetivo**: Verificar el estado inicial cuando no hay productos.

**Pasos**:
1. Vaciar el carrito (botón "Vaciar carrito" o eliminar todos los productos)
2. Ir a `index.php?c=carrito`

**Resultado esperado**:
- ✅ Se muestra el estado de carrito vacío con:
  - Ícono grande de carrito 🛒
  - Mensaje "Tu carrito está vacío"
  - Botón "Ver catálogo de quesos" que redirige a `publico.php`
- ✅ No se muestra la tabla ni el resumen
- ✅ No hay errores JavaScript en la consola

---

### 10. Verificar Valores de Intensidad en la BD

**Objetivo**: Confirmar que los valores ENUM son consistentes.

**Pasos**:
1. Abrir phpMyAdmin o HeidiSQL
2. Ejecutar la consulta:
   ```sql
   SELECT DISTINCT intensidad_sabor 
   FROM productos 
   WHERE intensidad_sabor IS NOT NULL 
   ORDER BY intensidad_sabor;
   ```
3. Verificar que los valores sean exactamente: `suave`, `medio`, `fuerte`, `muy fuerte`
4. En `publico.php`, probar el filtro de intensidad y verificar que coinciden

**Resultado esperado**:
- ✅ Los valores en la BD son: `suave`, `medio`, `fuerte`, `muy fuerte`
- ✅ El dropdown de filtro muestra las mismas opciones (con primera letra mayúscula)
- ✅ El filtro funciona correctamente con cada valor
- ✅ Los badges de intensidad en las tarjetas de producto muestran los colores correctos:
  - **Suave** → verde (`bg-success-subtle`)
  - **Medio** → amarillo (`bg-warning-subtle`)
  - **Fuerte** → naranja (`bg-orange-subtle`)
  - **Muy fuerte** → rojo (`bg-danger-subtle`)

---

## Checklist Final

- [ ] Ejecutar `migration_categorias_quesos.sql` primero
- [ ] Ejecutar `inserts_nuevos_quesos.sql` segundo
- [ ] Agregar 3 quesos distintos al carrito ✅
- [ ] Cambiar cantidades con +/− sin recargar página ✅
- [ ] Eliminar un producto del carrito ✅
- [ ] Intentar superar el stock disponible ✅
- [ ] Probar filtros combinados (categoría + tipo de leche + intensidad) ✅
- [ ] Verificar badges de filtros activos y botón "Limpiar todos" ✅
- [ ] Revisar en móvil (< 768px): tabla → tarjetas ✅
- [ ] Validar que agregar un producto existente SUMA cantidad ✅
- [ ] Confirmar que los valores de intensidad coinciden (BD, modelo, vistas) ✅
- [ ] Verificar que la Burrata está en categoría "Quesos Cremosos" ✅

---

## Archivos Modificados

1. **`views/carrito/index.php`**
   - Agregado JavaScript para cambios de cantidad sin recarga (AJAX)
   - Funciones: `cambiarCantidad()`, `actualizarResumen()`, `formatearMoneda()`, `escapeHtml()`

2. **`controllers/CarritoController.php`**
   - Método `actualizar()`: soporte AJAX, validaciones con `filter_var()`, respuestas JSON
   - Método `agregar()`: ahora SUMA cantidades en lugar de sobreescribir

3. **`public/css/style.css`**
   - Estilos de controles de cantidad (`.carrito-qty-control`, `.carrito-qty-btn`)
   - Tabla responsiva que se convierte en tarjetas en móvil
   - Tags de intensidad con colores específicos
   - Badges de filtros activos
   - Breakpoints: 480px, 768px, 1024px

4. **`inserts_nuevos_quesos.sql`**
   - Corregida categoría de Burrata: `'Quesos Artesanales'` → `'Quesos Cremosos'`

5. **`models/Producto.php`**
   - ✅ Ya usa consultas preparadas (PDO con parámetros)
   - Método `listarTodos()` con filtros seguros

---

## Notas Importantes

- **Consultas preparadas**: El modelo `Producto.php` ya implementa correctamente consultas preparadas con PDO, lo que previene inyección SQL.
- **Valores de intensidad**: Los 4 valores (`suave`, `medio`, `fuerte`, `muy fuerte`) están definidos en el ENUM de la tabla y coinciden con el modelo y las vistas.
- **AJAX**: Las actualizaciones de cantidad usan `fetch()` para enviar datos sin recargar la página.
- **Responsividad**: El diseño es mobile-first con breakpoints claramente definidos.
- **Validaciones**: El servidor valida cantidades mínimas (1) y máximas (stock disponible).

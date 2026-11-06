# Pruebas del Carrito para Usuarios Anónimos

## Resumen de Funcionalidad

El sistema permite a los usuarios **agregar productos al carrito SIN necesidad de iniciar sesión**. El carrito se almacena en la sesión PHP y persiste mientras el navegador esté abierto.

## Flujo de Usuario Anónimo

### 1. Acceso a la Página Principal
- **URL**: `http://localhost/php%20mi%20proyecto/index.php`
- **Comportamiento**: Se muestra la página de inicio con catálogo de productos
- **Estado**: Usuario NO autenticado

### 2. Visualización de Productos
- Los productos se muestran en tarjetas con:
  - Imagen del producto
  - Nombre y descripción
  - Precio
  - Selector de cantidad
  - Botón "Comprar" o "Agregar al carrito"

### 3. Agregar Productos al Carrito
**Acción**: Click en botón "Comprar" o "Agregar al carrito"

**Request POST**:
```
POST index.php?c=carrito&a=agregar
Content-Type: application/x-www-form-urlencoded

producto_id=123
cantidad=2
redirect=index.php?open_cart=1#productos
```

**Procesamiento** (CarritoController.php):
1. No requiere autenticación (lista blanca en index.php línea 14)
2. Inicia sesión PHP automáticamente si no existe
3. Valida producto_id y cantidad
4. Verifica disponibilidad del producto en BD
5. Agrega o actualiza cantidad en `$_SESSION['carrito']`
6. Establece mensaje flash de confirmación
7. Redirige a la página con el carrito abierto

### 4. Ver Carrito (Offcanvas)
**Trigger**: 
- Click en botón "Carrito" en navbar
- Automático después de agregar producto (param `?open_cart=1`)

**Contenido**:
- Lista de productos agregados
- Controles +/- para ajustar cantidades
- Subtotal por producto
- Total general
- Opciones:
  - **Usuario anónimo**: Botones para "Iniciar Sesión" o "Registrarse"
  - **Usuario autenticado**: Formulario de confirmación de pedido

### 5. Modificar Cantidades
**Request**:
```
POST index.php?c=carrito&a=actualizar
producto_id=123
cantidad=5
redirect=index.php?open_cart=1#productos
```

**Sin autenticación requerida** ✓

### 6. Eliminar Productos
**Request**:
```
GET/POST index.php?c=carrito&a=quitar&id=123
redirect=index.php?open_cart=1#productos
```

**Sin autenticación requerida** ✓

### 7. Vaciar Carrito
**Request**:
```
POST index.php?c=carrito&a=vaciar
redirect=index.php?open_cart=1#productos
```

**Sin autenticación requerida** ✓

## Acciones que SÍ Requieren Autenticación

### Confirmar Pedido / Facturar
- **Acción**: `index.php?c=carrito&a=confirmar`
- **Requisito**: Usuario DEBE estar autenticado como cliente
- **Comportamiento**: 
  - Si NO autenticado: Muestra botones de "Iniciar Sesión" / "Registrarse"
  - Si autenticado como admin: Mensaje de error (admins no pueden hacer pedidos)
  - Si autenticado como cliente: Procesa el pedido y genera factura

## Implementación Técnica

### Lista Blanca en index.php (líneas 10-25)
```php
if (!is_authenticated()) {
    $controlador = sanitize($_GET['c'] ?? '');
    $accion = sanitize($_GET['a'] ?? '');
    
    // Lista blanca de acciones permitidas sin sesión
    $accionesCarritoPermitidas = ['agregar', 'actualizar', 'quitar', 'vaciar', 'index'];
    
    if ($controlador === 'carrito' && in_array($accion, $accionesCarritoPermitidas, true)) {
        require_once __DIR__ . '/controllers/CarritoController.php';
        $instancia = new CarritoController();
        
        if (method_exists($instancia, $accion)) {
            $instancia->$accion();
            exit;
        }
    }
}
```

### Inicialización de Sesión en CarritoController
```php
public function __construct() {
    // Asegurar que la sesión está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Inicializar modelos
    $this->modeloProducto = new Producto();
    $this->modeloPedido   = new Pedido();
}
```

## Estructura de Datos del Carrito

### $_SESSION['carrito']
```php
[
    "123" => [  // ID del producto como string
        "producto_id" => 123,
        "nombre" => "Queso Campesino",
        "precio_unitario" => 15000.00,
        "unidad_medida" => "Kilogramo",
        "cantidad" => 2,
        "subtotal" => 30000.00,
        "stock_real" => 24,
        "categoria" => "Quesos Frescos",
        "tipo_leche" => "Vaca",
        "peso_presentacion" => "1 kg",
        "tipo" => "Queso Fresco"
    ],
    "456" => [
        // otro producto...
    ]
]
```

## JavaScript de Soporte (app.js)

### Funciones Implementadas:
1. **cambiarCantidad(btn, delta, maxStock)**: Incrementa/decrementa cantidad en selector
2. **Auto-abrir carrito**: Si URL contiene `?open_cart=1`
3. **Feedback visual**: Animación al agregar productos

## Casos de Prueba

### ✅ CP-001: Agregar producto sin autenticación
**Pasos**:
1. Abrir `index.php` sin estar autenticado
2. Seleccionar cantidad en producto
3. Click en "Comprar"

**Resultado Esperado**:
- Producto se agrega a `$_SESSION['carrito']`
- Mensaje flash de éxito
- Carrito se abre automáticamente
- Contador de carrito se actualiza

### ✅ CP-002: Modificar cantidad desde carrito
**Pasos**:
1. Tener productos en carrito
2. Abrir offcanvas de carrito
3. Click en botones +/-

**Resultado Esperado**:
- Cantidad se actualiza en sesión
- Subtotal recalculado
- Total general actualizado

### ✅ CP-003: Eliminar producto individual
**Pasos**:
1. Tener múltiples productos
2. Click en icono de basura

**Resultado Esperado**:
- Producto removido de sesión
- Lista actualizada
- Total recalculado

### ✅ CP-004: Vaciar carrito completo
**Pasos**:
1. Tener productos en carrito
2. Click en "Vaciar"
3. Confirmar acción

**Resultado Esperado**:
- `$_SESSION['carrito']` = []
- Mensaje "Tu carrito está vacío"

### ✅ CP-005: Intentar confirmar pedido sin autenticación
**Pasos**:
1. Agregar productos sin autenticación
2. Abrir carrito
3. Intentar confirmar pedido

**Resultado Esperado**:
- Botones de "Iniciar Sesión" y "Registrarse" visibles
- NO hay botón de confirmar pedido
- Datos del carrito se preservan

### ✅ CP-006: Persistencia de carrito entre páginas
**Pasos**:
1. Agregar productos en index.php
2. Navegar a publico.php
3. Navegar de vuelta a index.php

**Resultado Esperado**:
- Carrito mantiene los productos
- Contador de carrito correcto en todas las páginas

## Validaciones de Seguridad

### ✓ Sanitización de Entradas
- `producto_id`: `filter_var(..., FILTER_VALIDATE_INT)`
- `cantidad`: `filter_var(..., FILTER_VALIDATE_INT)`
- `redirect`: Validación anti-URL absoluta

### ✓ Verificación de Stock
- No permite agregar más que stock disponible
- Actualiza stock_real en cada operación

### ✓ Validación de Producto
- Verifica existencia en BD
- Solo productos con `estado='activo'`
- Verifica disponibilidad antes de agregar

### ✓ Protección XSS
- Todos los outputs usan `htmlspecialchars()`
- JSON encoding seguro con `ENT_QUOTES`

## Problemas Conocidos y Soluciones

### ❌ Error de Red en Prompt
**Síntoma**: Error "A network error occurred. Please check your connection"
**Causa**: No relacionado con el carrito - problema de Kiro IDE
**Solución**: El carrito funciona correctamente en el navegador

### ✅ Sesión no iniciada
**Solución implementada**: Constructor de CarritoController inicia sesión automáticamente

### ✅ Redirección pierde filtros
**Solución implementada**: El parámetro `redirect` preserva query string

## Conclusión

El sistema de carrito está **completamente funcional para usuarios anónimos**. Todas las operaciones básicas (agregar, modificar, eliminar, vaciar) funcionan sin autenticación. Solo la confirmación del pedido requiere inicio de sesión, lo cual es el comportamiento esperado y correcto.

---
**Última actualización**: 2026-10-21  
**Versión del sistema**: 1.0  
**Estado**: ✅ FUNCIONANDO CORRECTAMENTE

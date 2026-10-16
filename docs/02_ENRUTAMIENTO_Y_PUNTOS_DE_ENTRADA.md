# Módulo 02: Enrutamiento y Puntos de Entrada

Este documento detalla los archivos ubicados en la raíz del proyecto que actúan como puntos de entrada (*Front Controllers*) y gestionan las peticiones HTTP del navegador.

---

## 1. `index.php`

### Propósito
Es el enrutador principal y controlador frontal (*Front Controller*) del sistema. Analiza el estado de la sesión y los parámetros de la URL (`?c=controlador&a=accion`) para despachar la petición a la clase y método correspondiente.

### Flujo de Ejecución:
1. **Verificación de Sesión Inicial:**
   * Si no hay sesión autenticada (`!is_authenticated()`), instancia `PublicoController` y ejecuta `inicio()` mostrando la página de bienvenida (*landing page* con hero y catálogo de muestra).
2. **Cliente Autenticado sin Parámetros:**
   * Si el usuario es un cliente (`!is_admin()`) y no especificó un parámetro `?c=`, se le muestra la página de inicio pública.
3. **Mapeo de Rutas y Controladores Válidos:**
   Define una lista blanca (`$controladoresValidos`) para evitar la carga de archivos no permitidos:
   * `'dashboard'` $\rightarrow$ `DashboardController`
   * `'productos'` $\rightarrow$ `ProductoController`
   * `'inventario'` $\rightarrow$ `InventarioController`
   * `'ventas'` $\rightarrow$ `VentaController`
   * `'historial'` $\rightarrow$ `HistorialController`
   * `'reportes'` $\rightarrow$ `ReporteController`
   * `'catalogo'` $\rightarrow$ `CatalogoController`
   * `'proveedores'` $\rightarrow$ `ProveedorController`
   * `'carrito'` $\rightarrow$ `CarritoController`
4. **Instanciación Dinámica y Ejecución:**
   * Verifica la existencia del archivo en la carpeta `controllers/`.
   * Crea una instancia de la clase y comprueba que el método (acción `$accion`) exista mediante `method_exists`.
   * Ejecuta `$instancia->$accion()`.

---

## 2. `login.php`

### Propósito
Punto de entrada directo para el inicio de sesión.

### Flujo de Ejecución:
1. Incluye `config/helpers.php` y el controlador `controllers/AuthController.php`.
2. Instancia `AuthController`.
3. Llama a `$auth->login()`.
   * Si la petición es **GET**, muestra la vista de login con el modal de ingreso.
   * Si la petición es **POST**, procesa las credenciales del formulario, valida la contraseña y crea la sesión.

---

## 3. `logout.php`

### Propósito
Punto de acceso rápido para cerrar la sesión activa del usuario desde enlaces del menú o botones de salida.

### Flujo de Ejecución:
1. Requiere `config/helpers.php` y `controllers/AuthController.php`.
2. Instancia `AuthController` y llama a `$auth->logout()`.
3. Destruye la información de sesión en el servidor, expira las cookies de sesión del navegador y redirige a `login.php`.

---

## 4. `publico.php`

### Propósito
Punto de entrada directo para visitantes o clientes que desean ver el catálogo público sin tener que iniciar sesión obligatoriamente.

### Flujo de Ejecución:
1. Requiere `config/helpers.php` y `controllers/PublicoController.php`.
2. Instancia `PublicoController`.
3. Ejecuta `$ctrl->index()`, cargando los quesos disponibles y mostrando la vista pública interactiva (`views/publico/catalogo.php`).

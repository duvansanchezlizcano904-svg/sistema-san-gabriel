# Documentación Técnica del Sistema — Quesos San Gabriel

Bienvenido a la documentación técnica oficial de **Quesos San Gabriel**, un sistema web integral para la gestión de inventario, ventas (POS), reportes, proveedores y catálogo digital de productos lácteos.

---

## 1. Arquitectura General del Sistema

El software está construido siguiendo el patrón arquitectónico **MVC (Modelo - Vista - Controlador)** en PHP nativo, con soporte para sesiones seguras, base de datos relacional MySQL y una interfaz reactiva construida con HTML5, Bootstrap 5 y JavaScript vanilla.

```
                      ┌─────────────────────────┐
                      │    Navegador Web        │
                      └────────────┬────────────┘
                                   │ HTTP Request (?c=controlador&a=accion)
                                   ▼
                      ┌─────────────────────────┐
                      │   index.php / login.php │ (Enrutador Front Controller)
                      └────────────┬────────────┘
                                   │ Invoca acción
                                   ▼
                      ┌─────────────────────────┐
                      │      Controlador        │ (Ej: ProductoController)
                      └─────┬──────────────┬────┘
      Consulta datos        │              │ Pasa variables limpias
      y lógica de negocio   ▼              ▼
     ┌────────────────────────┐          ┌────────────────────────┐
     │         Modelo         │          │         Vista          │
     │ (Ej: Producto.php)     │          │ (views/productos/...)  │
     └──────────┬─────────────┘          └────────────────────────┘
                │ PDO Prepared Stmt
                ▼
     ┌────────────────────────┐
     │    Base de Datos MySQL │
     │    (gabriel_lacteos)   │
     └────────────────────────┘
```

---

## 2. Pila Tecnológica (Stack)

* **Backend:** PHP 8.x (orientado a objetos, tipado estricto, PDO para consultas preparadas).
* **Base de Datos:** MySQL / MariaDB con motor InnoDB y soporte de claves foráneas e integridad referencial.
* **Frontend:** HTML5 semántico, CSS3 personalizado con variables y temas lácteos, Bootstrap 5.3, Bootstrap Icons 1.11.
* **JavaScript:** JavaScript Vanilla (ES6+) para interactividad del carrito de ventas en vivo, validaciones dinámicas y confirmaciones.
* **Seguridad:** Encriptación de contraseñas con `password_hash` (`BCRYPT`), mitigación de Cross-Site Scripting con `htmlspecialchars`, prevención de Inyección SQL con consultas preparadas PDO y control de acceso basado en roles (`RBAC`).

---

## 3. Estructura de Carpetas y Guía de Documentación

La documentación se encuentra dividida en módulos organizados archivo por archivo:

| Archivo de Documentación | Contenido y Módulos Abordados |
| :--- | :--- |
| **[01_CONFIGURACION_Y_BASE_DE_DATOS.md](file:///c:/laragon/www/php%20mi%20proyecto/docs/01_CONFIGURACION_Y_BASE_DE_DATOS.md)** | Configuración PDO (`database.php`), funciones globales (`helpers.php`), esquema SQL (`database.sql`) y modelo entidad-relación (`schema.dbml`). |
| **[02_ENRUTAMIENTO_Y_PUNTOS_DE_ENTRADA.md](file:///c:/laragon/www/php%20mi%20proyecto/docs/02_ENRUTAMIENTO_Y_PUNTOS_DE_ENTRADA.md)** | Archivos de entrada raíz: `index.php`, `login.php`, `logout.php` y `publico.php`. |
| **[03_MODELOS.md](file:///c:/laragon/www/php%20mi%20proyecto/docs/03_MODELOS.md)** | Modelos de datos: `Usuario.php`, `Producto.php`, `Venta.php`, `Proveedor.php` y `Pedido.php`. |
| **[04_CONTROLADORES.md](file:///c:/laragon/www/php%20mi%20proyecto/docs/04_CONTROLADORES.md)** | Todos los 11 controladores: `AuthController`, `DashboardController`, `ProductoController`, `InventarioController`, `VentaController`, `HistorialController`, `ReporteController`, `ProveedorController`, `CatalogoController`, `CarritoController` y `PublicoController`. |
| **[05_VISTAS.md](file:///c:/laragon/www/php%20mi%20proyecto/docs/05_VISTAS.md)** | Todas las vistas organizadas por submódulos (`layouts`, `login`, `inicio`, `productos`, `inventario`, `ventas`, `historial`, `reportes`, `proveedores`, `catalogo`, `carrito`, `factura`). |
| **[06_FRONTEND_Y_ASSETS.md](file:///c:/laragon/www/php%20mi%20proyecto/docs/06_FRONTEND_Y_ASSETS.md)** | Archivo JavaScript (`public/js/app.js`) y hojas de estilo CSS (`style.css`, `login.css`, `home.css`, `publico.css`). |

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
| **[01_CONFIGURACION_Y_BASE_DE_DATOS.md](01_CONFIGURACION_Y_BASE_DE_DATOS.md)** | Configuración PDO (`database.php`), funciones globales (`helpers.php`), PHPMailer (`config/mail.php`), composer y vendor, esquema SQL (`database/database.sql`) y modelo entidad-relación (`schema.dbml`). |
| **[02_ENRUTAMIENTO_Y_PUNTOS_DE_ENTRADA.md](02_ENRUTAMIENTO_Y_PUNTOS_DE_ENTRADA.md)** | Archivos de entrada raíz: `index.php`, `login.php`, `logout.php`, `publico.php` y `recuperar.php`. |
| **[03_MODELOS.md](03_MODELOS.md)** | Modelos de datos: `Usuario.php`, `Producto.php`, `Venta.php`, `Proveedor.php` y `Pedido.php`. |
| **[04_CONTROLADORES.md](04_CONTROLADORES.md)** | Los 11 controladores del sistema. |
| **[05_VISTAS.md](05_VISTAS.md)** | Todas las vistas organizadas por submódulos. |
| **[06_FRONTEND_Y_ASSETS.md](06_FRONTEND_Y_ASSETS.md)** | JavaScript y hojas de estilo CSS. |

### Documentación por Módulo

| Archivo | Descripción |
| :--- | :--- |
| [MODULO_AUTENTICACION.md](modulos/MODULO_AUTENTICACION.md) | Login, registro, logout, recuperación de contraseña |
| [MODULO_DASHBOARD.md](modulos/MODULO_DASHBOARD.md) | Panel de administración con KPIs |
| [MODULO_PRODUCTOS.md](modulos/MODULO_PRODUCTOS.md) | CRUD de productos |
| [MODULO_INVENTARIO.md](modulos/MODULO_INVENTARIO.md) | Control de stock |
| [MODULO_VENTAS.md](modulos/MODULO_VENTAS.md) | Punto de venta (POS) |
| [MODULO_HISTORIAL_Y_FACTURAS.md](modulos/MODULO_HISTORIAL_Y_FACTURAS.md) | Historial de ventas y facturación |
| [MODULO_REPORTES.md](modulos/MODULO_REPORTES.md) | Reportes financieros |
| [MODULO_PROVEEDORES.md](modulos/MODULO_PROVEEDORES.md) | Gestión de proveedores |
| [MODULO_CATALOGO.md](modulos/MODULO_CATALOGO.md) | Catálogo para clientes |
| [MODULO_CARRITO_Y_PEDIDOS.md](modulos/MODULO_CARRITO_Y_PEDIDOS.md) | Carrito y pedidos en línea |
| [MODULO_PAGINA_PUBLICA.md](modulos/MODULO_PAGINA_PUBLICA.md) | Página pública sin sesión |

### Documentación General

| Archivo | Descripción |
| :--- | :--- |
| [INSTALACION.md](INSTALACION.md) | Requisitos y configuración inicial |
| [BASE_DE_DATOS.md](BASE_DE_DATOS.md) | Esquema de tablas y relaciones |
| [SEGURIDAD.md](SEGURIDAD.md) | Medidas de seguridad implementadas |
| [ERRORES_COMUNES.md](ERRORES_COMUNES.md) | Errores frecuentes y soluciones |
| [ORDEN_IMPORTACION_BD.md](ORDEN_IMPORTACION_BD.md) | Cómo importar la base de datos |
| [RESUMEN_CAMBIOS.md](RESUMEN_CAMBIOS.md) | Historial de cambios del proyecto |

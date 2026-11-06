<?php
/*
╔══════════════════════════════════════════════════════════════════════════════╗
║                                                                              ║
║          🧀  QUESOS SAN GABRIEL — GUÍA DE APRENDIZAJE PHP                  ║
║                                                                              ║
║          Esta carpeta contiene explicaciones detalladas de cada             ║
║          parte del proyecto, diseñadas para principiantes.                  ║
║                                                                              ║
╚══════════════════════════════════════════════════════════════════════════════╝

¿QUÉ ES ESTE PROYECTO?
───────────────────────
"Quesos San Gabriel" es un sistema web de venta de quesos desarrollado en PHP.
Funciona como una tienda en línea donde:

  🛒 Los CLIENTES pueden:
     - Ver el catálogo de quesos disponibles
     - Agregar productos al carrito
     - Registrarse / iniciar sesión
     - Confirmar su compra y ver la factura

  ⚙️ El ADMINISTRADOR puede:
     - Gestionar el catálogo de productos
     - Ver y administrar el inventario
     - Registrar ventas manuales
     - Ver reportes e historial de pedidos
     - Gestionar proveedores


TECNOLOGÍAS USADAS:
────────────────────
  - PHP 8.x       → Lenguaje del servidor (lógica del negocio)
  - MySQL         → Base de datos (almacena productos, usuarios, pedidos)
  - PDO           → Librería PHP para comunicarse con MySQL de forma segura
  - HTML + CSS    → Estructura y diseño de las páginas
  - Bootstrap 5   → Framework CSS para diseño responsive
  - JavaScript    → Interactividad (AJAX para el carrito)
  - Laragon       → Entorno local (Apache + MySQL + PHP)


PATRÓN DE ARQUITECTURA: MVC
─────────────────────────────
  M = Model (Modelo)      → models/       → Comunica con MySQL
  V = View (Vista)        → views/        → Muestra el HTML
  C = Controller (Ctrl)   → controllers/  → Coordina todo


╔══════════════════════════════════════════════════════════════════════════════╗
║  ÍNDICE DE ARCHIVOS DE ESTA GUÍA                                             ║
╚══════════════════════════════════════════════════════════════════════════════╝

  01_login.php
  ─────────────
  ¿Cómo funciona el inicio de sesión?
  Explica: AuthController::login(), $_SESSION, password_verify(),
           roles (admin/cliente), redirección según rol.

  02_registro.php
  ────────────────
  ¿Cómo se registran nuevos usuarios?
  Explica: AuthController::registro(), validaciones de formulario,
           password_hash(), creación automática de nombre de usuario.

  03_agregar_productos.php
  ─────────────────────────
  ¿Cómo el administrador registra nuevos quesos?
  Explica: ProductoController::guardar(), validaciones, subida de imágenes,
           Producto::crear() con INSERT en MySQL.

  04_modelo_productos.php
  ────────────────────────
  ¿Dónde se guardan los productos y cómo se comunican con MySQL?
  Explica: models/Producto.php, PDO, consultas preparadas, tabla "productos",
           función imagenUrl(), descontarStock().

  05_rutas_y_controladores.php
  ─────────────────────────────
  ¿Cómo llega la información del formulario al controlador?
  Explica: index.php como enrutador, mapeo URL→Controlador,
           lista de todos los controladores, AJAX vs formulario normal.

  06_carrito_compras.php
  ───────────────────────
  ¿Cómo funciona el carrito de compras?
  Explica: Estructura de $_SESSION['carrito'], funciones agregar/actualizar/
           quitar/confirmar, tokens CSRF, transacciones MySQL (begin/commit/rollback).


╔══════════════════════════════════════════════════════════════════════════════╗
║  ESTRUCTURA COMPLETA DEL PROYECTO                                            ║
╚══════════════════════════════════════════════════════════════════════════════╝

  📁 php mi proyecto/
  │
  ├── 📄 index.php              → Enrutador principal del sistema
  ├── 📄 login.php              → Entrada al login
  ├── 📄 registro.php           → Entrada al registro
  ├── 📄 logout.php             → Cierra sesión
  ├── 📄 publico.php            → Vista pública sin login
  ├── 📄 database.sql           → Script para crear la base de datos
  │
  ├── 📁 config/
  │   ├── database.php          → Conexión PDO a MySQL (Singleton)
  │   └── helpers.php           → Funciones globales: redirect, sanitize, etc.
  │
  ├── 📁 controllers/
  │   ├── AuthController.php    → Login, registro, logout
  │   ├── CarritoController.php → Todo el carrito de compras
  │   ├── CatalogoController.php→ Catálogo de clientes logueados
  │   ├── DashboardController.php→ Panel de administración
  │   ├── HistorialController.php→ Historial de ventas/pedidos
  │   ├── InventarioController.php→ Vista de inventario
  │   ├── ProductoController.php → CRUD de productos
  │   ├── ProveedorController.php → CRUD de proveedores
  │   ├── PublicoController.php  → Catálogo público (sin login)
  │   ├── ReporteController.php  → Reportes del negocio
  │   └── VentaController.php    → Ventas manuales del admin
  │
  ├── 📁 models/
  │   ├── Pedido.php            → Gestión de pedidos de clientes
  │   ├── Producto.php          → Catálogo e inventario de quesos
  │   ├── Proveedor.php         → Gestión de proveedores
  │   ├── Usuario.php           → Autenticación y registro de usuarios
  │   └── Venta.php             → Ventas manuales del administrador
  │
  ├── 📁 views/
  │   ├── carrito/              → Vista del carrito, confirmación, factura
  │   ├── catalogo/             → Catálogo para clientes logueados
  │   ├── dashboard/            → Panel de control del admin
  │   ├── factura/              → Tiquete/comprobante de compra
  │   ├── historial/            → Historial de pedidos
  │   ├── inicio/               → Página de bienvenida pública (home.php)
  │   ├── inventario/           → Vista del inventario
  │   ├── layouts/              → Header, sidebar, footer (plantillas)
  │   ├── login/                → Formulario de inicio de sesión
  │   ├── partials/             → Componentes reutilizables (tarjeta_producto)
  │   ├── productos/            → CRUD de productos (crear, editar, listar)
  │   ├── proveedores/          → CRUD de proveedores
  │   ├── publico/              → Vistas de catálogo público
  │   ├── registro/             → Formulario de registro
  │   ├── reportes/             → Vistas de reportes
  │   └── ventas/               → Gestión de ventas manuales
  │
  ├── 📁 public/
  │   └── img/
  │       └── productos/        → Imágenes subidas de los quesos
  │
  ├── 📁 database/
  │   └── seeds/                → Scripts SQL para datos iniciales
  │
  └── 📁 GUIA_CODIGO_PHP/       ← ¡ESTÁS AQUÍ!
      ├── 00_LEEME_PRIMERO.php  → Este archivo (índice general)
      ├── 01_login.php          → Cómo funciona el login
      ├── 02_registro.php       → Cómo funciona el registro
      ├── 03_agregar_productos.php → Cómo agregar productos
      ├── 04_modelo_productos.php  → El modelo y MySQL
      ├── 05_rutas_y_controladores.php → Cómo funciona el enrutador
      └── 06_carrito_compras.php   → El carrito de compras completo


╔══════════════════════════════════════════════════════════════════════════════╗
║  BASE DE DATOS: gabriel_lacteos                                              ║
╚══════════════════════════════════════════════════════════════════════════════╝

  TABLAS PRINCIPALES:
  ┌──────────────────┬──────────────────────────────────────────────────────┐
  │ Tabla            │ Para qué sirve                                       │
  ├──────────────────┼──────────────────────────────────────────────────────┤
  │ usuarios         │ Clientes y administradores del sistema               │
  │ productos        │ Catálogo de quesos (inventario incluido)             │
  │ pedidos          │ Compras confirmadas de los clientes                  │
  │ detalle_pedidos  │ Qué productos y cantidades tiene cada pedido         │
  │ ventas           │ Ventas manuales registradas por el administrador     │
  │ detalle_ventas   │ Qué productos se vendieron en cada venta manual      │
  │ proveedores      │ Proveedores de quesos y materias primas              │
  └──────────────────┴──────────────────────────────────────────────────────┘


╔══════════════════════════════════════════════════════════════════════════════╗
║  GLOSARIO DE TÉRMINOS                                                        ║
╚══════════════════════════════════════════════════════════════════════════════╝

  PDO         → PHP Data Objects. Librería para conectar PHP con MySQL.
  MVC         → Modelo-Vista-Controlador. Patrón de organización de código.
  SESSION     → Memoria temporal en el servidor para recordar al usuario.
  POST        → Método para enviar datos de forma segura (formularios).
  GET         → Método para enviar datos en la URL (parámetros visibles).
  AJAX        → Petición al servidor sin recargar la página (JavaScript).
  JSON        → Formato de datos para respuestas AJAX.
  Hash        → Contraseña cifrada. No se puede descifrar, solo comparar.
  CSRF        → Ataque de suplantación. Token: protección contra él.
  SQL         → Lenguaje para consultar bases de datos MySQL.
  Sanitize    → Limpiar datos de entrada para evitar ataques XSS.
  Transacción → Grupo de operaciones SQL que se completan todas o ninguna.
  Stock       → Cantidad disponible de un producto en inventario.
  Singleton   → Patrón que crea solo UNA instancia de un objeto (la conexión).
  Rol         → Nivel de acceso: 'administrador' o 'cliente'.
  CRUD        → Create, Read, Update, Delete (crear, leer, actualizar, borrar).
  FK          → Foreign Key (llave foránea). Relación entre tablas.
  PK          → Primary Key (llave primaria). ID único de cada fila.
  XSS         → Cross-Site Scripting. Ataque de inyección de código HTML/JS.
  Middleware  → Código que se ejecuta antes de una acción (como require_admin).


╔══════════════════════════════════════════════════════════════════════════════╗
║  FLUJO GENERAL DEL SISTEMA                                                   ║
╚══════════════════════════════════════════════════════════════════════════════╝

  VISITANTE (sin cuenta)
  ───────────────────────
  [index.php] → Ve la página de inicio con los quesos disponibles
  [Clic "Agregar"] → El queso va al carrito ($_SESSION['carrito'])
  [Ve el carrito] → Puede ver sus productos y precios
  [Quiere comprar] → El sistema le pide Registrarse o Iniciar Sesión

  CLIENTE (con cuenta)
  ─────────────────────
  [Login] → Accede con correo y contraseña
  [Catálogo] → Navega y agrega quesos al carrito
  [Carrito] → Revisa su pedido y confirma la compra
  [Pedido registrado] → Ve su factura/tiquete

  ADMINISTRADOR
  ──────────────
  [Login admin] → Accede al Panel de Control (Dashboard)
  [Productos] → Registra, edita o elimina quesos del catálogo
  [Inventario] → Ve el stock actual de cada queso
  [Pedidos] → Ve los pedidos realizados por clientes
  [Ventas] → Registra ventas manuales (ventas físicas)
  [Reportes] → Genera reportes de ventas e inventario
  [Proveedores] → Gestiona los proveedores del negocio

*/

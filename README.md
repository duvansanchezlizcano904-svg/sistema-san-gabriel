# Quesos San Gabriel — Sistema de Gestión y Catálogo

Sistema web para la gestión de inventario, ventas y catálogo en línea de la quesería artesanal **San Gabriel**. Desarrollado bajo la arquitectura **MVC (Modelo-Vista-Controlador)** con **PHP nativo**, **PDO / MySQL** y diseño responsive moderno.

---

## 📁 Estructura del Proyecto

```text
php mi proyecto/
├── config/              # Configuración global y base de datos
│   ├── database.php     # Conexión PDO Singleton a MySQL (gabriel_lacteos)
│   └── helpers.php      # Funciones auxiliares de sesión, auth y utilidades
│
├── controllers/         # Controladores de la aplicación
│   ├── AuthController.php        # Autenticación, login y registro
│   ├── CarritoController.php     # Gestión del carrito de compras y pedidos
│   ├── CatalogoController.php    # Catálogo para clientes autenticados
│   ├── DashboardController.php   # Panel principal del administrador
│   ├── HistorialController.php   # Historial de ventas y pedidos
│   ├── InventarioController.php  # Control de stock y alertas
│   ├── ProductoController.php    # CRUD de productos y carga de imágenes
│   ├── ProveedorController.php   # CRUD de proveedores
│   ├── PublicoController.php     # Catálogo público y página home
│   ├── ReporteController.php     # Reportes y estadísticas
│   └── VentaController.php       # Registro de ventas POS en mostrador
│
├── models/              # Modelos de datos (consultas preparadas PDO)
│   ├── Pedido.php       # Manejo de pedidos online y comprobantes
│   ├── Producto.php     # Operaciones de catálogo, stock y filtros
│   ├── Proveedor.php    # Datos de proveedores de insumos
│   ├── Usuario.php      # Usuarios (admin / cliente), hashing de contraseñas
│   └── Venta.php        # Registro y detalle de ventas
│
├── views/               # Vistas y plantillas
│   ├── carrito/         # Vista del carrito y pantalla de confirmación
│   ├── catalogo/        # Catálogo admin / cliente autenticado
│   ├── dashboard/       # Dashboard con métricas de ventas y stock
│   ├── factura/         # Generación de tiquetes y comprobantes
│   ├── historial/       # Consulta de ventas pasadas
│   ├── inicio/          # Home page con catálogo público y categorías
│   ├── inventario/      # Vista de inventario y alertas de stock bajo
│   ├── layouts/         # Header, footer y sidebar comunes
│   ├── login/           # Formulario modal y vista de inicio de sesión
│   ├── partials/        # Componentes reutilizables (tarjeta_producto.php)
│   ├── productos/       # Formularios crear/editar/listar productos
│   ├── proveedores/     # Formularios crear/editar/listar proveedores
│   ├── publico/         # Catálogo público con modal de producto
│   ├── registro/        # Formulario de registro de clientes
│   ├── reportes/        # Métricas y exportación
│   └── ventas/          # Punto de venta en mostrador
│
├── public/              # Archivos estáticos accesibles directamente
│   ├── css/             # Estilos CSS (home.css, login.css, publico.css, style.css)
│   ├── img/             # Imágenes y fotografías de productos
│   │   ├── productos/   # Fotos de alta calidad de los quesos artesanales
│   │   └── placeholder-queso.png # Placeholder local limpio
│   └── js/              # Scripts frontend (app.js)
│
├── database/            # Scripts y migraciones de base de datos
│   ├── database.sql     # Esquema inicial completo de la base de datos
│   ├── migrations/      # Migraciones incrementales de columnas e índices
│   └── seeds/           # Datos semilla e inserciones de catálogo
│
├── docs/                # Documentación detallada del proyecto
│   ├── 00_INDICE_Y_ARQUITECTURA.md
│   ├── 01_CONFIGURACION_Y_BASE_DE_DATOS.md
│   ├── 02_ENRUTAMIENTO_Y_PUNTOS_DE_ENTRADA.md
│   ├── 03_MODELOS.md
│   ├── 04_CONTROLADORES.md
│   ├── 05_VISTAS.md
│   ├── 06_FRONTEND_Y_ASSETS.md
│   ├── ORDEN_IMPORTACION_BD.md
│   ├── schema.dbml
│   └── pruebas/         # Informes de pruebas funcionales
│
├── index.php            # Enrutador principal de la aplicación
├── publico.php          # Punto de entrada directo al catálogo público
├── login.php            # Punto de acceso directo al inicio de sesión
├── logout.php           # Cierre de sesión seguro
├── registro.php         # Punto de acceso directo al registro de clientes
├── database.sql         # Esquema SQL disponible en raíz para importación rápida
└── package.json         # Scripts de desarrollo (npm start / npm run dev)
```

---

## 🚀 Inicio Rápido

1. **Servidor local:** Asegúrate de tener iniciado MySQL en Laragon o XAMPP.
2. **Base de datos:** Importa `database.sql` en tu gestor MySQL (crea la base `gabriel_lacteos`).
3. **Ejecutar la aplicación:**
   ```bash
   npm start
   # o bien mediante tu servidor Apache en http://localhost/php%20mi%20proyecto/
   ```
4. **Accesos por defecto:**
   - **Administrador:** usuario `admin` | contraseña `admin`
   - **Cliente:** usuario `cliente` | contraseña `cliente123`
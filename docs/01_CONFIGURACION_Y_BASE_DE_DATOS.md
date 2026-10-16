# Módulo 01: Configuración y Base de Datos

Este documento describe detalladamente los archivos encargados de la conexión con el motor de base de datos, las utilidades globales y la definición del esquema relacional.

---

## 1. `config/database.php`

### Propósito
Proporciona la clase `Database`, encargada de instanciar y entregar una única conexión persistente hacia el servidor MySQL utilizando el patrón de diseño **Singleton**.

### Componentes clave
* **Propiedades estáticas privadas:**
  * `$host`: Dirección del host de la base de datos (por defecto `127.0.0.1`).
  * `$dbName`: Nombre de la base de datos (`gabriel_lacteos`).
  * `$username`: Usuario de acceso (`root`).
  * `$password`: Contraseña del usuario (por defecto vacía para Laragon/XAMPP).
  * `$charset`: Codificación de caracteres (`utf8mb4`).
  * `$connection`: Instancia estática de `PDO` que almacena la conexión activa.

* **Método `getConnection(): PDO`:**
  * Evalúa si `$connection === null`.
  * Configura opciones de PDO:
    * `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`: Convierte cualquier error de SQL en una excepción `PDOException`.
    * `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`: Hace que los resultados se retornen como arreglos asociativos indexados por el nombre de la columna.
    * `PDO::ATTR_EMULATE_PREPARES => false`: Desactiva la emulación para garantizar consultas preparadas nativas en el motor de MySQL (máxima seguridad contra SQLi).
    * `PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"`: Soporte completo para tildes, caracteres internacionales y emojis.
  * Si la conexión falla, captura la excepción y muestra una alerta visual HTML explicativa indicando si MySQL está encendido y si la base de datos existe.

---

## 2. `config/helpers.php`

### Propósito
Contiene las funciones auxiliares de uso global en toda la aplicación (sesiones, autenticación, sanitización de datos, mensajes flash y formateo).

### Funciones documentadas
1. **`is_authenticated(): bool`**
   * Retorna `true` si existe la variable `$_SESSION['usuario_id']`.
2. **`is_admin(): bool`**
   * Verifica si el usuario autenticado tiene el rol `'administrador'`.
3. **`current_user(): ?array`**
   * Retorna un arreglo asociativo con los datos del usuario en sesión (`id`, `nombre`, `usuario`, `email`, `rol`).
4. **`require_auth(): void`**
   * Redirige forzosamente a `login.php` si el usuario no ha iniciado sesión.
5. **`require_admin(): void`**
   * Verifica que el usuario sea administrador. Si es un cliente, le asigna un mensaje flash de acceso denegado y lo redirige al catálogo (`index.php?c=catalogo`).
6. **`set_flash(string $tipo, string $mensaje): void`**
   * Guarda un mensaje temporal en `$_SESSION['flash']` con su tipo (`success`, `danger`, `warning`, `info`) para ser mostrado en la siguiente vista.
7. **`get_flash(): ?array`**
   * Recupera el mensaje flash actual y lo elimina de la sesión para que solo se muestre una vez.
8. **`sanitize(mixed $data): mixed`**
   * Limpia recursivamente cadenas de texto eliminando espacios sobrantes (`trim`), barras invertidas (`stripslashes`) y caracteres no seguros.
9. **`redirect(string $url): void`**
   * Envía la cabecera HTTP `Location: $url` y detiene el script con `exit`.
10. **`formatearMoneda(float|int $valor): string`**
    * Retorna una cifra formateada con separador de miles y dos decimales en estilo colombiano (ej: `$ 15.000,00`).

---

## 3. `database.sql`

### Propósito
Script de inicialización y migración DDL/DML para crear la base de datos `gabriel_lacteos` y sus tablas relacionales.

### Tablas definidas en el esquema:
1. **`usuarios`**:
   * Almacena administradores y clientes (`id`, `nombre`, `usuario`, `password`, `email`, `rol`, `creado_en`).
   * `rol`: Tipo `ENUM('administrador', 'cliente')`.
2. **`productos`**:
   * Catálogo de quesos y derivados (`id`, `codigo`, `nombre`, `tipo`, `descripcion`, `precio_compra`, `precio_venta`, `cantidad`, `stock_minimo`, `unidad_medida`, `estado`).
   * Índices sobre `tipo` y `estado`.
3. **`ventas`**:
   * Encabezado de ventas POS (`id`, `numero_venta`, `fecha`, `total`, `cliente`, `notas`, `usuario_id`).
   * Clave foránea `fk_ventas_usuario` apuntando a `usuarios(id)`.
4. **`detalle_ventas`**:
   * Renglones de venta (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`).
   * Restricción `ON DELETE RESTRICT` en `producto_id` para impedir borrar quesos vendidos.
5. **`proveedores`**:
   * Datos de empresas abastecedoras (`id`, `nombre`, `contacto`, `telefono`, `email`, `direccion`, `nit`, `notas`, `estado`).
6. **`pedidos`**:
   * Registro de compras hechas por clientes desde el catálogo digital (`id`, `numero_pedido`, `fecha`, `total`, `cliente_id`, `notas`, `estado`).
7. **`detalle_pedidos`**:
   * Productos comprados dentro de cada pedido.

---

## 4. `schema.dbml`

### Propósito
Especificación del modelo de base de datos en formato DBML (Database Markup Language), utilizable para generar diagramas entidad-relación en herramientas visuales como *dbdocs.io* o *dbdiagram.io*. Define las relaciones `1:N` entre usuarios y ventas, ventas y detalles, productos y pedidos.

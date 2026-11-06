<?php
/*
╔══════════════════════════════════════════════════════════════════════════════╗
║           GUÍA DE APRENDIZAJE — QUESOS SAN GABRIEL                         ║
║           ARCHIVO 04: EL MODELO DE PRODUCTOS                                ║
╚══════════════════════════════════════════════════════════════════════════════╝

¿QUÉ ES UN MODELO (MODEL)?
───────────────────────────
En el patrón MVC (Modelo - Vista - Controlador), el MODELO es la capa
que se encarga de TODA la comunicación con la base de datos.

Es como el "almacén" de tu empresa:
  - El CONTROLADOR (gerente) le pide: "Dame 10 quesos tipo fresco"
  - El MODELO (almacenero) va a MySQL y trae esa información
  - La VISTA (vitrina) la muestra al cliente

El Modelo NO decide qué mostrar ni cómo procesar datos.
Solo sabe LEER y ESCRIBIR en la base de datos.


╔══════════════════════════════════════════════════════════════════════════════╗
║  ARCHIVO PRINCIPAL: models/Producto.php                                     ║
╚══════════════════════════════════════════════════════════════════════════════╝

FUNCIONES DEL MODELO Producto:
────────────────────────────────

  listarTodos()              → Lista todos los productos (con filtros opcionales)
  obtenerPorId($id)          → Obtiene UN producto por su ID
  existeCodigo($codigo)      → Verifica si ya existe un código en la BD
  crear($datos)              → Inserta un nuevo producto (INSERT)
  actualizar($id, $datos)    → Modifica un producto existente (UPDATE)
  eliminar($id)              → Borra un producto (DELETE)
  descontarStock($id, $cant) → Reduce el inventario cuando se vende
  obtenerEstadisticas()      → Resumen de totales para el dashboard
  obtenerTipos()             → Lista los tipos de productos disponibles
  obtenerCategorias()        → Lista las categorías registradas
  imagenUrl($producto)       → Devuelve la URL correcta de la imagen
*/


// ─── CÓMO SE CONECTA EL MODELO A LA BASE DE DATOS ───────────────────────────
/*
    class Producto {
        private PDO $db;

        public function __construct() {
            $this->db = Database::getConnection();
        }
    }
*/

// 💡 PDO (PHP Data Objects) es la librería de PHP para hablar con bases de datos.
//    Database::getConnection() está en config/database.php.
//    Usa el patrón SINGLETON: solo crea UNA conexión y la reutiliza en todo el proyecto.
//    Esto evita abrir muchas conexiones a MySQL innecesariamente.


// ─── FUNCIÓN: listarTodos() — La más compleja y más usada ────────────────────
/*
    public function listarTodos(
        string $busqueda = '',
        string $tipo = '',
        string $estado = '',
        string $categoria = '',
        string $tipoLeche = '',
        string $intensidad = ''
    ): array {

        $sql = "SELECT * FROM productos WHERE 1=1";
        $params = [];

        // Agregar filtros dinámicamente si se proporcionan
        if (!empty($busqueda)) {
            $sql .= " AND (nombre LIKE :busqueda OR codigo LIKE :busqueda OR descripcion LIKE :busqueda)";
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        if (!empty($tipo)) {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $tipo;
        }

        // ... más filtros ...

        $sql .= " ORDER BY categoria ASC, nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();   // Retorna TODOS los resultados como array
    }
*/

// 💡 "WHERE 1=1" es un truco de programación:
//    Siempre es verdadero, entonces podemos agregar " AND ..." sin problemas.
//    Si empezáramos con WHERE vacío, el primer AND daría error de sintaxis SQL.
//
// 💡 "LIKE '%texto%'" busca el texto en cualquier parte del campo.
//    El % es un comodín: %queso% → encuentra "Queso Fresco", "Queso Azul", etc.
//
// 💡 fetchAll() → devuelve TODOS los resultados como array PHP
//    fetch()    → devuelve UN SOLO resultado (el primero que encuentre)


// ─── FUNCIÓN: obtenerPorId() ─────────────────────────────────────────────────
/*
    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT * FROM productos WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }
*/

// 💡 LIMIT 1: Solo necesitamos un resultado, esto optimiza la consulta SQL.
// 💡 ?: null → Si fetch() retorna false (no encontró nada), retornamos null.
// 💡 ?array → El signo ? antes del tipo significa "puede ser null o array".


// ─── FUNCIÓN: descontarStock() — Usada al confirmar compras ──────────────────
/*
    public function descontarStock(int $id, int $cantidadVendida, ?PDO $conn = null): bool {
        $db = $conn ?: $this->db;
        $sql = "UPDATE productos
                SET cantidad = cantidad - :cantidad
                WHERE id = :id AND cantidad >= :cantidad";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':cantidad' => $cantidadVendida,
            ':id'       => $id
        ]);
        return $stmt->rowCount() > 0;
    }
*/

// 💡 "cantidad = cantidad - :cantidad" → resta directamente en MySQL.
//    Es más seguro que: leer → restar en PHP → actualizar.
//    (Evita problemas si dos personas compran al mismo tiempo - "race condition")
//
// 💡 "WHERE cantidad >= :cantidad" → Solo resta si hay suficiente stock.
//    Si no hay stock, no hace el UPDATE y rowCount() retorna 0.
//
// 💡 rowCount() → cuántas filas fueron afectadas por el UPDATE.
//    Si es > 0 → el descuento fue exitoso.
//    Si es 0   → no había suficiente stock (protección anti-overselling).


// ─── FUNCIÓN: imagenUrl() — Lógica de imágenes ──────────────────────────────
/*
    public static function imagenUrl(array $producto): string {
        $imgGuardada = trim($producto['imagen'] ?? '');

        // 1. Si tiene URL externa (http://...) usar esa
        if (filter_var($imgGuardada, FILTER_VALIDATE_URL)) {
            return $imgGuardada;
        }

        // 2. Si tiene ruta local y el archivo existe, usarla
        if ($imgGuardada !== '' && file_exists(__DIR__ . '/../' . $imgGuardada)) {
            return $imgGuardada;
        }

        // 3. Si no tiene imagen, usar imagen por categoría
        $catMap = [
            'Quesos Frescos'     => 'public/img/productos/queso_fresco.jpg',
            'Quesos Madurados'   => 'public/img/productos/parmesano.jpg',
            'Quesos Artesanales' => 'public/img/productos/queso_artesanal.jpg',
            'Quesos Cremosos'    => 'public/img/productos/brie.jpg',
            'Quesos de Cabra'    => 'public/img/productos/queso_cabra.jpg',
            'Quesos Azules'      => 'public/img/productos/queso_azul.jpg',
        ];

        $cat = $producto['categoria'] ?? $producto['tipo'] ?? '';
        return $catMap[$cat] ?? 'public/img/productos/queso_fresco.jpg';
    }
*/

// 💡 Esta función tiene 3 niveles de fallback (respaldo):
//    1. URL externa en BD → la usa directamente
//    2. Archivo local en BD → verifica que exista
//    3. Sin imagen → imagen genérica por categoría
//    4. Sin categoría → imagen por defecto universal


// ─── CÓMO FUNCIONA LA CONEXIÓN PDO ──────────────────────────────────────────
/*

  La clase Database en config/database.php gestiona la conexión:

  class Database {
      private static ?PDO $connection = null;

      public static function getConnection(): PDO {
          if (self::$connection === null) {
              $dsn = "mysql:host=127.0.0.1;dbname=gabriel_lacteos;charset=utf8mb4";
              self::$connection = new PDO($dsn, 'root', '', [
                  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                  PDO::ATTR_EMULATE_PREPARES   => false,
              ]);
          }
          return self::$connection;
      }
  }

  OPCIONES IMPORTANTES:
  - ERRMODE_EXCEPTION  → Si SQL falla, lanza una Exception (capturamos con try/catch)
  - FETCH_ASSOC        → Los resultados vienen como array con nombres de columna
                         Ej: $producto['nombre'] en vez de $producto[0]
  - EMULATE_PREPARES=false → Consultas preparadas REALES (máxima protección)

*/


// ─── FLUJO DE UNA CONSULTA SQL COMPLETA ──────────────────────────────────────
/*

  1. Preparar consulta:
     $stmt = $this->db->prepare("SELECT * FROM productos WHERE id = :id");
     
     PHP envía la plantilla SQL a MySQL sin datos todavía.
     MySQL la "precompila" por seguridad.

  2. Ejecutar con datos:
     $stmt->execute([':id' => 5]);
     
     MySQL combina la plantilla con los datos de forma segura.
     Los datos NO pueden romper la consulta SQL.

  3. Obtener resultados:
     $resultado = $stmt->fetch();     // Un solo registro
     $resultados = $stmt->fetchAll(); // Todos los registros

*/


// ─── DIAGRAMA: VIAJE DE LOS DATOS ────────────────────────────────────────────
/*

  [Vista HTML]         [Controlador]         [Modelo]          [MySQL]
      │                    │                    │                 │
      │ POST datos         │                    │                 │
      │──────────────────▶ │                    │                 │
      │                    │ listarTodos()      │                 │
      │                    │──────────────────▶ │                 │
      │                    │                    │ SELECT * FROM   │
      │                    │                    │ productos       │
      │                    │                    │───────────────▶ │
      │                    │                    │                 │
      │                    │                    │ [10 filas]      │
      │                    │                    │◀─────────────── │
      │                    │ [array $productos]  │                 │
      │                    │◀────────────────── │                 │
      │ renderiza HTML     │                    │                 │
      │◀─────────────────  │                    │                 │

*/


// ─── RESUMEN PARA PRINCIPIANTES ─────────────────────────────────────────────
/*

  📌 CONCEPTOS QUE APRENDISTE AQUÍ:

  1. PATRÓN MVC:
     - Modelo → solo habla con la base de datos
     - Vista  → solo muestra al usuario
     - Controlador → coordina entre Modelo y Vista

  2. PDO (PHP Data Objects):
     - Librería estándar de PHP para bases de datos MySQL
     - Usa prepare() + execute() para consultas seguras

  3. CONSULTAS PREPARADAS:
     - Separan el SQL de los datos
     - Previenen SQL Injection completamente

  4. TIPOS DE CONSULTAS SQL:
     - SELECT → leer datos (listarTodos, obtenerPorId)
     - INSERT → agregar datos nuevos (crear)
     - UPDATE → modificar datos existentes (actualizar, descontarStock)
     - DELETE → eliminar datos (eliminar)

  5. SINGLETON:
     - Solo existe UNA conexión a la base de datos en todo el sistema.
     - Se reutiliza en cada clase que necesite consultar MySQL.

  6. MANEJO DE IMÁGENES:
     - Las imágenes no se guardan en MySQL, solo su RUTA (texto).
     - El archivo real va en la carpeta public/img/productos/.

*/

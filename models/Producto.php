<?php
/**
 * Modelo de Producto
 * Gestiona el catálogo de productos lácteos y el inventario.
 */

require_once __DIR__ . '/../config/database.php';

class Producto {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Asegura que las nuevas variedades de quesos estén cargadas en la BD
     */
    public function asegurarCatalogoCompleto(): void {
        static $verificado = false;
        if ($verificado) return;
        $verificado = true;

        try {
            $check = $this->db->query("SELECT COUNT(*) FROM productos WHERE codigo = 'QAR-006'")->fetchColumn();
            if ((int)$check === 0) {
                $sqlSeed = __DIR__ . '/../database/seeds/inserts_mas_quesos.sql';
                if (file_exists($sqlSeed)) {
                    $sql = file_get_contents($sqlSeed);
                    $sql = preg_replace('/USE\s+`?[a-zA-Z0-9_]+`?;/i', '', $sql);
                    $this->db->exec($sql);
                }
            }
        } catch (Throwable $e) {
            error_log("Producto::asegurarCatalogoCompleto(): " . $e->getMessage());
        }
    }

    /**
     * Lista todos los productos con soporte para búsqueda y filtros avanzados
     */
    public function listarTodos(
        string $busqueda = '', 
        string $tipo = '', 
        string $estado = '',
        string $categoria = '',
        string $tipoLeche = '',
        string $intensidad = ''
    ): array {
        $this->asegurarCatalogoCompleto();
        $sql = "SELECT * FROM productos WHERE 1=1";
        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (nombre LIKE :b1 OR codigo LIKE :b2 OR descripcion LIKE :b3)";
            $params[':b1'] = '%' . $busqueda . '%';
            $params[':b2'] = '%' . $busqueda . '%';
            $params[':b3'] = '%' . $busqueda . '%';
        }

        if (!empty($tipo)) {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $tipo;
        }

        if (!empty($estado)) {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        if (!empty($categoria)) {
            $sql .= " AND categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        if (!empty($tipoLeche)) {
            $sql .= " AND tipo_leche = :tipo_leche";
            $params[':tipo_leche'] = $tipoLeche;
        }

        if (!empty($intensidad)) {
            $sql .= " AND intensidad_sabor = :intensidad";
            $params[':intensidad'] = $intensidad;
        }

        $sql .= " ORDER BY categoria ASC, nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lista productos con paginación para carga infinita
     * 
     * @param int $pagina Número de página (1-indexed)
     * @param int $porPagina Cantidad de productos por página
     * @param string $busqueda Término de búsqueda
     * @param string $tipo Filtro por tipo
     * @param string $estado Filtro por estado
     * @param string $categoria Filtro por categoría
     * @return array ['productos' => array, 'total' => int, 'pagina' => int, 'porPagina' => int, 'hayMas' => bool]
     */
    public function listarConPaginacion(
        int $pagina = 1,
        int $porPagina = 8,
        string $busqueda = '',
        string $tipo = '',
        string $estado = 'activo',
        string $categoria = '',
        string $tipoLeche = '',
        string $intensidad = ''
    ): array {
        $this->asegurarCatalogoCompleto();
        
        // Construir consulta base con filtros
        $whereSql = "WHERE 1=1";
        $params = [];

        if (!empty($busqueda)) {
            $whereSql .= " AND (nombre LIKE :b1 OR codigo LIKE :b2 OR descripcion LIKE :b3)";
            $params[':b1'] = '%' . $busqueda . '%';
            $params[':b2'] = '%' . $busqueda . '%';
            $params[':b3'] = '%' . $busqueda . '%';
        }

        if (!empty($tipo)) {
            $whereSql .= " AND tipo = :tipo";
            $params[':tipo'] = $tipo;
        }

        if (!empty($estado)) {
            $whereSql .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        if (!empty($categoria)) {
            $whereSql .= " AND categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        if (!empty($tipoLeche)) {
            $whereSql .= " AND tipo_leche = :tipo_leche";
            $params[':tipo_leche'] = $tipoLeche;
        }

        if (!empty($intensidad)) {
            $whereSql .= " AND intensidad_sabor = :intensidad";
            $params[':intensidad'] = $intensidad;
        }

        // Contar total de productos que cumplen los filtros
        $countSql = "SELECT COUNT(*) FROM productos {$whereSql}";
        $stmtCount = $this->db->prepare($countSql);
        $stmtCount->execute($params);
        $total = (int)$stmtCount->fetchColumn();

        // Calcular offset y asegurar enteros para compatibilidad nativa con PDO (ATTR_EMULATE_PREPARES => false)
        $limitInt  = max(1, (int)$porPagina);
        $offsetInt = max(0, (int)(($pagina - 1) * $limitInt));

        // Obtener productos paginados (casteo a entero estricto evita error de sintaxis en MySQL con prepare nativo)
        $sql = "SELECT * FROM productos {$whereSql} ORDER BY categoria ASC, nombre ASC LIMIT {$limitInt} OFFSET {$offsetInt}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $productos = $stmt->fetchAll();

        // Calcular si hay más páginas
        $hayMas = ($offsetInt + count($productos)) < $total;

        return [
            'productos' => $productos,
            'total' => $total,
            'pagina' => $pagina,
            'porPagina' => $porPagina,
            'hayMas' => $hayMas
        ];
    }

    /**
     * Obtiene un producto por su ID
     */
    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT * FROM productos WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Verifica si un código ya está en uso (opcionalmente ignorando un ID en caso de edición)
     */
    public function existeCodigo(string $codigo, ?int $ignorarId = null): bool {
        $sql = "SELECT id FROM productos WHERE codigo = :codigo";
        $params = [':codigo' => $codigo];

        if ($ignorarId !== null) {
            $sql .= " AND id != :ignorarId";
            $params[':ignorarId'] = $ignorarId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    /**
     * Registra un nuevo producto lácteo
     */
    public function crear(array $datos): bool {
        $sql = "INSERT INTO productos 
                (codigo, nombre, tipo, categoria, tipo_leche, intensidad_sabor, tiempo_maduracion, 
                 peso_presentacion, origen, descripcion, imagen, precio_compra, precio_venta, cantidad, 
                 stock_minimo, unidad_medida, estado) 
                VALUES 
                (:codigo, :nombre, :tipo, :categoria, :tipo_leche, :intensidad_sabor, :tiempo_maduracion,
                 :peso_presentacion, :origen, :descripcion, :imagen, :precio_compra, :precio_venta, :cantidad, 
                 :stock_minimo, :unidad_medida, :estado)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':codigo'             => $datos['codigo'],
            ':nombre'             => $datos['nombre'],
            ':tipo'               => $datos['tipo'],
            ':categoria'          => $datos['categoria'] ?? null,
            ':tipo_leche'         => $datos['tipo_leche'] ?? null,
            ':intensidad_sabor'   => $datos['intensidad_sabor'] ?? 'medio',
            ':tiempo_maduracion'  => $datos['tiempo_maduracion'] ?? null,
            ':peso_presentacion'  => $datos['peso_presentacion'] ?? null,
            ':origen'             => $datos['origen'] ?? null,
            ':descripcion'        => $datos['descripcion'] ?? '',
            ':imagen'             => $datos['imagen'] ?? null,
            ':precio_compra'      => $datos['precio_compra'],
            ':precio_venta'       => $datos['precio_venta'],
            ':cantidad'           => $datos['cantidad'],
            ':stock_minimo'       => $datos['stock_minimo'],
            ':unidad_medida'      => $datos['unidad_medida'],
            ':estado'             => $datos['estado'] ?? 'activo'
        ]);
    }

    /**
     * Actualiza un producto existente
     */
    public function actualizar(int $id, array $datos): bool {
        // Si no se sube imagen nueva, mantener la actual
        $imagenSql = array_key_exists('imagen', $datos) ? ",\n                imagen = :imagen" : '';

        $sql = "UPDATE productos SET 
                codigo = :codigo,
                nombre = :nombre,
                tipo = :tipo,
                categoria = :categoria,
                tipo_leche = :tipo_leche,
                intensidad_sabor = :intensidad_sabor,
                tiempo_maduracion = :tiempo_maduracion,
                peso_presentacion = :peso_presentacion,
                origen = :origen,
                descripcion = :descripcion,
                precio_compra = :precio_compra,
                precio_venta = :precio_venta,
                cantidad = :cantidad,
                stock_minimo = :stock_minimo,
                unidad_medida = :unidad_medida,
                estado = :estado{$imagenSql}
                WHERE id = :id";

        $params = [
            ':id'                 => $id,
            ':codigo'             => $datos['codigo'],
            ':nombre'             => $datos['nombre'],
            ':tipo'               => $datos['tipo'],
            ':categoria'          => $datos['categoria'] ?? null,
            ':tipo_leche'         => $datos['tipo_leche'] ?? null,
            ':intensidad_sabor'   => $datos['intensidad_sabor'] ?? 'medio',
            ':tiempo_maduracion'  => $datos['tiempo_maduracion'] ?? null,
            ':peso_presentacion'  => $datos['peso_presentacion'] ?? null,
            ':origen'             => $datos['origen'] ?? null,
            ':descripcion'        => $datos['descripcion'] ?? '',
            ':precio_compra'      => $datos['precio_compra'],
            ':precio_venta'       => $datos['precio_venta'],
            ':cantidad'           => $datos['cantidad'],
            ':stock_minimo'       => $datos['stock_minimo'],
            ':unidad_medida'      => $datos['unidad_medida'],
            ':estado'             => $datos['estado'] ?? 'activo',
        ];

        if (array_key_exists('imagen', $datos)) {
            $params[':imagen'] = $datos['imagen'];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Cambia el estado de un producto (borrado lógico: activo / inactivo)
     */
    public function cambiarEstado(int $id, string $nuevoEstado): bool {
        if (!in_array($nuevoEstado, ['activo', 'inactivo'], true)) {
            throw new InvalidArgumentException("Estado no válido.");
        }
        $sql = "UPDATE productos SET estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado' => $nuevoEstado,
            ':id'     => $id
        ]);
    }

    /**
     * Elimina un producto. Si tiene ventas históricas, evita la eliminación o marca error controlado
     */
    public function eliminar(int $id): bool {
        // Verificar si tiene ventas asociadas
        $sqlCheck = "SELECT COUNT(*) FROM detalle_ventas WHERE producto_id = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $id]);
        $tieneVentas = $stmtCheck->fetchColumn();

        if ($tieneVentas > 0) {
            throw new Exception("No se puede eliminar el producto porque tiene ventas registradas en el historial. Puedes cambiar su estado a 'Inactivo' para ocultarlo.");
        }

        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Descuenta del inventario la cantidad vendida dentro de una transacción
     */
    public function descontarStock(int $id, int $cantidadVendida, ?PDO $conn = null): bool {
        $db = $conn ?: $this->db;
        $sql = "UPDATE productos SET cantidad = cantidad - :cantidad_val WHERE id = :id AND cantidad >= :cantidad_min";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':cantidad_val' => $cantidadVendida,
            ':cantidad_min' => $cantidadVendida,
            ':id'           => $id
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Obtiene estadísticas globales de productos e inventario
     */
    public function obtenerEstadisticas(): array {
        $sql = "SELECT 
                    COUNT(*) as total_productos,
                    SUM(CASE WHEN cantidad > stock_minimo THEN 1 ELSE 0 END) as disponibles,
                    SUM(CASE WHEN cantidad <= stock_minimo AND cantidad > 0 THEN 1 ELSE 0 END) as stock_bajo,
                    SUM(CASE WHEN cantidad <= 0 THEN 1 ELSE 0 END) as agotados,
                    SUM(cantidad * precio_venta) as valor_inventario_venta,
                    SUM(cantidad * precio_compra) as valor_inventario_costo
                FROM productos 
                WHERE estado = 'activo'";
        $stmt = $this->db->query($sql);
        $stats = $stmt->fetch();
        
        return [
            'total_productos' => (int)($stats['total_productos'] ?? 0),
            'disponibles'     => (int)($stats['disponibles'] ?? 0),
            'stock_bajo'      => (int)($stats['stock_bajo'] ?? 0),
            'agotados'        => (int)($stats['agotados'] ?? 0),
            'valor_venta'     => (float)($stats['valor_inventario_venta'] ?? 0),
            'valor_costo'     => (float)($stats['valor_inventario_costo'] ?? 0),
        ];
    }

    /**
     * Obtiene los productos con stock bajo o agotados para alertas rápidas
     */
    public function obtenerProductosCriticos(): array {
        $sql = "SELECT * FROM productos 
                WHERE estado = 'activo' AND cantidad <= stock_minimo 
                ORDER BY cantidad ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los tipos de productos registrados para filtros dinámicos
     */
    public function obtenerTipos(): array {
        $sql = "SELECT DISTINCT tipo FROM productos WHERE tipo IS NOT NULL ORDER BY tipo ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtiene las categorías de productos registradas para filtros dinámicos
     */
    public function obtenerCategorias(): array {
        $sql = "SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL ORDER BY categoria ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtiene los tipos de leche registrados para filtros dinámicos
     */
    public function obtenerTiposLeche(): array {
        $sql = "SELECT DISTINCT tipo_leche FROM productos WHERE tipo_leche IS NOT NULL ORDER BY tipo_leche ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtiene las intensidades de sabor disponibles
     */
    public function obtenerIntensidades(): array {
        return ['suave', 'medio', 'fuerte', 'muy fuerte'];
    }

    /**
     * Devuelve la URL de la imagen de un producto.
     * Prioridad: imagen guardada en BD → placeholder local.
     *
     * @param  array  $producto  Fila de la tabla productos
     * @return string            Ruta relativa lista para <img src="...">.
     */
    public static function imagenUrl(array $producto): string {
        $imgGuardada = trim($producto['imagen'] ?? $producto['imagen_url'] ?? '');
        if ($imgGuardada !== '') {
            if (filter_var($imgGuardada, FILTER_VALIDATE_URL) || str_starts_with($imgGuardada, 'http')) {
                return $imgGuardada;
            }
            if (file_exists(__DIR__ . '/../' . $imgGuardada)) {
                return $imgGuardada;
            }
        }
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

    /**
     * Obtiene estadísticas por categoría
     */
    public function obtenerEstadisticasPorCategoria(): array {
        $sql = "SELECT 
                    categoria,
                    COUNT(*) as total,
                    SUM(cantidad) as stock_total,
                    AVG(precio_venta) as precio_promedio
                FROM productos 
                WHERE estado = 'activo' AND categoria IS NOT NULL
                GROUP BY categoria
                ORDER BY categoria ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}

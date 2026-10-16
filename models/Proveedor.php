<?php
/**
 * Modelo de Proveedor
 * Gestiona el CRUD de proveedores del sistema San Gabriel.
 */

require_once __DIR__ . '/../config/database.php';

class Proveedor {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Lista todos los proveedores con soporte para búsqueda y filtro de estado
     */
    public function listarTodos(string $busqueda = '', string $estado = ''): array {
        $sql = "SELECT * FROM proveedores WHERE 1=1";
        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (nombre LIKE :busqueda OR contacto LIKE :busqueda OR telefono LIKE :busqueda OR email LIKE :busqueda OR nit LIKE :busqueda)";
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        if (!empty($estado)) {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un proveedor por su ID
     */
    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT * FROM proveedores WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Verifica si un NIT ya está registrado (ignorando un ID al editar)
     */
    public function existeNit(string $nit, ?int $ignorarId = null): bool {
        if (empty(trim($nit))) return false;

        $sql = "SELECT id FROM proveedores WHERE nit = :nit";
        $params = [':nit' => $nit];

        if ($ignorarId !== null) {
            $sql .= " AND id != :ignorarId";
            $params[':ignorarId'] = $ignorarId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    /**
     * Registra un nuevo proveedor
     */
    public function crear(array $datos): bool {
        $sql = "INSERT INTO proveedores 
                    (nombre, contacto, telefono, email, direccion, nit, notas, estado)
                VALUES 
                    (:nombre, :contacto, :telefono, :email, :direccion, :nit, :notas, :estado)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'    => $datos['nombre'],
            ':contacto'  => $datos['contacto']  ?? null,
            ':telefono'  => $datos['telefono']  ?? null,
            ':email'     => $datos['email']     ?? null,
            ':direccion' => $datos['direccion'] ?? null,
            ':nit'       => $datos['nit']       ?? null,
            ':notas'     => $datos['notas']     ?? null,
            ':estado'    => $datos['estado']    ?? 'activo',
        ]);
    }

    /**
     * Actualiza un proveedor existente
     */
    public function actualizar(int $id, array $datos): bool {
        $sql = "UPDATE proveedores SET
                    nombre    = :nombre,
                    contacto  = :contacto,
                    telefono  = :telefono,
                    email     = :email,
                    direccion = :direccion,
                    nit       = :nit,
                    notas     = :notas,
                    estado    = :estado
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'        => $id,
            ':nombre'    => $datos['nombre'],
            ':contacto'  => $datos['contacto']  ?? null,
            ':telefono'  => $datos['telefono']  ?? null,
            ':email'     => $datos['email']     ?? null,
            ':direccion' => $datos['direccion'] ?? null,
            ':nit'       => $datos['nit']       ?? null,
            ':notas'     => $datos['notas']     ?? null,
            ':estado'    => $datos['estado']    ?? 'activo',
        ]);
    }

    /**
     * Elimina un proveedor por ID
     */
    public function eliminar(int $id): bool {
        $sql = "DELETE FROM proveedores WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Estadísticas rápidas para el dashboard o encabezado del módulo
     */
    public function obtenerEstadisticas(): array {
        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'activo'   THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivos
                FROM proveedores";
        $stmt = $this->db->query($sql);
        $stats = $stmt->fetch();
        return [
            'total'    => (int)($stats['total']    ?? 0),
            'activos'  => (int)($stats['activos']  ?? 0),
            'inactivos'=> (int)($stats['inactivos'] ?? 0),
        ];
    }
}

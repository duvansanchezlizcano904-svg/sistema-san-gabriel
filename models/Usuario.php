<?php
/**
 * Modelo de Usuario
 * Encargado de la autenticación y registro de usuarios.
 */

require_once __DIR__ . '/../config/database.php';

class Usuario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Busca un usuario por su nombre de usuario (campo usuario)
     */
    public function obtenerPorUsuario(string $usuario): ?array {
        $sql = "SELECT id, nombre, usuario, password, email, rol, creado_en 
                FROM usuarios 
                WHERE usuario = :usuario 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario' => $usuario]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Busca un usuario por su correo electrónico
     */
    public function obtenerPorEmail(string $email): ?array {
        $sql = "SELECT id, nombre, usuario, password, email, rol, creado_en 
                FROM usuarios 
                WHERE email = :email 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Obtiene los datos del usuario por ID
     */
    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT id, nombre, usuario, email, rol, creado_en 
                FROM usuarios 
                WHERE id = :id 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Valida credenciales con password_verify — acepta email o nombre de usuario
     */
    public function autenticar(string $emailOUsuario, string $password): ?array {
        // Intentar primero por email, luego por nombre de usuario
        $user = $this->obtenerPorEmail($emailOUsuario)
             ?? $this->obtenerPorUsuario($emailOUsuario);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    /**
     * Registra un nuevo cliente en la base de datos.
     * Solo puede crear cuentas con rol 'cliente'.
     *
     * @param string $nombre       Nombre completo
     * @param string $email        Correo electrónico único
     * @param string $telefono     Teléfono (se guarda como parte del campo usuario)
     * @param string $password     Contraseña en texto plano (se hashea aquí)
     * @return bool True si el registro fue exitoso
     */
    public function registrar(string $nombre, string $email, string $telefono, string $password): bool {
        // Generar un nombre de usuario único a partir del email
        $baseUsuario = strtolower(explode('@', $email)[0]);
        $baseUsuario = preg_replace('/[^a-z0-9_]/', '', $baseUsuario);
        $usuario     = $baseUsuario;

        // Verificar que el nombre de usuario no exista y agregar sufijo numérico si hace falta
        $intento = 0;
        while ($this->obtenerPorUsuario($usuario) !== null) {
            $intento++;
            $usuario = $baseUsuario . $intento;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $sql = "INSERT INTO usuarios (nombre, usuario, password, email, telefono, rol) 
                    VALUES (:nombre, :usuario, :password, :email, :telefono, 'cliente')";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nombre'   => $nombre,
                ':usuario'  => $usuario,
                ':password' => $hash,
                ':email'    => $email,
                ':telefono' => $telefono,
            ]);
        } catch (PDOException $e) {
            // Si por alguna razón la columna teléfono no existiera, intentar sin ella
            if (str_contains($e->getMessage(), 'telefono')) {
                $sqlFallback = "INSERT INTO usuarios (nombre, usuario, password, email, rol) 
                                VALUES (:nombre, :usuario, :password, :email, 'cliente')";
                $stmtFallback = $this->db->prepare($sqlFallback);
                return $stmtFallback->execute([
                    ':nombre'   => $nombre,
                    ':usuario'  => $usuario,
                    ':password' => $hash,
                    ':email'    => $email,
                ]);
            }
            error_log("Error en Usuario::registrar: " . $e->getMessage());
            return false;
        }
    }
}

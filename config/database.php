<?php
/**
 * Gabriel Lácteos - Configuración de Conexión a la Base de Datos
 * Utiliza PDO (PHP Data Objects) para máxima seguridad y soporte para consultas preparadas.
 */

class Database {
    private static string $host = '127.0.0.1';
    private static string $dbName = 'gabriel_lacteos';
    private static string $username = 'root';
    private static string $password = ''; // Por defecto en Laragon y XAMPP es vacío
    private static string $charset = 'utf8mb4';
    private static ?PDO $connection = null;

    /**
     * Retorna una instancia única de PDO (Patrón Singleton sencillo)
     *
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO {
        if (self::$connection === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=" . self::$charset;
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones ante errores SQL
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retornar arreglos asociativos
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Consultas preparadas reales en el motor MySQL
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"     // Asegura codificación de caracteres completa
            ];

            try {
                self::$connection = new PDO($dsn, self::$username, self::$password, $options);
            } catch (PDOException $e) {
                // Mensaje claro si la base de datos no existe o Laragon está apagado
                die("<div style='font-family:sans-serif; padding:20px; background:#ffe4e6; color:#9f1239; border-radius:8px; margin:20px; border:1px solid #f43f5e;'>
                        <h3>❌ Error de Conexión a la Base de Datos</h3>
                        <p><strong>Detalle:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
                        <p>Por favor, asegúrate de que:</p>
                        <ul>
                            <li>MySQL esté iniciado en Laragon / XAMPP.</li>
                            <li>Hayas ejecutado el archivo <code>database.sql</code> para crear la base de datos <strong>gabriel_lacteos</strong>.</li>
                            <li>El usuario y contraseña en <code>config/database.php</code> coincidan con tu configuración local.</li>
                        </ul>
                     </div>");
            }
        }

        return self::$connection;
    }
}

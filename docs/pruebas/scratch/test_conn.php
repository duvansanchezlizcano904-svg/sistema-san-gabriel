<?php
require_once __DIR__ . '/../config/database.php';
try {
    $db = Database::getConnection();
    echo "CONEXION_MYSQL_OK\n";
    $stmt = $db->query('SELECT VERSION()');
    echo "VERSION: " . $stmt->fetchColumn() . "\n";
} catch (Throwable $e) {
    echo "ERROR_CONEXION: " . $e->getMessage() . "\n";
}


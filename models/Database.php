<?php
require_once __DIR__ . '/conexion.php';
class Database {
    private static $connection = null;
    public static function getConnection() {
        if (self::$connection === null) {
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            self::$connection->set_charset("utf8");
            if (self::$connection->connect_error) {
                die("Error de conexión: " . self::$connection->connect_error);
            }
        }
        return self::$connection;
    }
}
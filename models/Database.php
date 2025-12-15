<?php
class Database {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            self::$connection->set_charset("utf8");
        }
        return self::$connection;
    }
}
?>
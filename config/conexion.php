<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'bolonet');
define('DB_USER', 'root');
define('DB_PASS', '');


class Conexion
{

    public static function conectar()
    {
        $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($con->connect_error) {
            die("Error de conexión a la base de datos: " . $con->connect_error);
        }

        $con->set_charset("utf8");

        return $con;
    }
}
?>
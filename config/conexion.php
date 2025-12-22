<?php
// 1. Definición de Constantes (Tus datos)
define('DB_HOST', 'localhost');
define('DB_NAME', 'bolonet');
define('DB_USER', 'root');
define('DB_PASS', '');

// 2. Definición de la Clase (LO QUE FALTABA)
class Conexion {
    
    public static function conectar() {
        // Intentamos conectar usando las constantes de arriba
        $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Verificamos si hubo error
        if ($con->connect_error) {
            die("Error de conexión a la base de datos: " . $con->connect_error);
        }

        // Configuramos caracteres para que salgan bien las ñ y tildes
        $con->set_charset("utf8");
        
        return $con;
    }
}
?>
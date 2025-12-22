<?php
require_once __DIR__ . '/../config/conexion.php';

class Pago {
    
    private $db;

    public function __construct() {
        // Usamos la conexión estática compatible con tu sistema
        require_once __DIR__ . '/Database.php';
        $this->db = Database::getConnection();
    }

    /**
     * Registra un nuevo pago en la base de datos
     */
    public function registrar($usuario_id, $curso_id, $transaccion_id, $monto, $metodo_pago, $estado) {
        // SQL con placeholders de MySQLi (?)
        $sql = "INSERT INTO pago (usuario_id, curso_id, transaccion_id, monto, metodo_pago, estado, fecha_pago) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt) {
            // Tipos de datos para bind_param:
            // i = integer (entero)
            // s = string (cadena)
            // d = double (decimal)
            // Orden: usuario_id(i), curso_id(i), transaccion_id(s), monto(d), metodo_pago(s), estado(s)
            $stmt->bind_param("iisdss", 
                $usuario_id, 
                $curso_id, 
                $transaccion_id, 
                $monto, 
                $metodo_pago, 
                $estado
            );

            $resultado = $stmt->execute();
            $stmt->close();
            return $resultado;
        } else {
            error_log("Error preparando insert pago: " . $this->db->error);
            return false;
        }
    }

    /**
     * Verifica si un usuario ya pagó por un curso
     */
    public function verificarPagoExistente($usuario_id, $curso_id) {
        $sql = "SELECT * FROM pago WHERE usuario_id = ? AND curso_id = ? AND estado = 'COMPLETED'";
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ii", $usuario_id, $curso_id);
            $stmt->execute();
            
            $resultado = $stmt->get_result();
            $fila = $resultado->fetch_assoc();
            
            $stmt->close();
            return $fila;
        }
        return null;
    }
}
?>
<?php
require_once __DIR__ . '/../config/conexion.php';

class Pago
{

    private $db;

    public function __construct()
    {

        require_once __DIR__ . '/Database.php';
        $this->db = Database::getConnection();
    }


    public function registrar($usuario_id, $curso_id, $transaccion_id, $monto, $metodo_pago, $estado)
    {

        $sql = "INSERT INTO pago (usuario_id, curso_id, transaccion_id, monto, metodo_pago, estado, fecha_pago) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param(
                "iisdss",
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


    public function verificarPagoExistente($usuario_id, $curso_id)
    {
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
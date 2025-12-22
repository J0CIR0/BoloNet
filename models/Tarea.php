<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/Database.php';

class Tarea
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function crear($modulo_id, $titulo, $descripcion, $fecha_entrega, $puntaje)
    {
        $sql = "INSERT INTO curso_tarea (modulo_id, titulo, descripcion, fecha_entrega, puntaje_maximo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isssi", $modulo_id, $titulo, $descripcion, $fecha_entrega, $puntaje);
        return $stmt->execute();
    }

    public function getEntregasPorTarea($tarea_id)
    {
        $sql = "SELECT e.*, u.id as usuario_id FROM curso_entrega e 
                JOIN usuario u ON e.estudiante_id = u.id 
                WHERE e.tarea_id = ?";
        // Nota: Ajustar JOIN con 'persona' si se necesita nombre del estudiante
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $tarea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getEntregaEstudiante($tarea_id, $estudiante_id)
    {
        $sql = "SELECT * FROM curso_entrega WHERE tarea_id = ? AND estudiante_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $tarea_id, $estudiante_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function entregar($tarea_id, $estudiante_id, $archivo_url, $comentario)
    {
        // Verificar si ya existe para actualizar o insertar
        $existente = $this->getEntregaEstudiante($tarea_id, $estudiante_id);

        if ($existente) {
            $sql = "UPDATE curso_entrega SET archivo_url = ?, comentario = ?, fecha_entrega = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssi", $archivo_url, $comentario, $existente['id']);
        } else {
            $sql = "INSERT INTO curso_entrega (tarea_id, estudiante_id, archivo_url, comentario) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iiss", $tarea_id, $estudiante_id, $archivo_url, $comentario);
        }
        return $stmt->execute();
    }
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM curso_tarea WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
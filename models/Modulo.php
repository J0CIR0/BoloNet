<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/Database.php';

class Modulo
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Obtener módulos de un curso con su contenido y tareas
    public function getByCurso($curso_id)
    {
        $sql = "SELECT * FROM curso_modulo WHERE curso_id = ? ORDER BY orden ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $curso_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $modulos = [];
        while ($row = $result->fetch_assoc()) {
            $row['contenidos'] = $this->getContenidos($row['id']);
            $row['tareas'] = $this->getTareas($row['id']);
            $modulos[] = $row;
        }
        return $modulos;
    }

    private function getContenidos($modulo_id)
    {
        $sql = "SELECT * FROM curso_contenido WHERE modulo_id = ? ORDER BY orden ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $modulo_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function getTareas($modulo_id)
    {
        $sql = "SELECT * FROM curso_tarea WHERE modulo_id = ? ORDER BY creado_en ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $modulo_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function crear($curso_id, $titulo, $descripcion)
    {
        $sql = "INSERT INTO curso_modulo (curso_id, titulo, descripcion) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iss", $curso_id, $titulo, $descripcion);
        return $stmt->execute();
    }
}
?>
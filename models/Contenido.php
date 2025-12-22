<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/Database.php';

class Contenido
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function crear($modulo_id, $titulo, $tipo, $url, $descripcion)
    {
        $sql = "INSERT INTO curso_contenido (modulo_id, titulo, tipo, url_recurso, descripcion) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("issss", $modulo_id, $titulo, $tipo, $url, $descripcion);
        return $stmt->execute();
    }
}
?>
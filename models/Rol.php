<?php
require_once __DIR__ . '/../config/conexion.php';

class Rol {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/Database.php';
        $this->db = Database::getConnection();
    }
    
    public function getAll() {
        $sql = "SELECT * FROM rol ORDER BY nombre";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM rol WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function create($nombre, $descripcion) {
        $sql = "INSERT INTO rol (nombre, descripcion) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $nombre, $descripcion);
        return $stmt->execute();
    }
    
    public function update($id, $nombre, $descripcion) {
        $sql = "UPDATE rol SET nombre = ?, descripcion = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $nombre, $descripcion, $id);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $sql = "DELETE FROM rol WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
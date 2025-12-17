<?php
require_once __DIR__ . '/../config/conexion.php';
class Permiso {
    private $db;
    public function __construct() {
        require_once __DIR__ . '/Database.php';
        $this->db = Database::getConnection();
    }
    public function getAll() {
        $sql = "SELECT * FROM permiso ORDER BY modulo, nombre";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getById($id) {
        $sql = "SELECT * FROM permiso WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function getByModulo($modulo) {
        $sql = "SELECT * FROM permiso WHERE modulo = ? ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $modulo);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function create($nombre, $modulo, $descripcion) {
        $sql = "INSERT INTO permiso (nombre, modulo, descripcion) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sss", $nombre, $modulo, $descripcion);
        return $stmt->execute();
    }
    public function update($id, $nombre, $modulo, $descripcion) {
        $sql = "UPDATE permiso SET nombre = ?, modulo = ?, descripcion = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $modulo, $descripcion, $id);
        return $stmt->execute();
    }
    public function delete($id) {
        $sql = "DELETE FROM permiso WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
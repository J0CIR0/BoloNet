<?php
require_once __DIR__ . '/../config/conexion.php';

class Persona {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/Database.php';
        $this->db = Database::getConnection();
    }
    
    public function getAll() {
        $sql = "SELECT * FROM persona ORDER BY apellido, nombre";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM persona WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getByCi($ci) {
        $sql = "SELECT * FROM persona WHERE ci = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $ci);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function create($data) {
        $sql = "INSERT INTO persona (ci, nombre, apellido, fecha_nacimiento, genero, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssssss", 
            $data['ci'], 
            $data['nombre'], 
            $data['apellido'], 
            $data['fecha_nacimiento'],
            $data['genero'],
            $data['telefono'],
            $data['direccion']
        );
        return $stmt->execute();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE persona SET ci = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, genero = ?, telefono = ?, direccion = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssssssi", 
            $data['ci'], 
            $data['nombre'], 
            $data['apellido'], 
            $data['fecha_nacimiento'],
            $data['genero'],
            $data['telefono'],
            $data['direccion'],
            $id
        );
        return $stmt->execute();
    }
    
    public function delete($id) {
        $sql = "DELETE FROM persona WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function buscar($termino) {
        $sql = "SELECT * FROM persona WHERE ci LIKE ? OR nombre LIKE ? OR apellido LIKE ? ORDER BY apellido, nombre";
        $stmt = $this->db->prepare($sql);
        $termino_like = "%$termino%";
        $stmt->bind_param("sss", $termino_like, $termino_like, $termino_like);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
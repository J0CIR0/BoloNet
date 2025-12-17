<?php
require_once __DIR__ . '/../config/conexion.php';

class Curso {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/Database.php';
        $this->db = Database::getConnection();
    }
    
    public function getAll() {
        $sql = "SELECT c.*, CONCAT(p.nombre, ' ', p.apellido) as profesor_nombre 
                FROM curso c 
                LEFT JOIN usuario u ON c.profesor_id = u.id 
                LEFT JOIN persona p ON u.persona_id = p.id 
                ORDER BY c.nombre";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $sql = "SELECT c.*, CONCAT(p.nombre, ' ', p.apellido) as profesor_nombre 
                FROM curso c 
                LEFT JOIN usuario u ON c.profesor_id = u.id 
                LEFT JOIN persona p ON u.persona_id = p.id 
                WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getByCodigo($codigo) {
        $sql = "SELECT * FROM curso WHERE codigo = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function create($data) {
        $sql = "INSERT INTO curso (codigo, nombre, descripcion, duracion_horas, fecha_inicio, fecha_fin, profesor_id, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $profesor_id = !empty($data['profesor_id']) ? $data['profesor_id'] : NULL;
        $stmt->bind_param("sssiissi", 
            $data['codigo'], 
            $data['nombre'], 
            $data['descripcion'],
            $data['duracion_horas'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $profesor_id,
            $data['estado']
        );
        return $stmt->execute();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE curso SET codigo = ?, nombre = ?, descripcion = ?, duracion_horas = ?, fecha_inicio = ?, fecha_fin = ?, profesor_id = ?, estado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $profesor_id = !empty($data['profesor_id']) ? $data['profesor_id'] : NULL;
        $stmt->bind_param("sssiissii", 
            $data['codigo'], 
            $data['nombre'], 
            $data['descripcion'],
            $data['duracion_horas'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $profesor_id,
            $data['estado'],
            $id
        );
        return $stmt->execute();
    }
    
    public function delete($id) {
        $sql = "DELETE FROM curso WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getCursosActivos() {
        $sql = "SELECT c.*, CONCAT(p.nombre, ' ', p.apellido) as profesor_nombre 
                FROM curso c 
                LEFT JOIN usuario u ON c.profesor_id = u.id 
                LEFT JOIN persona p ON u.persona_id = p.id 
                WHERE c.estado = 'activo' 
                ORDER BY c.nombre";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function inscribirEstudiante($estudiante_id, $curso_id) {
        $sql = "INSERT INTO inscripcion (estudiante_id, curso_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $estudiante_id, $curso_id);
        return $stmt->execute();
    }
    
    public function getInscripcionesByEstudiante($estudiante_id) {
        $sql = "SELECT i.*, c.codigo, c.nombre as curso_nombre, c.estado as curso_estado 
                FROM inscripcion i 
                JOIN curso c ON i.curso_id = c.id 
                WHERE i.estudiante_id = ? 
                ORDER BY i.fecha_inscripcion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $estudiante_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getInscripcionesByCurso($curso_id) {
        $sql = "SELECT i.*, CONCAT(p.nombre, ' ', p.apellido) as estudiante_nombre, u.email 
                FROM inscripcion i 
                JOIN usuario u ON i.estudiante_id = u.id 
                JOIN persona p ON u.persona_id = p.id 
                WHERE i.curso_id = ? 
                ORDER BY p.apellido, p.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $curso_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function actualizarInscripcion($inscripcion_id, $estado, $nota_final = null) {
        $sql = "UPDATE inscripcion SET estado = ?, nota_final = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sdi", $estado, $nota_final, $inscripcion_id);
        return $stmt->execute();
    }
}
?>
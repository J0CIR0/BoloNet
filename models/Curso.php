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
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    public function getByCodigo($codigo) {
        $sql = "SELECT * FROM curso WHERE codigo = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function create($data) {
        foreach ($data as $key => $value) {
            error_log("$key: " . ($value === null ? 'NULL' : $value));
        }
        $codigo = isset($data['codigo']) ? trim($data['codigo']) : '';
        $nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
        $descripcion = isset($data['descripcion']) ? trim($data['descripcion']) : '';
        $duracion_horas = isset($data['duracion_horas']) ? intval($data['duracion_horas']) : 0;
        $fecha_inicio = isset($data['fecha_inicio']) ? trim($data['fecha_inicio']) : '';
        $fecha_fin = isset($data['fecha_fin']) ? trim($data['fecha_fin']) : '';
        $profesor_id = isset($data['profesor_id']) && $data['profesor_id'] !== '' && $data['profesor_id'] !== null ? intval($data['profesor_id']) : null;
        $estado = isset($data['estado']) && !empty($data['estado']) ? $data['estado'] : 'activo';
        error_log("=== VALORES PROCESADOS ===");
        error_log("codigo: $codigo");
        error_log("nombre: $nombre");
        error_log("fecha_inicio: $fecha_inicio");
        error_log("fecha_fin: $fecha_fin");
        error_log("estado: $estado");
        error_log("profesor_id: " . ($profesor_id === null ? 'NULL' : $profesor_id));
        if (empty($fecha_inicio) || $fecha_inicio == '0000-00-00') {
            $fecha_inicio = date('Y-m-d');
            error_log("Fecha inicio corregida a: $fecha_inicio");
        }
        if (empty($fecha_fin) || $fecha_fin == '0000-00-00') {
            $fecha_fin = date('Y-m-d', strtotime('+1 month'));
            error_log("Fecha fin corregida a: $fecha_fin");
        }
        if (empty($estado)) {
            $estado = 'activo';
            error_log("Estado corregido a: $estado");
        }
        $sql_directo = "INSERT INTO curso (codigo, nombre, descripcion, duracion_horas, fecha_inicio, fecha_fin, estado" . ($profesor_id !== null ? ", profesor_id" : "") . ") VALUES (";
        $sql_directo .= "'" . $this->db->real_escape_string($codigo) . "', ";
        $sql_directo .= "'" . $this->db->real_escape_string($nombre) . "', ";
        $sql_directo .= "'" . $this->db->real_escape_string($descripcion) . "', ";
        $sql_directo .= $duracion_horas . ", ";
        $sql_directo .= "'" . $this->db->real_escape_string($fecha_inicio) . "', ";
        $sql_directo .= "'" . $this->db->real_escape_string($fecha_fin) . "', ";
        $sql_directo .= "'" . $this->db->real_escape_string($estado) . "'";
        if ($profesor_id !== null) {
            $sql_directo .= ", " . $profesor_id;
        }
        $sql_directo .= ")";
        error_log("SQL DIRECTO: $sql_directo");
        $result_directo = $this->db->query($sql_directo);
        if ($result_directo) {
            error_log("✓ Insert directo exitoso");
            return true;
        } else {
            error_log("✗ Error en insert directo: " . $this->db->error);
            if ($profesor_id !== null) {
                $sql = "INSERT INTO curso (codigo, nombre, descripcion, duracion_horas, fecha_inicio, fecha_fin, profesor_id, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("sssiissi", 
                        $codigo, 
                        $nombre, 
                        $descripcion,
                        $duracion_horas,
                        $fecha_inicio,
                        $fecha_fin,
                        $profesor_id,
                        $estado
                    );
                    $result = $stmt->execute();
                    $stmt->close();
                    return $result;
                }
            } else {
                $sql = "INSERT INTO curso (codigo, nombre, descripcion, duracion_horas, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("sssiiss", 
                        $codigo, 
                        $nombre, 
                        $descripcion,
                        $duracion_horas,
                        $fecha_inicio,
                        $fecha_fin,
                        $estado
                    );
                    $result = $stmt->execute();
                    $stmt->close();
                    return $result;
                }
            }
            return false;
        }
    }
    public function update($id, $data) {
        $codigo = isset($data['codigo']) ? trim($data['codigo']) : '';
        $nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
        $descripcion = isset($data['descripcion']) ? trim($data['descripcion']) : '';
        $duracion_horas = isset($data['duracion_horas']) ? intval($data['duracion_horas']) : 0;
        $fecha_inicio = isset($data['fecha_inicio']) ? trim($data['fecha_inicio']) : '';
        $fecha_fin = isset($data['fecha_fin']) ? trim($data['fecha_fin']) : '';
        $profesor_id = isset($data['profesor_id']) && $data['profesor_id'] !== '' && $data['profesor_id'] !== null ? intval($data['profesor_id']) : null;
        $estado = isset($data['estado']) && !empty($data['estado']) ? $data['estado'] : 'activo';
        if (empty($fecha_inicio) || $fecha_inicio == '0000-00-00') {
            $fecha_inicio = date('Y-m-d');
        }
        if (empty($fecha_fin) || $fecha_fin == '0000-00-00') {
            $fecha_fin = date('Y-m-d', strtotime('+1 month'));
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio)) {
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecha_inicio, $matches)) {
                $fecha_inicio = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            } else {
                $fecha_inicio = date('Y-m-d');
            }
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecha_fin, $matches)) {
                $fecha_fin = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            } else {
                $fecha_fin = date('Y-m-d', strtotime('+1 month'));
            }
        }
        if (empty($estado)) {
            $estado = 'activo';
        }
        $sql = "UPDATE curso SET ";
        $sql .= "codigo = '" . $this->db->real_escape_string($codigo) . "', ";
        $sql .= "nombre = '" . $this->db->real_escape_string($nombre) . "', ";
        $sql .= "descripcion = '" . $this->db->real_escape_string($descripcion) . "', ";
        $sql .= "duracion_horas = " . intval($duracion_horas) . ", ";
        $sql .= "fecha_inicio = '" . $this->db->real_escape_string($fecha_inicio) . "', ";
        $sql .= "fecha_fin = '" . $this->db->real_escape_string($fecha_fin) . "', ";
        $sql .= "estado = '" . $this->db->real_escape_string($estado) . "'";
        if ($profesor_id !== null) {
            $sql .= ", profesor_id = " . intval($profesor_id);
        } else {
            $sql .= ", profesor_id = NULL";
        }
        $sql .= " WHERE id = " . intval($id);
        error_log("SQL UPDATE CURSO: " . $sql);
        $result = $this->db->query($sql);
        if (!$result) {
            error_log("ERROR UPDATE CURSO: " . $this->db->error);
        }
        return $result;
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
        $sql_check = "SELECT id FROM inscripcion WHERE estudiante_id = ? AND curso_id = ?";
        $stmt_check = $this->db->prepare($sql_check);
        $stmt_check->bind_param("ii", $estudiante_id, $curso_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        if ($result->num_rows > 0) {
            return false;
        }
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
    public function getCursosInscritos($estudiante_id) {
        $sql = "SELECT c.* FROM curso c 
                JOIN inscripcion i ON c.id = i.curso_id 
                WHERE i.estudiante_id = ? AND c.estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $estudiante_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
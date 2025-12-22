<?php
// Usamos una ruta absoluta para encontrar el archivo de configuración sin errores
$rutaConexion = __DIR__ . '/../config/conexion.php';

if (file_exists($rutaConexion)) {
    require_once $rutaConexion;
} else {
    die("Error Crítico: No se encuentra el archivo de conexión en: " . $rutaConexion);
}

class Inscripcion
{

    private $db;

    public function __construct()
    {
        // Verificamos si la clase existe antes de usarla
        if (class_exists('Conexion')) {
            $this->db = Conexion::conectar();
        } else {
            die("Error: El archivo conexion.php se cargó, pero no contiene la clase 'Conexion'.");
        }
    }

    // Registrar inscripción (Pago exitoso)
    public function registrar($usuario_id, $curso_id)
    {
        if ($this->verificarInscripcion($usuario_id, $curso_id)) {
            return false;
        }

        $sql = "INSERT INTO inscripcion (estudiante_id, curso_id, estado, fecha_inscripcion) 
                VALUES (?, ?, 'inscrito', NOW())";

        $stmt = $this->db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ii", $usuario_id, $curso_id);
            $resultado = $stmt->execute();
            $stmt->close();
            return $resultado;
        } else {
            return false;
        }
    }

    // Verificar si ya existe inscripción (True/False)
    public function verificarInscripcion($usuario_id, $curso_id)
    {
        $sql = "SELECT id FROM inscripcion WHERE estudiante_id = ? AND curso_id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $usuario_id, $curso_id);
            $stmt->execute();
            $stmt->store_result();
            $existe = $stmt->num_rows > 0;
            $stmt->close();
            return $existe;
        }
        return false;
    }

    // Obtener detalles completos para la vista "Mis Cursos"
    public function obtenerCursosPorEstudiante($usuario_id)
    {
        $sql = "SELECT c.*, i.fecha_inscripcion, i.estado as estado_inscripcion, i.nota_final
                FROM inscripcion i
                INNER JOIN curso c ON i.curso_id = c.id
                WHERE i.estudiante_id = ?
                ORDER BY i.fecha_inscripcion DESC";

        $stmt = $this->db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $resultado = $stmt->get_result();

            $mis_cursos = [];
            while ($row = $resultado->fetch_assoc()) {
                $mis_cursos[] = $row;
            }
            $stmt->close();
            return $mis_cursos;
        } else {
            return [];
        }
    }

    // --- NUEVA FUNCIÓN NECESARIA ---
    // Esta función devuelve solo los IDs de los cursos (ej: [1, 5])
    // Sirve para que el Catálogo sepa qué botones deshabilitar
    public function obtenerIdsInscritos($usuario_id)
    {
        $sql = "SELECT curso_id FROM inscripcion WHERE estudiante_id = ? AND estado IN ('inscrito', 'aprobado')";
        $stmt = $this->db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $resultado = $stmt->get_result();

            $ids = [];
            while ($row = $resultado->fetch_assoc()) {
                $ids[] = $row['curso_id'];
            }
            $stmt->close();
            return $ids;
        }
        return [];
    }

    // Aprobar estudiante manualmente
    public function aprobarEstudiante($curso_id, $estudiante_id, $nota_final)
    {
        $sql = "UPDATE inscripcion SET estado = 'aprobado', nota_final = ? WHERE curso_id = ? AND estudiante_id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("dii", $nota_final, $curso_id, $estudiante_id);
            return $stmt->execute();
        }
        return false;
    }

    public function obtenerInscritosPorCurso($curso_id)
    {
        $sql = "SELECT i.*, u.id as usuario_id, p.nombre, p.apellido, p.ci, u.email 
                FROM inscripcion i 
                JOIN usuario u ON i.estudiante_id = u.id 
                JOIN persona p ON u.persona_id = p.id 
                WHERE i.curso_id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $curso_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}
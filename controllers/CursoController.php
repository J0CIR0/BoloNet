<?php
class CursoController {
    private $curso;
    private $usuario;
    public function __construct() {
        require_once __DIR__ . '/../models/Curso.php';
        require_once __DIR__ . '/../models/Usuario.php';
        $this->curso = new Curso();
        $this->usuario = new Usuario();
    }
    public function checkPermission($permiso) {
        if (!isset($_SESSION['user_id']) || !$this->usuario->hasPermission($_SESSION['user_id'], $permiso)) {
            $_SESSION['error'] = 'No tienes permisos para esta acción';
            header('Location: dashboard.php');
            exit();
        }
    }
    public function index() {
        $this->checkPermission('ver_cursos');
        $cursos = $this->curso->getAll();
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cursos/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function create() {
    $this->checkPermission('crear_curso');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $codigo = trim($_POST['codigo']);
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $duracion_horas = intval($_POST['duracion_horas']);
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $profesor_id = !empty($_POST['profesor_id']) ? intval($_POST['profesor_id']) : null;
        $estado = $_POST['estado'] ?? 'activo';
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio)) {
            $_SESSION['error'] = 'Formato de fecha de inicio inválido. Use YYYY-MM-DD';
            header('Location: cursos.php?action=create');
            exit();
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
            $_SESSION['error'] = 'Formato de fecha de fin inválido. Use YYYY-MM-DD';
            header('Location: cursos.php?action=create');
            exit();
        }
        if (empty($codigo) || empty($nombre) || empty($fecha_inicio) || empty($fecha_fin)) {
            $_SESSION['error'] = 'Todos los campos obligatorios deben estar completos';
            header('Location: cursos.php?action=create');
            exit();
        }
        if ($duracion_horas <= 0) {
            $_SESSION['error'] = 'La duración debe ser mayor a 0';
            header('Location: cursos.php?action=create');
            exit();
        }
        if ($fecha_inicio >= $fecha_fin) {
            $_SESSION['error'] = 'La fecha de inicio debe ser anterior a la fecha de fin';
            header('Location: cursos.php?action=create');
            exit();
        }
        $existente = $this->curso->getByCodigo($codigo);
        if ($existente) {
            $_SESSION['error'] = 'El código del curso ya existe';
            header('Location: cursos.php?action=create');
            exit();
        }
        $data = [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'duracion_horas' => $duracion_horas,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'profesor_id' => $profesor_id,
            'estado' => $estado
        ];
        if ($this->curso->create($data)) {
            $_SESSION['success'] = 'Curso creado exitosamente';
            header('Location: cursos.php');
            exit();
        } else {
            $_SESSION['error'] = 'Error al crear curso';
            header('Location: cursos.php?action=create');
            exit();
        }
    }
    $profesores = $this->usuario->getProfesores();
    $title = 'Nuevo Curso';
    require_once __DIR__ . '/../views/layouts/header.php';
    require_once __DIR__ . '/../views/cursos/create.php';
    require_once __DIR__ . '/../views/layouts/footer.php';
}
    public function edit($id) {
        $this->checkPermission('editar_curso');
        $curso = $this->curso->getById($id);
        if (!$curso) {
            $_SESSION['error'] = 'Curso no encontrado';
            header('Location: cursos.php');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            error_log("Datos POST recibidos:");
            error_log("codigo: " . $_POST['codigo']);
            error_log("nombre: " . $_POST['nombre']);
            error_log("fecha_inicio: " . $_POST['fecha_inicio']);
            error_log("fecha_fin: " . $_POST['fecha_fin']);
            error_log("estado: " . ($_POST['estado'] ?? 'activo'));
            $codigo = trim($_POST['codigo']);
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $duracion_horas = intval($_POST['duracion_horas']);
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];
            $profesor_id = !empty($_POST['profesor_id']) ? intval($_POST['profesor_id']) : null;
            $estado = $_POST['estado'] ?? 'activo';
            error_log("fecha_inicio procesada: $fecha_inicio");
            error_log("fecha_fin procesada: $fecha_fin");
            if (empty($codigo) || empty($nombre) || empty($fecha_inicio) || empty($fecha_fin)) {
                $_SESSION['error'] = 'Todos los campos obligatorios deben estar completos';
                header("Location: cursos.php?action=edit&id=$id");
                exit();
            }
            if ($duracion_horas <= 0) {
                $_SESSION['error'] = 'La duración debe ser mayor a 0';
                header("Location: cursos.php?action=edit&id=$id");
                exit();
            }
            if ($fecha_inicio >= $fecha_fin) {
                $_SESSION['error'] = 'La fecha de inicio debe ser anterior a la fecha de fin';
                header("Location: cursos.php?action=edit&id=$id");
                exit();
            }
            if ($curso['codigo'] != $codigo) {
                $existente = $this->curso->getByCodigo($codigo);
                if ($existente) {
                    $_SESSION['error'] = 'El código del curso ya existe';
                    header("Location: cursos.php?action=edit&id=$id");
                    exit();
                }
            }
            $data = [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'duracion_horas' => $duracion_horas,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'profesor_id' => $profesor_id,
                'estado' => $estado
            ];
            error_log("Datos para update:");
            error_log(print_r($data, true));
            if ($this->curso->update($id, $data)) {
                $_SESSION['success'] = 'Curso actualizado exitosamente';
                header('Location: cursos.php');
                exit();
            } else {
                $_SESSION['error'] = 'Error al actualizar curso';
                header("Location: cursos.php?action=edit&id=$id");
                exit();
            }
        }
        $profesores = $this->usuario->getProfesores();
        $title = 'Editar Curso';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cursos/edit.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function delete($id) {
        $this->checkPermission('eliminar_curso');
        $curso = $this->curso->getById($id);
        if (!$curso) {
            $_SESSION['error'] = 'Curso no encontrado';
            header('Location: cursos.php');
            exit();
        }
        if ($this->curso->delete($id)) {
            $_SESSION['success'] = 'Curso eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar curso';
        }
        header('Location: cursos.php');
        exit();
    }
    public function inscribir() {
        $this->checkPermission('inscribir_curso');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['curso_id'])) {
            $curso_id = $_POST['curso_id'];
            $estudiante_id = $_SESSION['user_id'];
            if ($this->curso->inscribirEstudiante($estudiante_id, $curso_id)) {
                $_SESSION['success'] = 'Inscripción realizada exitosamente';
            } else {
                $_SESSION['error'] = 'Ya estás inscrito en este curso';
            }
            header('Location: cursos.php');
            exit();
        }
        $cursos_activos = $this->curso->getCursosActivos();
        $title = 'Inscribirse en Curso';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cursos/inscribir.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function misCursos() {
        $this->checkPermission('ver_inscripciones');
        $estudiante_id = $_SESSION['user_id'];
        $inscripciones = $this->curso->getInscripcionesByEstudiante($estudiante_id);
        $title = 'Mis Cursos';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cursos/mis_cursos.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function gestionarInscripciones($curso_id) {
        $this->checkPermission('gestionar_inscripciones');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $inscripcion_id = $_POST['inscripcion_id'];
            $estado = $_POST['estado'];
            $nota_final = $_POST['nota_final'] ?? null;
            $this->curso->actualizarInscripcion($inscripcion_id, $estado, $nota_final);
            $_SESSION['success'] = 'Inscripción actualizada';
            header("Location: cursos.php?action=gestionar&id=$curso_id");
            exit();
        }
        $curso = $this->curso->getById($curso_id);
        $inscripciones = $this->curso->getInscripcionesByCurso($curso_id);
        $title = 'Gestionar Inscripciones: ' . $curso['nombre'];
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cursos/gestionar_inscripciones.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
?>
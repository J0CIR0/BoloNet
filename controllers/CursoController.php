<?php
require_once __DIR__ . '/../config/conexion.php';

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
            $data = [
                'codigo' => $_POST['codigo'],
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'duracion_horas' => $_POST['duracion_horas'],
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin' => $_POST['fecha_fin'],
                'profesor_id' => $_POST['profesor_id'] ?? null,
                'estado' => $_POST['estado'] ?? 'activo'
            ];
            
            $existente = $this->curso->getByCodigo($data['codigo']);
            if ($existente) {
                $_SESSION['error'] = 'El código del curso ya existe';
                header('Location: cursos.php?action=create');
                exit();
            }
            
            if ($this->curso->create($data)) {
                $_SESSION['success'] = 'Curso creado exitosamente';
                header('Location: cursos.php');
                exit();
            } else {
                $_SESSION['error'] = 'Error al crear curso';
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
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'codigo' => $_POST['codigo'],
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'duracion_horas' => $_POST['duracion_horas'],
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin' => $_POST['fecha_fin'],
                'profesor_id' => $_POST['profesor_id'] ?? null,
                'estado' => $_POST['estado'] ?? 'activo'
            ];
            
            $curso_actual = $this->curso->getById($id);
            if ($curso_actual['codigo'] != $data['codigo']) {
                $existente = $this->curso->getByCodigo($data['codigo']);
                if ($existente) {
                    $_SESSION['error'] = 'El código del curso ya existe';
                    header("Location: cursos.php?action=edit&id=$id");
                    exit();
                }
            }
            
            if ($this->curso->update($id, $data)) {
                $_SESSION['success'] = 'Curso actualizado exitosamente';
                header('Location: cursos.php');
                exit();
            } else {
                $_SESSION['error'] = 'Error al actualizar curso';
            }
        }
        
        $curso = $this->curso->getById($id);
        if (!$curso) {
            $_SESSION['error'] = 'Curso no encontrado';
            header('Location: cursos.php');
            exit();
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
                $_SESSION['error'] = 'Error al realizar la inscripción';
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
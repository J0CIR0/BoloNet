<?php
class CursoController
{
    private $curso;
    private $usuario;
    private $inscripcion;

    public function __construct()
    {
        require_once __DIR__ . '/../models/Curso.php';
        require_once __DIR__ . '/../models/Usuario.php';
        require_once __DIR__ . '/../models/Inscripcion.php';

        $this->curso = new Curso();
        $this->usuario = new Usuario();
        $this->inscripcion = new Inscripcion();
    }

    public function checkPermission($permiso)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Validar Sesión Concurrente (Si fue invalidada por otro login)
        require_once __DIR__ . '/../models/UserSession.php';
        $sessionModel = new UserSession();
        if (isset($_SESSION['user_id']) && !$sessionModel->isValid(session_id())) {
            session_destroy();
            header('Location: index.php?error=sesion_invalida');
            exit();
        }

        if (!isset($_SESSION['user_id']) || !$this->usuario->hasPermission($_SESSION['user_id'], $permiso)) {
            $_SESSION['error'] = 'No tienes permisos para esta acción';
            header('Location: dashboard.php');
            exit();
        }
    }

    public function index()
    {
        $this->checkPermission('ver_cursos');

        $cursos = $this->curso->getAll();

        $cursos = $this->curso->getAll();

        $cursos_inscritos = [];
        $isSubscribed = false;

        // Lógica Suscripción: Si está activo, tiene acceso a TODO
        if (isset($_SESSION['user_id'])) {
            $isSubscribed = isset($_SESSION['subscription_status']) && $_SESSION['subscription_status'] === 'active';

            if ($isSubscribed) {
                // Si es suscriptor, simulamos que está inscrito en TODOS los cursos para desbloquear el UI
                $cursos_inscritos = array_column($cursos, 'id');
            } else {
                // Si no, mantenemos lógica antigua (solo ve lo que compró antes o nada)
                if (method_exists($this->inscripcion, 'obtenerIdsInscritos')) {
                    $cursos_inscritos = $this->inscripcion->obtenerIdsInscritos($_SESSION['user_id']);
                }
            }
        }

        $title = 'Lista de Cursos';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cursos/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function create()
    {
        $this->checkPermission('crear_curso');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $codigo = trim($_POST['codigo']);
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion'] ?? '');

            $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0.00;
            $duracion_horas = intval($_POST['duracion_horas']);
            $fecha_inicio = trim($_POST['fecha_inicio']);
            $fecha_fin = trim($_POST['fecha_fin']);
            $profesor_id = isset($_POST['profesor_id']) && $_POST['profesor_id'] !== '' ? intval($_POST['profesor_id']) : null;
            $estado = $_POST['estado'] ?? 'activo';

            // Validaciones básicas
            $dateRegex = '/^\d{4}-\d{2}-\d{2}$/';
            if (!preg_match($dateRegex, $fecha_inicio) || !preg_match($dateRegex, $fecha_fin)) {
                $_SESSION['error'] = 'Formato de fechas incorrecto';
                header('Location: index.php?controller=Curso&action=create');
                exit();
            }
            if ($duracion_horas <= 0 || $precio < 0) {
                $_SESSION['error'] = 'Datos numéricos inválidos';
                header('Location: index.php?controller=Curso&action=create');
                exit();
            }
            if ($fecha_inicio >= $fecha_fin) {
                $_SESSION['error'] = 'Fechas inválidas';
                header('Location: index.php?controller=Curso&action=create');
                exit();
            }

            if ($this->curso->getByCodigo($codigo)) {
                $_SESSION['error'] = 'El código ya existe';
                header('Location: index.php?controller=Curso&action=create');
                exit();
            }

            $data = [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'duracion_horas' => $duracion_horas,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'profesor_id' => $profesor_id,
                'estado' => $estado
            ];

            if ($this->curso->create($data)) {
                $_SESSION['success'] = 'Curso creado exitosamente';
                header('Location: index.php?controller=Curso&action=index');
                exit();
            } else {
                $_SESSION['error'] = 'Error al crear curso';
                header('Location: index.php?controller=Curso&action=create');
                exit();
            }
        }

        $profesores = $this->usuario->getProfesores();
        $title = 'Nuevo Curso';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cursos/create.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    // --- FUNCIÓN EDITAR CORREGIDA (Argumento Opcional) ---
    public function edit($id = null)
    {
        // Si no llega por parámetro, lo buscamos en GET
        if ($id === null && isset($_GET['id'])) {
            $id = $_GET['id'];
        }

        if (!$id) {
            $_SESSION['error'] = 'ID de curso no especificado';
            header('Location: index.php?controller=Curso&action=index');
            exit();
        }

        $this->checkPermission('editar_curso');
        $curso = $this->curso->getById($id);

        if (!$curso) {
            $_SESSION['error'] = 'Curso no encontrado';
            header('Location: index.php?controller=Curso&action=index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $codigo = trim($_POST['codigo']);
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion'] ?? '');

            $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0.00;
            $duracion_horas = intval($_POST['duracion_horas']);
            $fecha_inicio = trim($_POST['fecha_inicio']);
            $fecha_fin = trim($_POST['fecha_fin']);
            $profesor_id = isset($_POST['profesor_id']) && $_POST['profesor_id'] !== '' ? intval($_POST['profesor_id']) : null;
            $estado = $_POST['estado'] ?? 'activo';

            if ($duracion_horas <= 0 || $precio < 0) {
                $_SESSION['error'] = 'Datos numéricos inválidos';
                header("Location: index.php?controller=Curso&action=edit&id=$id");
                exit();
            }

            if ($curso['codigo'] != $codigo) {
                if ($this->curso->getByCodigo($codigo)) {
                    $_SESSION['error'] = 'El código ya existe';
                    header("Location: index.php?controller=Curso&action=edit&id=$id");
                    exit();
                }
            }

            $data = [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'duracion_horas' => $duracion_horas,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'profesor_id' => $profesor_id,
                'estado' => $estado
            ];

            if ($this->curso->update($id, $data)) {
                $_SESSION['success'] = 'Curso actualizado exitosamente';
                header('Location: index.php?controller=Curso&action=index');
                exit();
            } else {
                $_SESSION['error'] = 'Error al actualizar curso';
                header("Location: index.php?controller=Curso&action=edit&id=$id");
                exit();
            }
        }

        $profesores = $this->usuario->getProfesores();
        $title = 'Editar Curso';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cursos/edit.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    // --- FUNCIÓN ELIMINAR CORREGIDA (Argumento Opcional) ---
    public function delete($id = null)
    {
        if ($id === null && isset($_GET['id'])) {
            $id = $_GET['id'];
        }

        $this->checkPermission('eliminar_curso');

        if ($id && $this->curso->delete($id)) {
            $_SESSION['success'] = 'Curso eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar curso';
        }
        header('Location: index.php?controller=Curso&action=index');
        exit();
    }

    public function mis_cursos()
    {
        $this->checkPermission('ver_inscripciones');

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $estudiante_id = $_SESSION['user_id'];

        // --- LÓGICA DE SINCRONIZACIÓN SUSCRIPCIÓN ---
        $isSubscribed = isset($_SESSION['subscription_status']) && $_SESSION['subscription_status'] === 'active';

        if ($isSubscribed) {
            // Si tiene suscripción, ve TODOS los cursos como si estuviera inscrito
            $todosLosCursos = $this->curso->getAll();
            $inscripciones = [];

            // Transformamos formato curso -> inscripcion para la vista
            foreach ($todosLosCursos as $c) {
                $inscripciones[] = [
                    'id' => $c['id'], // ID para seed de imagen
                    'nombre' => $c['nombre'],
                    'codigo' => $c['codigo'],
                    'descripcion' => $c['descripcion'],
                    'estado_inscripcion' => 'inscrito', // Por defecto
                    'nota_final' => 0,
                    'fecha_inscripcion' => $c['fecha_inicio'] // Usamos inicio curso como fecha ref
                ];
            }
        } else {
            // Si no es suscriptor, ve solo lo que compró individualmente
            $inscripciones = $this->inscripcion->obtenerCursosPorEstudiante($estudiante_id);
        }

        $title = 'Mis Cursos';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cursos/mis_cursos.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    // --- FUNCIÓN GESTIONAR INSCRIPCIONES CORREGIDA ---
    public function gestionarInscripciones($curso_id = null)
    {
        if ($curso_id === null && isset($_GET['id'])) {
            $curso_id = $_GET['id'];
        }

        $this->checkPermission('gestionar_inscripciones');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['inscripcion_id'])) {
                $this->curso->actualizarInscripcion(
                    $_POST['inscripcion_id'],
                    $_POST['estado'],
                    $_POST['nota_final'] ?? null
                );
                $_SESSION['success'] = 'Inscripción actualizada';
            }
        }

        // Si después de todo no hay ID, volvemos
        if (!$curso_id) {
            header('Location: index.php?controller=Curso&action=index');
            exit();
        }

        $curso = $this->curso->getById($curso_id);
        $inscripciones = $this->curso->getInscripcionesByCurso($curso_id);

        $title = 'Gestionar Inscripciones: ' . ($curso['nombre'] ?? '');
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/cursos/gestionar_inscripciones.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
?>
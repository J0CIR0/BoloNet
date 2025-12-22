<?php
require_once __DIR__ . '/../models/Modulo.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Tarea.php';
// require_once __DIR__ . '/../models/Contenido.php'; // Lo cargaremos on-demand

class AulaController
{
    private $moduloModel;
    private $cursoModel;
    private $usuarioModel;
    private $tareaModel;

    public function __construct()
    {
        require_once __DIR__ . '/../models/Database.php';
        $this->db = Database::getConnection();
        $this->moduloModel = new Modulo();
        $this->cursoModel = new Curso();
        $this->usuarioModel = new Usuario();
        $this->tareaModel = new Tarea();
    }

    // VISTA PRINCIPAL DEL AULA
    public function index()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=Curso&action=mis_cursos');
            exit();
        }

        $id_curso = (int) $_GET['id'];
        $user_id = $_SESSION['user_id'] ?? 0;

        // 1. Validar Acceso (Suscripción o Inscripción)
        $user_role = $_SESSION['user_role'] ?? '';
        $esProfesor = ($user_role === 'profesor') || $this->usuarioModel->hasPermission($user_id, 'crear_curso');

        if (!$esProfesor) {
            require_once __DIR__ . '/../models/Inscripcion.php';
            $inscripcionCheck = new Inscripcion();
            $yaInscrito = $inscripcionCheck->verificarInscripcion($user_id, $id_curso);
            $isSubscribed = isset($_SESSION['subscription_status']) && $_SESSION['subscription_status'] === 'active';

            if (!$yaInscrito && !$isSubscribed) {
                $_SESSION['error'] = 'Debes tener una suscripción activa para acceder al contenido del curso.';
                header('Location: index.php?controller=Pago&action=planes');
                exit();
            }
        }

        $curso = $this->cursoModel->obtenerPorId($id_curso); // Devuelve objeto u array
        if (!$curso) {
            die("Curso no encontrado");
        }
        // Convertir a array si es objeto para consistencia
        $cursoData = is_object($curso) ? (array) $curso : $curso;

        // 2. Cargar contenido del aula
        $modulos = $this->moduloModel->getByCurso($id_curso);

        // 3. Determinar rol en el contexto del aula (Ya calculado arriba)

        // --- LÓGICA DE AUDITORÍA vs ESTUDIANTE (Suscripciones) ---
        $gradingEnabled = false; // Por defecto desactivado
        $auditorMessage = "";

        if ($esProfesor) {
            $gradingEnabled = true; // Profesores siempre pueden "calificar/editar"
        } else {
            // Verificar si YA está inscrito realmente
            require_once __DIR__ . '/../models/Inscripcion.php';
            $inscripcionModel = new Inscripcion();
            $estaInscrito = $inscripcionModel->verificarInscripcion($user_id, $id_curso);

            // Variables para certificado
            $isAprobado = false;
            $datosInscripcion = null;

            if ($estaInscrito) {
                // Obtenemos los datos completos para saber si está aprobado
                // Usamos una función ad-hoc o reutilizamos obtenerCursosPorEstudiante filtrado
                // Por eficiencia, crearemos un método rápido en Inscripcion o hacemos query directa aquí
                // O mejor, obtengamos todos y filtremos (no es lo más óptimo pero funciona rápido con pocos cursos)
                $misCursos = $inscripcionModel->obtenerCursosPorEstudiante($user_id);
                foreach ($misCursos as $mc) {
                    if ($mc['id'] == $id_curso) {
                        $datosInscripcion = $mc;
                        break;
                    }
                }

                if ($datosInscripcion && $datosInscripcion['estado_inscripcion'] === 'aprobado') {
                    $isAprobado = true;
                }

                // Si ya está inscrito en BD, es alumno regular
                $gradingEnabled = true;
            } else {
                // Es Suscriptor entrando por primera vez o revisitando sin inscripción
                $isSubscribed = isset($_SESSION['subscription_status']) && $_SESSION['subscription_status'] === 'active';

                if ($isSubscribed) {
                    // Calcular progreso del curso
                    // Suponiendo que $cursoData tiene 'fecha_inicio' y 'fecha_fin'
                    $fechaInicio = strtotime($cursoData['fecha_inicio']);
                    $fechaFin = strtotime($cursoData['fecha_fin']);
                    $hoy = time();

                    if ($fechaFin > $fechaInicio) {
                        $duracionTotal = $fechaFin - $fechaInicio;
                        $tiempoTranscurrido = $hoy - $fechaInicio;
                        $progresoTemporal = $tiempoTranscurrido / $duracionTotal;
                    } else {
                        $progresoTemporal = 0; // Evitar división por cero
                    }

                    // REGLA: Si va por menos de la mitad (0.5), se inscribe automáticamente.
                    // Si va por más de la mitad, es OYENTE (Auditor)
                    // REGLA: Si va por menos de la mitad (0.5), PUEDE inscribirse manualmente.
                    // Si va por más de la mitad, es OYENTE (Auditor) permanente.

                    $puedeInscribirse = false;
                    $inscripcionesCerradas = false;
                    $cursoFinalizado = false;

                    // Verificar si finalizó
                    if ($hoy > $fechaFin) {
                        $cursoFinalizado = true;
                        $gradingEnabled = false; // Nadie puede ser calificado si ya terminó, o quizás sí si entregó antes, pero por ahora simple
                    } elseif ($progresoTemporal <= 0.5) {
                        // Puede participar
                        $puedeInscribirse = true;
                        $gradingEnabled = false; // Aún no es alumno, debe darle clic a participar
                    } else {
                        // Pasó el 50%
                        $inscripcionesCerradas = true;
                        $gradingEnabled = false;
                        $auditorMessage = "Inscripciones cerradas (Curso > 50%). Estás en modo OYENTE: puedes ver el contenido pero no serás calificado.";
                    }
                } else {
                    $gradingEnabled = false;
                }
            }
        }

        // 4. Renderizar Vista
        $title = "Aula Virtual: " . $cursoData['nombre'];
        require_once __DIR__ . '/../views/aula/index.php';
    }

    public function participar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_POST['curso_id'])) {
            header('Location: index.php?controller=Curso&action=mis_cursos');
            exit();
        }

        $curso_id = (int) $_POST['curso_id'];
        $user_id = $_SESSION['user_id'];

        // Validar de nuevo las reglas por seguridad
        require_once __DIR__ . '/../models/Curso.php';
        require_once __DIR__ . '/../models/Inscripcion.php';

        $cursoModel = new Curso();
        $curso = $cursoModel->obtenerPorId($curso_id);
        $cursoData = is_object($curso) ? (array) $curso : $curso;

        if ($cursoData) {
            $fechaInicio = strtotime($cursoData['fecha_inicio']);
            $fechaFin = strtotime($cursoData['fecha_fin']);
            $hoy = time();

            if ($fechaFin > $fechaInicio) {
                $duracionTotal = $fechaFin - $fechaInicio;
                $tiempoTranscurrido = $hoy - $fechaInicio;
                $progresoTemporal = $tiempoTranscurrido / $duracionTotal;
            } else {
                $progresoTemporal = 0;
            }

            if ($progresoTemporal <= 0.5 && $hoy <= $fechaFin) {
                $inscripcionModel = new Inscripcion();
                if ($inscripcionModel->registrar($user_id, $curso_id)) {
                    $_SESSION['success'] = '¡Te has inscrito correctamente! Ahora puedes entregar tareas.';
                } else {
                    $_SESSION['error'] = 'Error al inscribirse o ya estabas inscrito.';
                }
            } else {
                $_SESSION['error'] = 'No es posible inscribirse: El curso ya ha avanzado más del 50% o ha finalizado.';
            }
        }

        header("Location: index.php?controller=Aula&action=index&id=" . $curso_id);
        exit();
    }

    public function certificado()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
            die("Acceso denegado");
        }

        $curso_id = (int) $_GET['id'];
        $user_id = $_SESSION['user_id'];

        // Verificar aprobación
        require_once __DIR__ . '/../models/Inscripcion.php';
        $inscripcionModel = new Inscripcion();
        $misCursos = $inscripcionModel->obtenerCursosPorEstudiante($user_id);

        $datosInscripcion = null;
        foreach ($misCursos as $mc) {
            if ($mc['id'] == $curso_id) {
                $datosInscripcion = $mc;
                break;
            }
        }

        if (!$datosInscripcion || $datosInscripcion['estado_inscripcion'] !== 'aprobado') {
            die("No tienes un certificado disponible para este curso. Debes aprobarlo primero.");
        }

        // Obtener datos del curso y profesor
        $curso = $this->cursoModel->obtenerPorId($curso_id);
        $cursoData = is_object($curso) ? (array) $curso : $curso;

        // Obtener nombre del estudiante
        require_once __DIR__ . '/../models/Usuario.php';
        // Asumiendo que podemos obtener datos del usuario actual
        // Si no hay un metodo directo, usaremos la sesión si tiene nombre, o un query rapido.
        // Haremos un metodo helper rapido aqui o query
        // Para simplificar, asumimos que nombre esta en session o lo sacamos del modelo
        $nombreEstudiante = $_SESSION['user_name'] . ' ' . ($_SESSION['user_lastname'] ?? '');

        require_once __DIR__ . '/../views/aula/certificado.php';
    }

    public function editar_modulo()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        $this->verificarPermisosProfesor(); // Helper o check manual

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) $_POST['id'];
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $curso_id = (int) $_POST['curso_id'];

            // Actualizar
            $sql = "UPDATE curso_modulo SET titulo = ?, descripcion = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssi", $titulo, $descripcion, $id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Módulo actualizado.";
                } else {
                    $_SESSION['error'] = "Error al actualizar módulo.";
                }
                $stmt->close();
            }
            header("Location: index.php?controller=Aula&action=index&id=" . $curso_id);
            exit();
        }
    }

    public function editar_contenido()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        $this->verificarPermisosProfesor();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) $_POST['id'];
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $url = $_POST['url_recurso'] ?? '';
            $curso_id = (int) $_POST['curso_id'];

            // Si es archivo, la lógica de subida es más compleja, por ahora solo editamos texto/url simple
            // Ojo: Si suben otro archivo, habría que manejarlo. Por simplicidad en este "Fix", actualizamos textos.

            $sql = "UPDATE curso_contenido SET titulo = ?, descripcion = ?, url_recurso = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sssi", $titulo, $descripcion, $url, $id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Recurso actualizado.";
                } else {
                    $_SESSION['error'] = "Error al actualizar recurso.";
                }
                $stmt->close();
            }
            header("Location: index.php?controller=Aula&action=index&id=" . $curso_id);
            exit();
        }
    }

    private function verificarPermisosProfesor()
    {
        $user_id = $_SESSION['user_id'] ?? 0;
        $user_role = $_SESSION['user_role'] ?? '';
        if ($user_role !== 'profesor' && !$this->usuarioModel->hasPermission($user_id, 'crear_curso')) {
            die("Acceso denegado");
        }
    }

    public function crear_modulo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->moduloModel->crear($_POST['curso_id'], $_POST['titulo'], $_POST['descripcion']);
            header("Location: index.php?controller=Aula&action=index&id=" . $_POST['curso_id']);
        }
    }

    public function crear_contenido()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../models/Contenido.php';

            $url_recurso = $_POST['url'] ?? '';
            $tipo = $_POST['tipo'];

            // Lógica de subida de archivos
            if ($tipo === 'archivo' && isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/cursos/materiales/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileTmpPath = $_FILES['archivo_pdf']['tmp_name'];
                $fileName = time() . '_' . basename($_FILES['archivo_pdf']['name']);
                $destPath = $uploadDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // Guardar ruta relativa para acceso web
                    $url_recurso = 'uploads/cursos/materiales/' . $fileName;
                } else {
                    die("Error al subir el archivo.");
                }
            }

            $contenidoModel = new Contenido();
            $contenidoModel->crear($_POST['modulo_id'], $_POST['titulo'], $tipo, $url_recurso, $_POST['descripcion']);

            // Redirigir al curso
            header("Location: index.php?controller=Aula&action=index&id=" . $_POST['curso_id']);
        }
    }
    public function crear_tarea()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->tareaModel->crear(
                $_POST['modulo_id'],
                $_POST['titulo'],
                $_POST['descripcion'],
                $_POST['fecha_entrega'],
                $_POST['puntaje']
            );
            header("Location: index.php?controller=Aula&action=index&id=" . $_POST['curso_id']);
        }
    }
}
?>
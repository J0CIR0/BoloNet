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
        // Puedes reutilizar lógica de CursoController o simplificar aquí
        // Por ahora asumimos que si llega aquí es porque tiene permiso, pero lo ideal es validar

        $curso = $this->cursoModel->obtenerPorId($id_curso); // Devuelve objeto u array
        if (!$curso) {
            die("Curso no encontrado");
        }
        // Convertir a array si es objeto para consistencia
        $cursoData = is_object($curso) ? (array) $curso : $curso;

        // 2. Cargar contenido del aula
        $modulos = $this->moduloModel->getByCurso($id_curso);

        // 3. Determinar rol en el contexto del aula
        // Permitir edición si es rol 'profesor' o tiene permiso 'crear_curso'
        $user_role = $_SESSION['user_role'] ?? '';
        $esProfesor = ($user_role === 'profesor') || $this->usuarioModel->hasPermission($user_id, 'crear_curso');

        // 4. Renderizar Vista
        // La vista tendrá pestañas: Contenido, Calificaciones, etc.
        $title = "Aula Virtual: " . $cursoData['nombre'];
        require_once __DIR__ . '/../views/aula/index.php';
    }

    // --- ACCIONES DEL PROFESOR ---

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
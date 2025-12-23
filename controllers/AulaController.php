<?php
require_once __DIR__ . '/../models/Modulo.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Tarea.php';

class AulaController
{
    private $moduloModel;
    private $cursoModel;
    private $usuarioModel;
    private $tareaModel;
    private $inscripcionModel;
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../models/Database.php';
        require_once __DIR__ . '/../models/Inscripcion.php';
        $this->db = Database::getConnection();
        $this->moduloModel = new Modulo();
        $this->cursoModel = new Curso();
        $this->usuarioModel = new Usuario();
        $this->tareaModel = new Tarea();
        $this->inscripcionModel = new Inscripcion();
    }

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

        $user_role = $_SESSION['user_role'] ?? '';
        $esProfesor = ($user_role === 'profesor') || $this->usuarioModel->hasPermission($user_id, 'crear_curso');

        // Lógica Freemium:
        // Todos pueden entrar.
        // Acceso Premium si: Es Profesor O Tiene Suscripción Activa.
        $isSubscribed = isset($_SESSION['subscription_status']) && $_SESSION['subscription_status'] === 'active';
        $isPremiumAccess = $esProfesor || $isSubscribed;

        // También permitimos acceso completo si el usuario YA estaba inscrito formalmente (Legacy o becado)
        if (!$isPremiumAccess) {
            require_once __DIR__ . '/../models/Inscripcion.php';
            $inscripcionCheck = new Inscripcion();
            $yaInscrito = $inscripcionCheck->verificarInscripcion($user_id, $id_curso);
            if ($yaInscrito) {
                $isPremiumAccess = true;
            }
        }

        // Eliminamos el boqueo estricto
        /*
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
        */

        $curso = $this->cursoModel->obtenerPorId($id_curso);
        if (!$curso) {
            die("Curso no encontrado");
        }
        $cursoData = is_object($curso) ? (array) $curso : $curso;

        $modulos = $this->moduloModel->getByCurso($id_curso);


        $gradingEnabled = false;
        $auditorMessage = "";

        if ($esProfesor) {
            $gradingEnabled = true;
            $inscritos = $this->inscripcionModel->obtenerInscritosPorCurso($id_curso);
        } else {
            require_once __DIR__ . '/../models/Inscripcion.php';
            $inscripcionModel = new Inscripcion();
            $estaInscrito = $inscripcionModel->verificarInscripcion($user_id, $id_curso);

            $isAprobado = false;
            $datosInscripcion = null;

            if ($estaInscrito) {

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

                $gradingEnabled = true;
            } else {
                $isSubscribed = isset($_SESSION['subscription_status']) && $_SESSION['subscription_status'] === 'active';

                if ($isSubscribed) {

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



                    $puedeInscribirse = false;
                    $inscripcionesCerradas = false;
                    $cursoFinalizado = false;

                    if ($hoy > $fechaFin) {
                        $cursoFinalizado = true;

                        $gradingEnabled = false;
                    } elseif ($progresoTemporal <= 0.5) {
                        $puedeInscribirse = true;
                        $gradingEnabled = false;
                    } else {
                        $inscripcionesCerradas = true;
                        $gradingEnabled = false;
                        $auditorMessage = "Inscripciones cerradas (Curso > 50%). Estás en modo OYENTE: puedes ver el contenido pero no serás calificado.";
                    }
                } else {
                    $gradingEnabled = false;
                }
            }
        }

        $calificacionesData = [];


        $todasLasTareas = [];
        if (!empty($modulos)) {
            foreach ($modulos as $mod) {
                if (!empty($mod['tareas'])) {
                    foreach ($mod['tareas'] as $t) {
                        $t['modulo_titulo'] = $mod['titulo'];
                        $todasLasTareas[] = $t;
                    }
                }
            }
        }

        if (!$esProfesor && ($yaInscrito || ($isSubscribed && $puedeInscribirse))) {

            foreach ($todasLasTareas as &$tarea) {
                $entrega = $this->tareaModel->getEntregaEstudiante($tarea['id'], $_SESSION['user_id']);
                $tarea['entrega'] = $entrega;
            }
            unset($tarea);
            $calificacionesData = $todasLasTareas;
        } elseif ($esProfesor) {

            $calificacionesData = $todasLasTareas;
        }

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



    public function editar_modulo()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        $this->verificarPermisosProfesor();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) $_POST['id'];
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $curso_id = (int) $_POST['curso_id'];

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

            if ($tipo === 'archivo' && isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/cursos/materiales/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileTmpPath = $_FILES['archivo_pdf']['tmp_name'];
                $fileName = time() . '_' . basename($_FILES['archivo_pdf']['name']);
                $destPath = $uploadDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $url_recurso = 'uploads/cursos/materiales/' . $fileName;
                } else {
                    die("Error al subir el archivo.");
                }
            }

            $contenidoModel = new Contenido();
            $contenidoModel->crear($_POST['modulo_id'], $_POST['titulo'], $tipo, $url_recurso, $_POST['descripcion']);

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
    public function subir_tarea()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $tarea_id = (int) $_POST['tarea_id'];
            $curso_id = (int) $_POST['curso_id'];
            $comentario = $_POST['comentario'] ?? '';
            $user_id = $_SESSION['user_id'];

            $entregaExistente = $this->tareaModel->getEntregaEstudiante($tarea_id, $user_id);
            $url_archivo = $entregaExistente ? $entregaExistente['archivo_url'] : '';

            if (isset($_FILES['archivo_tarea']) && $_FILES['archivo_tarea']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/cursos/tareas/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = time() . '_' . $user_id . '_' . basename($_FILES['archivo_tarea']['name']);
                $destPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['archivo_tarea']['tmp_name'], $destPath)) {
                    $url_archivo = 'uploads/cursos/tareas/' . $fileName;
                } else {
                    $_SESSION['error'] = "Error al mover el archivo subido.";
                    header("Location: index.php?controller=Aula&action=index&id=" . $curso_id);
                    exit();
                }
            }

            if ($this->tareaModel->entregar($tarea_id, $user_id, $url_archivo, $comentario)) {
                $_SESSION['success'] = "Entrega guardada correctamente.";
            } else {
                $_SESSION['error'] = "Error al guardar la entrega.";
            }

            header("Location: index.php?controller=Aula&action=ver_tarea&id=" . $tarea_id);
            exit();
        }
    }

    public function eliminar_entrega()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $tarea_id = (int) $_POST['tarea_id'];
            $curso_id = (int) $_POST['curso_id'];
            $user_id = $_SESSION['user_id'];

            $archivoEliminado = $this->tareaModel->eliminarEntrega($tarea_id, $user_id);

            if ($archivoEliminado !== false) {
                if ($archivoEliminado && file_exists(__DIR__ . '/../' . $archivoEliminado)) {
                    unlink(__DIR__ . '/../' . $archivoEliminado);
                }
                $_SESSION['success'] = "Entrega eliminada.";
            } else {
                $_SESSION['error'] = "No se pudo eliminar la entrega.";
            }

            header("Location: index.php?controller=Aula&action=ver_tarea&id=" . $tarea_id);
            exit();
        }
    }
    public function ver_tarea()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
            die("Acceso denegado o ID inválido");
        }

        $tarea_id = (int) $_GET['id'];
        $user_id = $_SESSION['user_id'];

        $tarea = $this->tareaModel->obtenerPorId($tarea_id);
        if (!$tarea) {
            die("Tarea no encontrada");
        }

        $stmt = $this->db->prepare("SELECT curso_id FROM curso_modulo WHERE id = ?");
        $stmt->bind_param("i", $tarea['modulo_id']);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        $curso_id = $res['curso_id'] ?? 0;

        require_once __DIR__ . '/../models/Usuario.php';
        $esProfesor = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'profesor');

        $entregas = [];
        $entrega = null;

        if ($esProfesor) {
            $entregas = $this->tareaModel->getEntregasPorTarea($tarea_id);
        } else {
            $entrega = $this->tareaModel->getEntregaEstudiante($tarea_id, $user_id);
        }

        require_once __DIR__ . '/../views/aula/ver_tarea.php';
    }

    public function calificar_entrega()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        $this->verificarPermisosProfesor();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $entrega_id = (int) $_POST['entrega_id'];
            $tarea_id = (int) $_POST['tarea_id'];
            $calificacion = $_POST['calificacion'];
            $retroalimentacion = $_POST['retroalimentacion'];

            $sql = "UPDATE curso_entrega SET calificacion = ?, retroalimentacion = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("dsi", $calificacion, $retroalimentacion, $entrega_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Calificación guardada.";
                } else {
                    $_SESSION['error'] = "Error al guardar calificación.";
                }
                $stmt->close();
            }

            if (isset($_POST['redirect_view']) && $_POST['redirect_view'] === 'calificar_tarea') {
                header("Location: index.php?controller=Aula&action=ver_calificaciones_tarea&id=" . $tarea_id);
            } else {
                header("Location: index.php?controller=Aula&action=ver_tarea&id=" . $tarea_id);
            }
            exit();
        }
    }

    public function ver_calificaciones_tarea()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        $this->verificarPermisosProfesor();

        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=Curso&action=mis_cursos');
            exit();
        }

        $tarea_id = (int) $_GET['id'];

        require_once __DIR__ . '/../models/Tarea.php';
        require_once __DIR__ . '/../models/Modulo.php';

        if (!isset($this->tareaModel))
            $this->tareaModel = new Tarea();
        $moduloModel = new Modulo();

        $tarea = $this->tareaModel->obtenerPorId($tarea_id);
        if (!$tarea) {
            $_SESSION['error'] = "Tarea no encontrada.";
            header('Location: index.php');
            exit();
        }

        $modulo = $moduloModel->obtenerPorId($tarea['modulo_id']);
        $curso_id = $modulo['curso_id'];

        $entregas = $this->tareaModel->getEntregasPorTarea($tarea_id);

        $title = "Calificar: " . $tarea['titulo'];

        require_once __DIR__ . '/../views/aula/calificar_tarea.php';
    }

    public function aprobar_estudiante()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        $this->verificarPermisosProfesor();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $curso_id = (int) $_POST['curso_id'];
            $estudiante_id = (int) $_POST['estudiante_id'];
            $nota_final = $_POST['nota_final'];

            if ($this->inscripcionModel->aprobarEstudiante($curso_id, $estudiante_id, $nota_final)) {
                $_SESSION['success'] = "Estudiante aprobado y certificado generado.";

                try {
                    $estudiante = $this->inscripcionModel->getDetalleInscripcion($curso_id, $estudiante_id);
                    if ($estudiante && !empty($estudiante['email'])) {
                        require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
                        require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
                        require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
                        if (!defined('SMTP_HOST')) {
                            require_once __DIR__ . '/../config/smtp.php';
                        }

                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                        $host = $_SERVER['HTTP_HOST'];
                        $path = dirname($_SERVER['PHP_SELF']);
                        $certLink = $protocol . "://" . $host . "/BoloNet/index.php?controller=Aula&action=certificado&id=" . $curso_id;

                        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = SMTP_HOST;
                        $mail->SMTPAuth = true;
                        $mail->Username = SMTP_USER;
                        $mail->Password = SMTP_PASS;
                        $mail->SMTPSecure = SMTP_SECURE;
                        $mail->Port = SMTP_PORT;

                        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
                        $mail->addAddress($estudiante['email'], $estudiante['nombre'] . ' ' . $estudiante['apellido']);

                        $mail->isHTML(true);
                        $mail->Subject = '¡Felicidades! Has aprobado el curso';
                        $mail->Body = "
                            <h1>¡Felicitaciones " . htmlspecialchars($estudiante['nombre']) . "!</h1>
                            <p>Nos complace informarte que has aprobado satisfactoriamente tu curso.</p>
                            <p>Tu <strong>Certificado de Finalización</strong> ya está disponible.</p>
                            <p>Puedes verlo y descargarlo en formato PDF haciendo clic en el siguiente enlace:</p>
                            <p><a href='" . $certLink . "' style='background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ver Mi Certificado</a></p>
                            <p>Si el botón no funciona, copia y pega este enlace en tu navegador:<br> $certLink</p>
                            <hr>
                            <small>BoloNet Learning System</small>
                        ";

                        $mail->send();
                        $_SESSION['success'] .= " Notificación enviada al correo.";
                    }
                } catch (Throwable $e) {

                    error_log("Error enviando certificado: " . $mail->ErrorInfo);
                }

            } else {
                $_SESSION['error'] = "Error al aprobar estudiante.";
            }

            header("Location: index.php?controller=Aula&action=index&id=" . $curso_id . "&section=calificaciones");
            exit();
        }
    }

    public function certificado()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        $curso_id = (int) $_GET['id'];
        $usuario_id = $_SESSION['user_id'];

        if (!$this->inscripcionModel->verificarInscripcion($usuario_id, $curso_id)) {
            die("No estás inscrito en este curso.");
        }

        $cursoObj = $this->cursoModel->obtenerPorId($curso_id);
        $curso = is_object($cursoObj) ? (array) $cursoObj : $cursoObj;

        $stmt = $this->db->prepare("SELECT i.*, p.nombre, p.apellido, p.ci 
                                    FROM inscripcion i 
                                    JOIN usuario u ON i.estudiante_id = u.id 
                                    JOIN persona p ON u.persona_id = p.id 
                                    WHERE i.curso_id = ? AND i.estudiante_id = ? AND i.estado = 'aprobado'");
        $stmt->bind_param("ii", $curso_id, $usuario_id);
        $stmt->execute();
        $inscripcion = $stmt->get_result()->fetch_assoc();

        if (!$inscripcion) {
            die("El certificado no está disponible o el curso no ha sido aprobado.");
        }

        require_once __DIR__ . '/../views/aula/certificado.php';
    }
}
?>
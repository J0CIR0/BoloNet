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

            // Increment interruption count before destroying session
            $this->usuario->incrementarInterrupciones($_SESSION['user_id']);

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

        // --- LÓGICA DE UPSELL (Banner Interrupciones) ---
        $showUpsellBanner = false;
        $upsellMessage = '';
        $interruptionCount = 0;
        if (isset($_SESSION['user_id'])) {
            $interruptionCount = $this->usuario->getInterrupciones($_SESSION['user_id']);
            $userPlan = $_SESSION['plan_type'] ?? 'basic';

            if ($interruptionCount >= 3) {
                $showUpsellBanner = true;
                if ($userPlan === 'basic') {
                    $upsellMessage = "Tu <strong>Plan Básico</strong> solo permite 1 sesión simultánea. Actualiza a <strong>Pro</strong> (3 sesiones) o <strong>Premium</strong> (5 sesiones) para evitar interrupciones.";
                } elseif ($userPlan === 'pro') {
                    $upsellMessage = "Tu <strong>Plan Pro</strong> permite 3 sesiones. Si necesitas más, actualiza a <strong>Premium</strong> (5 sesiones).";
                } else {
                    $showUpsellBanner = false;
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

        // Check for interruption upsell
        $userData = $this->usuario->getById($estudiante_id);
        $interruptionCount = $userData['conteo_interrupciones'] ?? 0;
        $planType = $userData['plan_type'] ?? 'basic'; // Default to basic if null

        $showUpsellBanner = false;
        $upsellMessage = '';
        $upsellTarget = ''; // 'pro' or 'premium'

        if ($interruptionCount >= 3) {
            if ($planType === 'basic' || !$planType) {
                // Basic -> Suggest Pro or Premium
                $showUpsellBanner = true;
                $upsellMessage = 'Con el <strong>Plan Básico</strong> solo tienes 1 sesión activa. Actualiza a <strong>Pro (3 sesiones)</strong> o <strong>Premium (5 sesiones)</strong>.';
                $upsellTarget = 'pro';
            } elseif ($planType === 'pro') {
                // Pro -> Suggest Premium
                $showUpsellBanner = true;
                $upsellMessage = 'Tu <strong>Plan Pro</strong> permite 3 sesiones. Si necesitas más, actualiza a <strong>Premium (5 sesiones)</strong>.';
                $upsellTarget = 'premium';
            }
        }

        if ((isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'profesor') || $this->usuario->hasPermission($estudiante_id, 'crear_curso')) {
            // Es PROFESOR: Obtener cursos asignados
            $cursosAsignados = $this->curso->getByProfesor($estudiante_id);
            $inscripciones = [];
            foreach ($cursosAsignados as $c) {
                $inscripciones[] = [
                    'id' => $c['id'],
                    'nombre' => $c['nombre'],
                    'codigo' => $c['codigo'],
                    'descripcion' => $c['descripcion'],
                    'estado_inscripcion' => 'asignado', // Estado especial para profesor
                    'nota_final' => 0,
                    'fecha_inscripcion' => $c['fecha_inicio'],
                    'fecha_inicio' => $c['fecha_inicio'],
                    'fecha_fin' => $c['fecha_fin'],
                    'estado' => $c['estado'],
                    'modalidad' => 'Presencial',
                    'hora_inicio' => '08:00',
                    'hora_fin' => '10:00',
                    'progreso' => 0
                ];
            }
        } elseif ($isSubscribed) {
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
                    'fecha_inscripcion' => $c['fecha_inicio'], // Usamos inicio curso como fecha ref
                    'fecha_inicio' => $c['fecha_inicio'],
                    'fecha_fin' => $c['fecha_fin'],
                    'estado' => $c['estado'],
                    'modalidad' => 'Presencial',
                    'hora_inicio' => '09:00',
                    'hora_fin' => '11:00'
                ];
            }
        } else {
            // Si no es suscriptor, ve solo lo que compró individualmente
            $inscripciones = $this->inscripcion->obtenerCursosPorEstudiante($estudiante_id);
            foreach ($inscripciones as &$ins) {
                if (!isset($ins['nombre']))
                    $ins['nombre'] = $ins['curso_nombre'] ?? 'Curso';
                if (!isset($ins['descripcion']))
                    $ins['descripcion'] = '';
                if (!isset($ins['estado']))
                    $ins['estado'] = $ins['curso_estado'] ?? 'activo';
            }
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
                $estado = $_POST['estado'];
                $inscripcionId = $_POST['inscripcion_id'];

                if (
                    $this->curso->actualizarInscripcion(
                        $inscripcionId,
                        $estado,
                        $_POST['nota_final'] ?? null
                    )
                ) {
                    $_SESSION['success'] = 'Inscripción actualizada';

                    // --- ENVIAR CORREO SI ES APROBADO ---
                    if ($estado === 'aprobado') {
                        try {
                            // Obtener datos del estudiante e inscripción
                            $estudiante = $this->inscripcion->getInscripcionPorId($inscripcionId);
                            $this->sendCertificateEmail($estudiante);
                            $_SESSION['success'] .= " y notificación enviada.";
                        } catch (Exception $e) {
                            error_log("Error enviando certificado: " . $e->getMessage());
                        }
                    }

                } else {
                    $_SESSION['error'] = 'Error al actualizar inscripción';
                }
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
    // --- ACCIÓN PARA REENVIAR CERTIFICADO ---
    public function reenviarCertificado()
    {
        $this->checkPermission('gestionar_inscripciones');

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['inscripcion_id'])) {
            $inscripcionId = $_POST['inscripcion_id'];
            $estudiante = $this->inscripcion->getInscripcionPorId($inscripcionId);

            if ($estudiante && $estudiante['estado'] === 'aprobado') {
                try {
                    $this->sendCertificateEmail($estudiante);
                    $_SESSION['success'] = 'Certificado reenviado exitosamente a ' . $estudiante['email'];
                } catch (Throwable $e) {
                    $_SESSION['error'] = 'Error al reenviar certificado: ' . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = 'El estudiante no está aprobado o no existe.';
            }

            // Redirigir de vuelta a gestionar inscripciones
            if ($estudiante) {
                header("Location: index.php?controller=Curso&action=gestionarInscripciones&id=" . $estudiante['curso_id']);
            } else {
                header('Location: index.php?controller=Curso&action=index');
            }
            exit();
        }
    }

    // --- HELPER PRIVADO PARA ENVIAR EMAIL ---
    private function sendCertificateEmail($estudiante)
    {
        if ($estudiante && !empty($estudiante['email'])) {
            require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
            require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
            require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

            if (!defined('SMTP_HOST')) {
                require_once __DIR__ . '/../config/smtp.php';
            }

            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            // Construir link al certificado
            $certLink = $protocol . "://" . $host . "/BoloNet/index.php?controller=Aula&action=certificado&id=" . $estudiante['curso_id'];

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
            $mail->Subject = 'Recordatorio: Tu Certificado del Curso ' . ($estudiante['curso_nombre'] ?? '');
            $mail->Body = "
                <h1>Hola " . htmlspecialchars($estudiante['nombre']) . "</h1>
                <p>Te enviamos nuevamente el enlace a tu certificado del curso <strong>" . htmlspecialchars($estudiante['curso_nombre'] ?? 'BoloNet') . "</strong>.</p>
                <p>Puedes verlo y descargarlo aquí:</p>
                <p><a href='" . $certLink . "' style='background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ver Mi Certificado</a></p>
                <p>Si el botón no funciona, copia y pega este enlace:<br> $certLink</p>
                <hr>
                <small>BoloNet Learning System</small>
            ";

            $mail->send();
        }
    }
}
?>
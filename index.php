<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';

// Cargar Autoload si existe
if (file_exists('autoload.php')) {
    require_once 'autoload.php';
}

// ==========================================
// 1. LOGICA DEL ENRUTADOR (CORREGIDA Y BLINDADA)
// ==========================================

if (isset($_GET['controller']) && isset($_GET['action'])) {

    $nombreControlador = $_GET['controller'] . 'Controller'; // Ej: CursoController
    $accion = $_GET['action']; // Ej: edit
    $archivoControlador = 'controllers/' . $nombreControlador . '.php';

    if (file_exists($archivoControlador)) {
        require_once $archivoControlador;

        if (class_exists($nombreControlador)) {
            $controlador = new $nombreControlador();

            if (method_exists($controlador, $accion)) {

                // --- SOLUCIÓN AL ERROR FATAL (ArgumentCountError) ---
                // Si la URL trae un ID, se lo pasamos a la función
                if (isset($_GET['id'])) {
                    $controlador->$accion($_GET['id']);
                } else {
                    $controlador->$accion();
                }
                // ----------------------------------------------------

                exit(); // Detenemos la ejecución aquí
            } else {
                die("Error: Método '$accion' no encontrado.");
            }
        } else {
            die("Error: Clase '$nombreControlador' no encontrada.");
        }
    } else {
        die("Error: Archivo de controlador no existe.");
    }
}

// ==========================================
// 2. REDIRECCIÓN DASHBOARD (Si ya está logueado)
// ==========================================

if (isset($_SESSION['user_id'])) {

    // VALIDACIÓN DE SESIÓN GLOBAL (Server-Side)
    // Evita navegar si la sesión fue eliminada en BD
    require_once 'models/UserSession.php';
    $sessionModel = new UserSession();
    if (!$sessionModel->isValid(session_id())) {
        session_destroy();
        header('Location: index.php?error=sesion_invalida');
        exit();
    }

    header('Location: dashboard.php');
    exit();
}

// ==========================================
// 3. LÓGICA DE LOGIN
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    if (file_exists('controllers/AuthController.php')) {
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        exit();
    }
}

$title = defined('SITE_NAME') ? SITE_NAME : 'BoloNet';
// Cargamos el header público
require_once 'views/layouts/public_header.php';
?>

<style>
    /* Fondo con degradado moderno oscuro */
    body {
        background: linear-gradient(135deg, #000000 0%, #1a4d2e 100%);
        /* Negro a Verde oscuro */
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Efecto de tarjeta de cristal */
    .glass-card {
        background: rgba(20, 20, 20, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(25, 135, 84, 0.3);
        border-radius: 20px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
    }

    /* Inputs optimizados para móvil */
    .form-control-custom {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid #2d6a4f;
        color: white;
        height: 50px;
        border-radius: 10px;
        padding-left: 15px;
    }

    .form-control-custom:focus {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        border-color: #40916c;
    }

    /* Títulos */
    .hero-title {
        font-weight: 800;
        background: -webkit-linear-gradient(#4ade80, #22c55e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0px 4px 10px rgba(34, 197, 94, 0.3);
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .main-container {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .hero-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .display-4 {
            font-size: 2.5rem;
        }
    }
</style>

<div class="container-fluid flex-grow-1 d-flex align-items-center justify-content-center main-container">
    <div class="row w-100 justify-content-center align-items-center">

        <div class="col-md-6 col-lg-5 text-white hero-section px-4">
            <h1 class="display-4 hero-title mb-3"><?php echo $title; ?></h1>
            <p class="lead fs-4 fw-light text-light">Tu plataforma de aprendizaje sin límites.</p>
            <p class="text-secondary d-none d-md-block">
                Gestiona tus cursos, revisa tu progreso y alcanza tus metas profesionales desde cualquier lugar.
            </p>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'sesion_invalida'): ?>
                <div class="alert alert-warning border border-warning shadow-lg mt-3" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Sesión Cerrada</h5>
                    <p class="mb-0">Tu cuenta inició sesión en otro dispositivo. Por seguridad, se ha cerrado esta sesión.
                    </p>
                    <hr>
                    <p class="mb-0 small">Mejora tu plan para conectar más dispositivos simultáneamente.</p>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-3 mt-4 d-none d-md-flex">
                <div class="d-flex align-items-center text-success"><i class="fas fa-check-circle me-2"></i> Accesible
                </div>
                <div class="d-flex align-items-center text-success"><i class="fas fa-check-circle me-2"></i> Rápido
                </div>
                <div class="d-flex align-items-center text-success"><i class="fas fa-check-circle me-2"></i> Seguro
                </div>
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="card glass-card p-4 mx-2">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-user-circle fa-2x text-success"></i>
                        </div>
                        <h4 class="text-white fw-bold">Iniciar Sesión</h4>
                        <p class="text-muted small">Ingresa tus credenciales para continuar</p>
                    </div>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label text-success small fw-bold">CORREO ELECTRÓNICO</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-success text-success border-end-0">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" name="email"
                                    class="form-control form-control-custom border-start-0 ps-2"
                                    placeholder="ejemplo@correo.com" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-success small fw-bold">CONTRASEÑA</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-success text-success border-end-0">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password"
                                    class="form-control form-control-custom border-start-0 ps-2" placeholder="••••••••"
                                    required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-lg text-uppercase mb-3"
                            style="border-radius: 10px;">
                            Ingresar <i class="fas fa-arrow-right ms-2"></i>
                        </button>

                        <div class="text-center d-flex justify-content-between align-items-center mt-3 px-1">
                            <a href="register.php" class="text-decoration-none text-white small opacity-75">
                                <i class="fas fa-user-plus"></i> Crear cuenta
                            </a>

                            <a href="forgot_password.php" class="text-decoration-none text-success small fw-bold">
                                <i class="fas fa-key"></i> ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4 text-muted small d-md-none">
                &copy; <?php echo date('Y'); ?> <?php echo $title; ?>.
            </div>
        </div>
    </div>
</div>

<?php
require_once 'views/layouts/public_footer.php';
?>
<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    require_once 'controllers/AuthController.php';
    $controller = new AuthController();
    $controller->login();
    exit();
}

$title = 'Inicio - ' . SITE_NAME;
require_once 'views/layouts/public_header.php';

require_once 'models/Curso.php';
$curso = new Curso();
$cursos = $curso->getCursosActivos();
?>

<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="display-4 text-success fw-bold"><?php echo SITE_NAME; ?></h1>
            <p class="lead">Sistema de Gestión de Cursos en Línea</p>
            
            <div class="card border-success mt-4">
                <div class="card-header bg-black">
                    <h5 class="mb-0 text-success">Iniciar Sesión</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Ingresar</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="forgot_password.php" class="text-success">¿Olvidaste tu contraseña?</a> | 
                        <a href="register.php" class="text-success">Registrarse</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body">
                    <h4 class="text-success">Características del Sistema</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2">Gestión de cursos y horarios</li>
                        <li class="mb-2">Sistema de inscripciones en línea</li>
                        <li class="mb-2">Control de roles y permisos</li>
                        <li class="mb-2">Seguimiento académico</li>
                        <li class="mb-2">Comunicación estudiante-profesor</li>
                    </ul>
                </div>
            </div>
            
            <div class="card border-success mt-3">
                <div class="card-body text-center">
                    <a href="public_cursos.php" class="btn btn-outline-success w-100">Ver Cursos Disponibles</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/public_footer.php'; ?>
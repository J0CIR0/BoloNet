<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$title = 'Inicio - ' . SITE_NAME;
require_once 'views/layouts/public_header.php';
?>

<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="display-4 text-success fw-bold"><?php echo SITE_NAME; ?></h1>
            <p class="lead">Sistema de Gestión de Cursos en Línea</p>
            <p>Gestiona tus cursos, inscripciones y actividades académicas de manera eficiente y segura.</p>
            <div class="mt-4">
                <a href="index.php" class="btn btn-success btn-lg me-3">Iniciar Sesión</a>
                <a href="register.php" class="btn btn-outline-success btn-lg">Registrarse</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="fas fa-graduation-cap fa-6x text-success mb-4"></i>
                    <h4>Características Principales</h4>
                    <ul class="list-unstyled text-start">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Gestión de cursos y horarios</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Sistema de inscripciones en línea</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Control de roles y permisos</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Seguimiento académico</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Comunicación estudiante-profesor</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
                <div class="card-body text-center">
                    <i class="fas fa-book-open fa-3x text-success mb-3"></i>
                    <h4>Explora Cursos</h4>
                    <p>Consulta nuestra oferta académica disponible</p>
                    <a href="public_cursos.php" class="btn btn-outline-success">Ver Cursos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
                <div class="card-body text-center">
                    <i class="fas fa-user-plus fa-3x text-success mb-3"></i>
                    <h4>Únete a Nosotros</h4>
                    <p>Regístrate y forma parte de nuestra comunidad académica</p>
                    <a href="register.php" class="btn btn-outline-success">Registrarse</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
                <div class="card-body text-center">
                    <i class="fas fa-sign-in-alt fa-3x text-success mb-3"></i>
                    <h4>Acceso Rápido</h4>
                    <p>Si ya tienes cuenta, ingresa al sistema</p>
                    <a href="index.php" class="btn btn-outline-success">Iniciar Sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/public_footer.php'; ?>
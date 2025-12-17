<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$title = 'Dashboard';
require_once 'views/layouts/header.php';

require_once 'models/Usuario.php';
require_once 'models/Persona.php';
require_once 'models/Curso.php';

$usuarioModel = new Usuario();
$personaModel = new Persona();
$cursoModel = new Curso();

$total_usuarios = 0;
$total_personas = 0;
$total_cursos = 0;
$cursos_activos = 0;

if($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_usuarios')) {
    $usuarios = $usuarioModel->getAll();
    $total_usuarios = count($usuarios);
}

if($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_personas')) {
    $personas = $personaModel->getAll();
    $total_personas = count($personas);
}

if($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_cursos')) {
    $cursos = $cursoModel->getAll();
    $total_cursos = count($cursos);
    $cursos_activos = count($cursoModel->getCursosActivos());
}
?>

<div class="container-fluid p-4">
    <h2 class="mb-4">Dashboard</h2>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4>Bienvenido, <?php echo $_SESSION['user_name']; ?></h4>
                    <p>Rol: <?php echo $_SESSION['user_role']; ?></p>
                    <p>Email: <?php echo $_SESSION['user_email']; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_usuarios')): ?>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="text-success"><?php echo $total_usuarios; ?></h1>
                    <p>Usuarios Registrados</p>
                    <a href="usuarios.php" class="btn btn-outline-success btn-sm">Ver Usuarios</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_personas')): ?>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="text-success"><?php echo $total_personas; ?></h1>
                    <p>Personas Registradas</p>
                    <a href="personas.php" class="btn btn-outline-success btn-sm">Ver Personas</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_cursos')): ?>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="text-success"><?php echo $total_cursos; ?></h1>
                    <p>Cursos Totales</p>
                    <a href="cursos.php" class="btn btn-outline-success btn-sm">Ver Cursos</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="text-success"><?php echo $cursos_activos; ?></h1>
                    <p>Cursos Activos</p>
                    <a href="cursos.php" class="btn btn-outline-success btn-sm">Ver Cursos</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
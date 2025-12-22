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
if ($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_usuarios')) {
    $usuarios = $usuarioModel->getAll();
    $total_usuarios = count($usuarios);
}
if ($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_personas')) {
    $personas = $personaModel->getAll();
    $total_personas = count($personas);
}
if ($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_cursos')) {
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

    <?php
    // --- LÓGICA DE UPSELL POR INTERRUPCIONES ---
    $showUpsellBanner = false;
    $upsellMessage = '';
    $user_id = $_SESSION['user_id'];

    // Obtener conteo actual
    $interruptionCount = $usuarioModel->getInterrupciones($user_id);
    $userPlan = $_SESSION['plan_type'] ?? 'basic'; // Default a basic si no está seteado
    
    // Definir límites (Basic: 1 sesión, Pro: 3 sesiones, Premium: 5 sesiones)
    // El "Upsell" se muestra si ha tenido interrupciones frecuentes (ej > 3) indicando que necesita más sesiones
    if ($interruptionCount >= 3) {
        $showUpsellBanner = true;
        if ($userPlan === 'basic') {
            $upsellMessage = "Tu <strong>Plan Básico</strong> solo permite 1 sesión simultánea. Actualiza a <strong>Pro</strong> (3 sesiones) o <strong>Premium</strong> (5 sesiones) para evitar interrupciones.";
        } elseif ($userPlan === 'pro') {
            $upsellMessage = "Tu <strong>Plan Pro</strong> permite 3 sesiones. Si necesitas más, actualiza a <strong>Premium</strong> (5 sesiones).";
        } else {
            // Premium (ya tiene max)
            $showUpsellBanner = false;
        }
    }
    ?>

    <?php if ($showUpsellBanner): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div
                    class="alert alert-warning border border-warning shadow-sm d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="alert-heading fw-bold"><i class="fas fa-exclamation-triangle"></i> ¡Mejora tu
                            experiencia!</h4>
                        <p class="mb-1">Hemos notado que has tenido <strong><?php echo $interruptionCount; ?>
                                interrupciones</strong> en tu sesión recientemente.</p>
                        <p class="mb-0 small"><?php echo $upsellMessage; ?></p>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <a href="index.php?controller=Pago&action=planes" class="btn btn-warning fw-bold text-dark">
                            <i class="fas fa-arrow-circle-up"></i> Ver Planes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="row">
        <?php if ($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_usuarios')): ?>
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
        <?php if ($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_personas')): ?>
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
        <?php if ($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_cursos')): ?>
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
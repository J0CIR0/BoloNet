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

$total_usuarios = count($usuarioModel->getAll());
$total_personas = count($personaModel->getAll());
$total_cursos = count($cursoModel->getAll());
$cursos_activos = count($cursoModel->getCursosActivos());
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
    
    <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_inscripciones')): 
        $inscripciones = $cursoModel->getInscripcionesByEstudiante($_SESSION['user_id']);
        if(!empty($inscripciones)):
    ?>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mis Cursos Inscritos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th>Código</th>
                                    <th>Estado</th>
                                    <th>Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($inscripciones as $inscripcion): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($inscripcion['curso_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($inscripcion['codigo']); ?></td>
                                    <td>
                                        <?php 
                                        $estado_clase = $inscripcion['estado'] == 'aprobado' ? 'success' : 
                                                      ($inscripcion['estado'] == 'reprobado' ? 'danger' : 
                                                      ($inscripcion['estado'] == 'retirado' ? 'warning' : 'info'));
                                        ?>
                                        <span class="badge bg-<?php echo $estado_clase; ?>"><?php echo ucfirst($inscripcion['estado']); ?></span>
                                    </td>
                                    <td><?php echo $inscripcion['nota_final'] ? $inscripcion['nota_final'] : 'N/A'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="cursos.php?action=mis_cursos" class="btn btn-success">Ver Todos Mis Cursos</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; endif; ?>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
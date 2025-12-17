<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';
$title = 'Cursos Disponibles';
require_once 'views/layouts/public_header.php';
require_once 'models/Curso.php';
$curso = new Curso();
$cursos = $curso->getCursosActivos();
?>
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="text-success">Cursos Disponibles</h1>
        <p class="lead">Explora nuestra oferta académica</p>
    </div>
    <div class="row">
        <?php foreach($cursos as $curso_item): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
                <div class="card-header bg-black">
                    <h5 class="mb-0 text-success"><?php echo htmlspecialchars($curso_item['nombre']); ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>Código:</strong> <?php echo htmlspecialchars($curso_item['codigo']); ?></p>
                    <p><strong>Duración:</strong> <?php echo $curso_item['duracion_horas']; ?> horas</p>
                    <p><strong>Fechas:</strong> <?php echo $curso_item['fecha_inicio']; ?> - <?php echo $curso_item['fecha_fin']; ?></p>
                    <p><strong>Profesor:</strong> <?php echo htmlspecialchars($curso_item['profesor_nombre'] ?? 'Por asignar'); ?></p>
                    <p><?php echo htmlspecialchars(substr($curso_item['descripcion'] ?? '', 0, 100)) . '...'; ?></p>
                </div>
                <div class="card-footer">
                    <span class="badge bg-success"><?php echo ucfirst($curso_item['estado']); ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if(empty($cursos)): ?>
    <div class="alert alert-info text-center">
        <h4>No hay cursos disponibles en este momento</h4>
        <p>Vuelve pronto para ver nuestra oferta académica</p>
    </div>
    <?php endif; ?>
    <div class="text-center mt-5">
        <?php if(isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php" class="btn btn-success">Volver al Dashboard</a>
        <?php else: ?>
        <a href="index.php" class="btn btn-success">Regresar al Inicio</a>
        <?php endif; ?>
    </div>
</div>
<?php require_once 'views/layouts/public_footer.php'; ?>
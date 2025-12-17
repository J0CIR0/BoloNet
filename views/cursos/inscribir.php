<?php
$title = 'Inscribirse en Curso';
?>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Inscribirse en Curso</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach($cursos_activos as $curso): ?>
            <div class="col-md-6 mb-3">
                <div class="card bg-dark">
                    <div class="card-body">
                        <h5><?php echo htmlspecialchars($curso['nombre']); ?></h5>
                        <p><strong>Código:</strong> <?php echo htmlspecialchars($curso['codigo']); ?></p>
                        <p><strong>Duración:</strong> <?php echo $curso['duracion_horas']; ?> horas</p>
                        <p><strong>Fechas:</strong> <?php echo $curso['fecha_inicio']; ?> - <?php echo $curso['fecha_fin']; ?></p>
                        <p><strong>Profesor:</strong> <?php echo htmlspecialchars($curso['profesor_nombre'] ?? 'Sin asignar'); ?></p>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="curso_id" value="<?php echo $curso['id']; ?>">
                            <button type="submit" class="btn btn-success w-100">Inscribirse</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if(empty($cursos_activos)): ?>
        <div class="alert alert-info">No hay cursos activos disponibles para inscripción.</div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="cursos.php" class="btn btn-secondary">Volver a Cursos</a>
        </div>
    </div>
</div>
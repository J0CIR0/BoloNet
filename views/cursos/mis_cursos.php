<?php
$title = 'Mis Cursos';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Mis Cursos</h2>
    <a href="cursos.php" class="btn btn-secondary">Volver a Cursos</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Código</th>
                        <th>Fecha Inscripción</th>
                        <th>Estado</th>
                        <th>Nota Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($inscripciones as $inscripcion): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($inscripcion['curso_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($inscripcion['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($inscripcion['fecha_inscripcion']); ?></td>
                        <td>
                            <?php 
                            $estado_clase = $inscripcion['estado'] == 'aprobado' ? 'success' : 
                                          ($inscripcion['estado'] == 'reprobado' ? 'danger' : 
                                          ($inscripcion['estado'] == 'retirado' ? 'warning' : 'info'));
                            ?>
                            <span class="badge bg-<?php echo $estado_clase; ?>"><?php echo ucfirst($inscripcion['estado']); ?></span>
                        </td>
                        <td>
                            <?php echo $inscripcion['nota_final'] ? $inscripcion['nota_final'] : 'N/A'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($inscripciones)): ?>
        <div class="alert alert-info">No estás inscrito en ningún curso.</div>
        <?php endif; ?>
    </div>
</div>
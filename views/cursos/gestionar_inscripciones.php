<?php
if (!isset($curso) || empty($curso)) {
    echo '<div class="alert alert-danger">Curso no encontrado</div>';
    return;
}
if (is_object($curso)) {
    $curso_data = (array) $curso;
} else {
    $curso_data = $curso;
}

$title = 'Gestionar Inscripciones';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestionar Inscripciones: <?php echo htmlspecialchars($curso_data['nombre']); ?></h2>
    <a href="cursos.php" class="btn btn-secondary">Volver a Cursos</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Email</th>
                        <th>Fecha Inscripción</th>
                        <th>Estado</th>
                        <th>Nota Final</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($inscripciones as $inscripcion): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($inscripcion['estudiante_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($inscripcion['email']); ?></td>
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
                        <td>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="inscripcion_id" value="<?php echo $inscripcion['id']; ?>">
                                <div class="input-group input-group-sm">
                                    <select name="estado" class="form-select form-select-sm">
                                        <option value="inscrito" <?php echo $inscripcion['estado'] == 'inscrito' ? 'selected' : ''; ?>>Inscrito</option>
                                        <option value="aprobado" <?php echo $inscripcion['estado'] == 'aprobado' ? 'selected' : ''; ?>>Aprobado</option>
                                        <option value="reprobado" <?php echo $inscripcion['estado'] == 'reprobado' ? 'selected' : ''; ?>>Reprobado</option>
                                        <option value="retirado" <?php echo $inscripcion['estado'] == 'retirado' ? 'selected' : ''; ?>>Retirado</option>
                                    </select>
                                    <input type="number" name="nota_final" class="form-control form-control-sm" placeholder="Nota" step="0.01" min="0" max="100" value="<?php echo $inscripcion['nota_final'] ?? ''; ?>" style="width: 80px;">
                                    <button type="submit" class="btn btn-success btn-sm">Actualizar</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($inscripciones)): ?>
        <div class="alert alert-info">No hay inscripciones en este curso.</div>
        <?php endif; ?>
    </div>
</div>
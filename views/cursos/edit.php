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
$curso_data['codigo'] = $curso_data['codigo'] ?? '';
$curso_data['nombre'] = $curso_data['nombre'] ?? '';
$curso_data['descripcion'] = $curso_data['descripcion'] ?? '';
$curso_data['duracion_horas'] = $curso_data['duracion_horas'] ?? 0;
$curso_data['fecha_inicio'] = $curso_data['fecha_inicio'] ?? '';
$curso_data['fecha_fin'] = $curso_data['fecha_fin'] ?? '';
$curso_data['profesor_id'] = $curso_data['profesor_id'] ?? null;
$curso_data['estado'] = $curso_data['estado'] ?? 'activo';
$fecha_inicio = ($curso_data['fecha_inicio'] == '0000-00-00' || empty($curso_data['fecha_inicio'])) ? date('Y-m-d') : $curso_data['fecha_inicio'];
$fecha_fin = ($curso_data['fecha_fin'] == '0000-00-00' || empty($curso_data['fecha_fin'])) ? date('Y-m-d', strtotime('+1 month')) : $curso_data['fecha_fin'];
$estado = $curso_data['estado'];
$title = 'Editar Curso';
?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Editar Curso: <?php echo htmlspecialchars($curso_data['nombre']); ?></h4>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Código del Curso</label>
                    <input type="text" name="codigo" class="form-control" value="<?php echo htmlspecialchars($curso_data['codigo']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre del Curso</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($curso_data['nombre']); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($curso_data['descripcion']); ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Duración (horas)</label>
                    <input type="number" name="duracion_horas" class="form-control" value="<?php echo htmlspecialchars($curso_data['duracion_horas']); ?>" min="1" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($fecha_inicio); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($fecha_fin); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Profesor</label>
                    <select name="profesor_id" class="form-control">
                        <option value="">Seleccionar Profesor</option>
                        <?php foreach($profesores as $profesor): ?>
                        <option value="<?php echo $profesor['id']; ?>" <?php echo $curso_data['profesor_id'] == $profesor['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($profesor['nombre_completo']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-control" required>
                        <option value="activo" <?php echo $estado == 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo $estado == 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                        <option value="completado" <?php echo $estado == 'completado' ? 'selected' : ''; ?>>Completado</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="cursos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
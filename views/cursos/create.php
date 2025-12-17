<?php
$title = 'Nuevo Curso';
$fecha_hoy = date('Y-m-d');
$fecha_mes = date('Y-m-d', strtotime('+1 month'));
?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Crear Nuevo Curso</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Código del Curso</label>
                    <input type="text" name="codigo" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre del Curso</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Duración (horas)</label>
                    <input type="number" name="duracion_horas" class="form-control" min="1" value="40" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $fecha_hoy; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="<?php echo $fecha_mes; ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Profesor</label>
                    <select name="profesor_id" class="form-control">
                        <option value="">Seleccionar Profesor</option>
                        <?php foreach($profesores as $profesor): ?>
                        <option value="<?php echo $profesor['id']; ?>"><?php echo htmlspecialchars($profesor['nombre_completo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-control">
                        <option value="activo" selected>Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="cursos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
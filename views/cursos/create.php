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
        <form method="POST" action="" id="cursoForm">
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
                    <label class="form-label">Fecha Inicio (AAAA-MM-DD)</label>
                    <input type="text" name="fecha_inicio" class="form-control" value="<?php echo $fecha_hoy; ?>" required pattern="\d{4}-\d{2}-\d{2}" placeholder="Ej: 2025-12-18">
                    <small class="text-muted">Formato: AAAA-MM-DD</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Fin (AAAA-MM-DD)</label>
                    <input type="text" name="fecha_fin" class="form-control" value="<?php echo $fecha_mes; ?>" required pattern="\d{4}-\d{2}-\d{2}" placeholder="Ej: 2026-01-18">
                    <small class="text-muted">Formato: AAAA-MM-DD</small>
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
                    <select name="estado" class="form-control" required>
                        <option value="activo" selected>Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="completado">Completado</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="cursos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
        <script>
        document.getElementById('cursoForm').addEventListener('submit', function(e) {
            var fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
            var fechaFin = document.querySelector('input[name="fecha_fin"]').value;
            var dateRegex = /^\d{4}-\d{2}-\d{2}$/;
            if (!dateRegex.test(fechaInicio)) {
                alert('Formato de Fecha Inicio incorrecto. Use AAAA-MM-DD (Ej: 2025-12-18)');
                e.preventDefault();
                return false;
            }
            if (!dateRegex.test(fechaFin)) {
                alert('Formato de Fecha Fin incorrecto. Use AAAA-MM-DD (Ej: 2026-01-18)');
                e.preventDefault();
                return false;
            }
            if (fechaInicio >= fechaFin) {
                alert('La fecha de inicio debe ser anterior a la fecha de fin');
                e.preventDefault();
                return false;
            }
            return true;
        });
        </script>
    </div>
</div>
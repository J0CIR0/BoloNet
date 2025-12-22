<?php
if (!isset($curso) || empty($curso)) {
    echo '<div class="alert alert-danger">Error: Curso no encontrado.</div>';
    return;
}

$curso_data = is_object($curso) ? (array) $curso : $curso;

$codigo = $curso_data['codigo'] ?? '';
$nombre = $curso_data['nombre'] ?? '';
$descripcion = $curso_data['descripcion'] ?? '';
$duracion = $curso_data['duracion_horas'] ?? 0;
$precio = $curso_data['precio'] ?? 0.00;
$profesor_id = $curso_data['profesor_id'] ?? null;
$estado = $curso_data['estado'] ?? 'activo';
$id = $curso_data['id'] ?? 0;

$f_inicio = ($curso_data['fecha_inicio'] == '0000-00-00' || empty($curso_data['fecha_inicio'])) ? date('Y-m-d') : $curso_data['fecha_inicio'];
$f_fin = ($curso_data['fecha_fin'] == '0000-00-00' || empty($curso_data['fecha_fin'])) ? date('Y-m-d', strtotime('+1 month')) : $curso_data['fecha_fin'];

$title = 'Editar Curso';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Curso: <?php echo htmlspecialchars($nombre); ?>
                    </h4>
                </div>
                <div class="card-body p-4">

                    <form method="POST" action="index.php?controller=Curso&action=edit&id=<?php echo $id; ?>"
                        id="cursoFormEdit">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Código del Curso</label>
                                <input type="text" name="codigo" class="form-control"
                                    value="<?php echo htmlspecialchars($codigo); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre del Curso</label>
                                <input type="text" name="nombre" class="form-control"
                                    value="<?php echo htmlspecialchars($nombre); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control"
                                rows="3"><?php echo htmlspecialchars($descripcion); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-success">Precio (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio" class="form-control" step="0.01" min="0"
                                        value="<?php echo htmlspecialchars($precio); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Duración (horas)</label>
                                <input type="number" name="duracion_horas" class="form-control"
                                    value="<?php echo htmlspecialchars($duracion); ?>" min="1" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control"
                                    value="<?php echo htmlspecialchars($f_inicio); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha Fin</label>
                                <input type="date" name="fecha_fin" class="form-control"
                                    value="<?php echo htmlspecialchars($f_fin); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Profesor</label>
                                <select name="profesor_id" class="form-select">
                                    <option value="">Seleccionar Profesor</option>
                                    <?php if (isset($profesores)): ?>
                                        <?php foreach ($profesores as $profesor): ?>
                                            <option value="<?php echo $profesor['id']; ?>" <?php echo $profesor_id == $profesor['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($profesor['nombre_completo'] ?? $profesor['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Estado</label>
                                <select name="estado" class="form-select" required>
                                    <option value="activo" <?php echo $estado == 'activo' ? 'selected' : ''; ?>>Activo
                                    </option>
                                    <option value="inactivo" <?php echo $estado == 'inactivo' ? 'selected' : ''; ?>>
                                        Inactivo</option>
                                    <option value="completado" <?php echo $estado == 'completado' ? 'selected' : ''; ?>>
                                        Completado</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="index.php?controller=Curso&action=index" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Curso
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('cursoFormEdit').addEventListener('submit', function (e) {
        var fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
        var fechaFin = document.querySelector('input[name="fecha_fin"]').value;
        var precio = parseFloat(document.querySelector('input[name="precio"]').value);

        if (fechaInicio >= fechaFin) {
            alert('⚠️ Error: La fecha de inicio debe ser anterior a la fecha de fin.');
            e.preventDefault();
            return false;
        }

        if (precio < 0) {
            alert('⚠️ Error: El precio no puede ser negativo.');
            e.preventDefault();
            return false;
        }

        return true;
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
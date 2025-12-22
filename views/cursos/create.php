<?php
$title = 'Nuevo Curso';
// Definimos fechas por defecto
$fecha_hoy = date('Y-m-d');
$fecha_mes = date('Y-m-d', strtotime('+1 month'));

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Crear Nuevo Curso</h4>
                </div>
                <div class="card-body p-4">

                    <form method="POST" action="index.php?controller=Curso&action=create" id="cursoForm">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Código del Curso <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="codigo" class="form-control" placeholder="Ej: PROG101"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre del Curso <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control"
                                    placeholder="Ej: Introducción a Java" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"
                                placeholder="Detalles sobre lo que aprenderán..."></textarea>
                        </div>

                        <div class="row">

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Duración (horas) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="duracion_horas" class="form-control" min="1" value="40"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control"
                                    value="<?php echo $fecha_hoy; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha Fin</label>
                                <input type="date" name="fecha_fin" class="form-control"
                                    value="<?php echo $fecha_mes; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Profesor Asignado</label>
                                <select name="profesor_id" class="form-select">
                                    <option value="">Seleccionar Profesor...</option>
                                    <?php if (isset($profesores)): ?>
                                        <?php foreach ($profesores as $profesor): ?>
                                            <option value="<?php echo $profesor['id']; ?>">
                                                <?php echo htmlspecialchars($profesor['nombre_completo'] ?? $profesor['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Estado Inicial</label>
                                <select name="estado" class="form-select" required>
                                    <option value="activo" selected>Activo (Visible)</option>
                                    <option value="inactivo">Inactivo (Oculto)</option>
                                    <option value="completado">Completado</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="index.php?controller=Curso&action=index" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar Curso
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('cursoForm').addEventListener('submit', function (e) {
        var fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
        var fechaFin = document.querySelector('input[name="fecha_fin"]').value;

        // Validaciones
        if (fechaInicio >= fechaFin) {
            alert('⚠️ Error: La fecha de inicio debe ser anterior a la fecha de fin.');
            e.preventDefault();
            return false;
        }

        // Validaciones
    if (fechaInicio >= fechaFin) {
        alert('⚠️ Error: La fecha de inicio debe ser anterior a la fecha de fin.');
        e.preventDefault();
        return false;
    }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
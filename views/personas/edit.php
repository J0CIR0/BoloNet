<?php
if (!isset($persona) || empty($persona)) {
    echo '<div class="alert alert-danger">Persona no encontrada</div>';
    return;
}
$title = 'Editar Persona';
?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Editar Persona: <?php echo htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido']); ?></h4>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Carnet de Identidad</label>
                    <input type="text" name="ci" class="form-control" value="<?php echo htmlspecialchars($persona['ci']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo htmlspecialchars($persona['fecha_nacimiento']); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($persona['nombre']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($persona['apellido']); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Género</label>
                    <select name="genero" class="form-control" required>
                        <option value="M" <?php echo $persona['genero'] == 'M' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="F" <?php echo $persona['genero'] == 'F' ? 'selected' : ''; ?>>Femenino</option>
                        <option value="O" <?php echo $persona['genero'] == 'O' ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($persona['telefono'] ?? ''); ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <textarea name="direccion" class="form-control" rows="2"><?php echo htmlspecialchars($persona['direccion'] ?? ''); ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="personas.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
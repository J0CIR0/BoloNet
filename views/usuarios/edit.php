<?php
if (!isset($usuario_data) || empty($usuario_data)) {
    echo '<div class="alert alert-danger">Usuario no encontrado</div>';
    return;
}
$title = 'Editar Usuario';
?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Editar Usuario: <?php echo htmlspecialchars($usuario_data['persona_nombre'] . ' ' . $usuario_data['persona_apellido']); ?></h4>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Carnet de Identidad</label>
                    <input type="text" name="ci" class="form-control" value="<?php echo htmlspecialchars($usuario_data['ci']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo htmlspecialchars($usuario_data['fecha_nacimiento'] ?? '2000-01-01'); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario_data['persona_nombre']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($usuario_data['persona_apellido']); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Género</label>
                    <select name="genero" class="form-control" required>
                        <option value="M" <?php echo ($usuario_data['genero'] ?? 'M') == 'M' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="F" <?php echo ($usuario_data['genero'] ?? 'M') == 'F' ? 'selected' : ''; ?>>Femenino</option>
                        <option value="O" <?php echo ($usuario_data['genero'] ?? 'M') == 'O' ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($usuario_data['telefono'] ?? ''); ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <textarea name="direccion" class="form-control" rows="2"><?php echo htmlspecialchars($usuario_data['direccion'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario_data['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select name="rol_id" class="form-control" required>
                    <option value="">Seleccionar Rol</option>
                    <?php foreach($roles as $rol): ?>
                    <option value="<?php echo $rol['id']; ?>" <?php echo $usuario_data['rol_id'] == $rol['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($rol['nombre']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Nueva Contraseña (dejar vacío para no cambiar)</label>
                <input type="password" name="password" class="form-control" placeholder="Nueva contraseña">
                <small class="text-muted">Solo llena este campo si quieres cambiar la contraseña</small>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
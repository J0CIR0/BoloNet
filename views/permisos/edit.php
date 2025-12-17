<?php
$title = 'Editar Permiso';
?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Editar Permiso</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Nombre del Permiso</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($permiso['nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Módulo</label>
                <input type="text" name="modulo" class="form-control" value="<?php echo htmlspecialchars($permiso['modulo']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" required><?php echo htmlspecialchars($permiso['descripcion']); ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="permisos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
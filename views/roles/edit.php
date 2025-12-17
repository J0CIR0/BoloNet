<?php
$title = 'Editar Rol';
?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Editar Rol: <?php echo htmlspecialchars($rol['nombre']); ?></h4>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Nombre del Rol</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($rol['nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" required><?php echo htmlspecialchars($rol['descripcion']); ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="roles.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
        <hr class="border-success my-4">
        <div class="mb-3">
            <h5>Administrar Permisos</h5>
            <a href="rolpermiso.php?action=manage&id=<?php echo $rol['id']; ?>" class="btn btn-info">Asignar Permisos</a>
        </div>
    </div>
</div>
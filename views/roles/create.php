<?php
$title = 'Nuevo Rol';
?>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Crear Nuevo Rol</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Nombre del Rol</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="roles.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php
$title = 'Asignar Permisos';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Asignar Permisos a Roles</h2>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Seleccione un Rol</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Rol</th>
                        <th>Descripción</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($roles as $rol): ?>
                    <tr>
                        <td><?php echo $rol['id']; ?></td>
                        <td><?php echo htmlspecialchars($rol['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($rol['descripcion']); ?></td>
                        <td>
                            <a href="rolpermiso.php?action=manage&id=<?php echo $rol['id']; ?>" class="btn btn-success btn-sm">Asignar Permisos</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
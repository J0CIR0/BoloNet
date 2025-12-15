<?php
$title = 'Permisos';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Permisos</h2>
    <?php 
    require_once __DIR__ . '/../../models/Usuario.php';
    $usuario = new Usuario();
    if($usuario->hasPermission($_SESSION['user_id'], 'ver_permisos')): 
    ?>
    <a href="permisos.php?action=create" class="btn btn-success">Nuevo Permiso</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Módulo</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($permisos as $permiso): ?>
                    <tr>
                        <td><?php echo $permiso['id']; ?></td>
                        <td><?php echo htmlspecialchars($permiso['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($permiso['modulo']); ?></td>
                        <td><?php echo htmlspecialchars($permiso['descripcion']); ?></td>
                        <td>
                            <a href="permisos.php?action=edit&id=<?php echo $permiso['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="permisos.php?action=delete&id=<?php echo $permiso['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar permiso?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
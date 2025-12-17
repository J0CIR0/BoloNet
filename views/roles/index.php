<?php
$title = 'Roles';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Roles</h2>
    <?php 
    require_once __DIR__ . '/../../models/Usuario.php';
    $usuario = new Usuario();
    if($usuario->hasPermission($_SESSION['user_id'], 'crear_rol')): 
    ?>
    <a href="roles.php?action=create" class="btn btn-success">Nuevo Rol</a>
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
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($roles as $rol): ?>
                    <tr>
                        <td><?php echo $rol['id']; ?></td>
                        <td><?php echo htmlspecialchars($rol['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($rol['descripcion']); ?></td>
                        <td>
                            <?php if($usuario->hasPermission($_SESSION['user_id'], 'editar_rol')): ?>
                            <a href="roles.php?action=edit&id=<?php echo $rol['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <?php endif; ?>
                            
                            <?php if($usuario->hasPermission($_SESSION['user_id'], 'asignar_permisos')): ?>
                            <a href="rolpermiso.php?action=manage&id=<?php echo $rol['id']; ?>" class="btn btn-info btn-sm">Permisos</a>
                            <?php endif; ?>
                            
                            <?php 
                            $puede_eliminar_rol = false;
                            if($usuario->hasPermission($_SESSION['user_id'], 'eliminar_rol')) {
                                if($rol['nombre'] != 'administrador') {
                                    $puede_eliminar_rol = true;
                                }
                            }
                            
                            if($puede_eliminar_rol): 
                            ?>
                            <a href="roles.php?action=delete&id=<?php echo $rol['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar rol?')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
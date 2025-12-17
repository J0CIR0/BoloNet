<?php
$title = 'Usuarios';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Usuarios</h2>
    <?php 
    require_once __DIR__ . '/../../models/Usuario.php';
    $usuarioModel = new Usuario();
    if($usuarioModel->hasPermission($_SESSION['user_id'], 'crear_usuario')): 
    ?>
    <a href="usuarios.php?action=create" class="btn btn-success">Nuevo Usuario</a>
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
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $usuarioItem): ?>
                    <tr>
                        <td><?php echo $usuarioItem['id']; ?></td>
                        <td><?php echo htmlspecialchars($usuarioItem['persona_nombre'] ?? $usuarioItem['nombre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($usuarioItem['persona_apellido'] ?? $usuarioItem['apellido'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($usuarioItem['email']); ?></td>
                        <td><span class="badge bg-success"><?php echo htmlspecialchars($usuarioItem['rol_nombre']); ?></span></td>
                        <td>
                            <?php 
                            $estado_texto = $usuarioItem['estado'] == 1 ? 'Verificado' : 'No verificado';
                            $estado_clase = $usuarioItem['estado'] == 1 ? 'success' : 'warning';
                            ?>
                            <span class="badge bg-<?php echo $estado_clase; ?>"><?php echo $estado_texto; ?></span>
                        </td>
                        <td>
                            <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'editar_usuario')): ?>
                            <a href="usuarios.php?action=edit&id=<?php echo $usuarioItem['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <?php endif; ?>
                            
                            <?php 
                            $puede_eliminar = false;
                            if($usuarioModel->hasPermission($_SESSION['user_id'], 'eliminar_usuario')) {
                                if($usuarioItem['id'] != $_SESSION['user_id']) {
                                    if($usuarioItem['rol_nombre'] != 'registro') {
                                        $puede_eliminar = true;
                                    }
                                }
                            }
                            
                            if($puede_eliminar): 
                            ?>
                            <a href="usuarios.php?action=delete&id=<?php echo $usuarioItem['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
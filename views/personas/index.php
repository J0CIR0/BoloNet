<?php
$title = 'Personas';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Personas</h2>
    <?php 
    require_once __DIR__ . '/../../models/Usuario.php';
    $usuarioModel = new Usuario();
    if($usuarioModel->hasPermission($_SESSION['user_id'], 'crear_persona')): 
    ?>
    <a href="personas.php?action=create" class="btn btn-success">Nueva Persona</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>CI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Fecha Nac.</th>
                        <th>Género</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($personas as $persona): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($persona['ci']); ?></td>
                        <td><?php echo htmlspecialchars($persona['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($persona['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($persona['fecha_nacimiento']); ?></td>
                        <td>
                            <?php 
                            $genero_text = $persona['genero'] == 'M' ? 'Masculino' : ($persona['genero'] == 'F' ? 'Femenino' : 'Otro');
                            echo $genero_text;
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($persona['telefono'] ?? 'N/A'); ?></td>
                        <td>
                            <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'editar_persona')): ?>
                            <a href="personas.php?action=edit&id=<?php echo $persona['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <?php endif; ?>
                            
                            <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'eliminar_persona')): ?>
                            <a href="personas.php?action=delete&id=<?php echo $persona['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar persona?')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
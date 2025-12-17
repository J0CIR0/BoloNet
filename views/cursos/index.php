<?php
$title = 'Cursos';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Cursos</h2>
    <?php 
    require_once __DIR__ . '/../../models/Usuario.php';
    $usuarioModel = new Usuario();
    if($usuarioModel->hasPermission($_SESSION['user_id'], 'crear_curso')): 
    ?>
    <a href="cursos.php?action=create" class="btn btn-success">Nuevo Curso</a>
    <?php endif; ?>
    <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'inscribir_curso')): ?>
    <a href="cursos.php?action=inscribir" class="btn btn-info">Inscribirse en Curso</a>
    <?php endif; ?>
    <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_inscripciones')): ?>
    <a href="cursos.php?action=mis_cursos" class="btn btn-primary">Mis Cursos</a>
    <?php endif; ?>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Duración</th>
                        <th>Fechas</th>
                        <th>Profesor</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cursos as $curso): ?>
                    <?php 
                    $fecha_inicio = isset($curso['fecha_inicio']) && $curso['fecha_inicio'] != '0000-00-00' ? $curso['fecha_inicio'] : 'Sin fecha';
                    $fecha_fin = isset($curso['fecha_fin']) && $curso['fecha_fin'] != '0000-00-00' ? $curso['fecha_fin'] : 'Sin fecha';
                    $estado = isset($curso['estado']) && !empty($curso['estado']) ? $curso['estado'] : 'activo';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($curso['codigo'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($curso['nombre'] ?? ''); ?></td>
                        <td><?php echo isset($curso['duracion_horas']) ? $curso['duracion_horas'] . ' horas' : '0 horas'; ?></td>
                        <td>
                            <?php echo htmlspecialchars($fecha_inicio); ?> - 
                            <?php echo htmlspecialchars($fecha_fin); ?>
                        </td>
                        <td><?php echo htmlspecialchars($curso['profesor_nombre'] ?? 'Sin asignar'); ?></td>
                        <td>
                            <?php 
                            $estado_clase = 'secondary';
                            if ($estado == 'activo') {
                                $estado_clase = 'success';
                            } elseif ($estado == 'inactivo') {
                                $estado_clase = 'warning';
                            } elseif ($estado == 'completado') {
                                $estado_clase = 'info';
                            }
                            ?>
                            <span class="badge bg-<?php echo $estado_clase; ?>"><?php echo ucfirst($estado); ?></span>
                        </td>
                        <td>
                            <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'editar_curso')): ?>
                            <a href="cursos.php?action=edit&id=<?php echo $curso['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <?php endif; ?>
                            <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'gestionar_inscripciones')): ?>
                            <a href="cursos.php?action=gestionar&id=<?php echo $curso['id']; ?>" class="btn btn-info btn-sm">Inscripciones</a>
                            <?php endif; ?>
                            <?php if($usuarioModel->hasPermission($_SESSION['user_id'], 'eliminar_curso')): ?>
                            <a href="cursos.php?action=delete&id=<?php echo $curso['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar curso?')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
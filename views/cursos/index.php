<?php
$title = 'Cursos';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white">Cursos Disponibles</h2>
    <?php
    require_once __DIR__ . '/../../models/Usuario.php';
    $usuarioModel = new Usuario();

    if ($usuarioModel->hasPermission($_SESSION['user_id'], 'crear_curso')):
        ?>
        <a href="index.php?controller=Curso&action=create" class="btn btn-success"><i class="fas fa-plus"></i> Nuevo
            Curso</a>
    <?php endif; ?>

    <?php if ($usuarioModel->hasPermission($_SESSION['user_id'], 'ver_inscripciones')): ?>
        <a href="index.php?controller=Curso&action=mis_cursos" class="btn btn-primary"><i class="fas fa-graduation-cap"></i>
            Mis Cursos</a>
    <?php endif; ?>
</div>


<?php if (isset($showUpsellBanner) && $showUpsellBanner): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div
                class="alert alert-warning border border-warning shadow-sm d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="alert-heading fw-bold"><i class="fas fa-exclamation-triangle"></i> ¡Mejora tu experiencia!
                    </h4>
                    <p class="mb-1">Hemos notado que has tenido <strong><?php echo $interruptionCount; ?>
                            interrupciones</strong> en tu sesión recientemente.</p>
                    <p class="mb-0 small"><?php echo $upsellMessage; ?></p>
                </div>
                <div class="mt-2 mt-md-0">
                    <a href="index.php?controller=Pago&action=planes" class="btn btn-warning fw-bold text-dark">
                        <i class="fas fa-arrow-circle-up"></i> Ver Planes
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card shadow-lg bg-dark border-secondary">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr class="table-secondary text-dark">
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
                    <?php if (empty($cursos)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">No hay cursos disponibles en este momento.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cursos as $curso): ?>
                            <?php
                            $fecha_inicio = isset($curso['fecha_inicio']) && $curso['fecha_inicio'] != '0000-00-00' ? date('d/m/Y', strtotime($curso['fecha_inicio'])) : 'Sin fecha';
                            $fecha_fin = isset($curso['fecha_fin']) && $curso['fecha_fin'] != '0000-00-00' ? date('d/m/Y', strtotime($curso['fecha_fin'])) : 'Sin fecha';
                            $estado = isset($curso['estado']) && !empty($curso['estado']) ? $curso['estado'] : 'activo';
                            $precio = isset($curso['precio']) ? number_format($curso['precio'], 2) : '0.00';


                            $ya_inscrito = false;
                            if (isset($cursos_inscritos) && is_array($cursos_inscritos)) {
                                if (in_array($curso['id'], $cursos_inscritos)) {
                                    $ya_inscrito = true;
                                }
                            }
                            ?>
                            <tr>
                                <td><span
                                        class="badge bg-secondary"><?php echo htmlspecialchars($curso['codigo'] ?? ''); ?></span>
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($curso['nombre'] ?? ''); ?></td>

                                <td><?php echo isset($curso['duracion_horas']) ? $curso['duracion_horas'] . ' hrs' : '-'; ?>
                                </td>
                                <td>
                                    <small class="d-block text-muted">Inicio: <?php echo $fecha_inicio; ?></small>
                                    <small class="d-block text-muted">Fin: <?php echo $fecha_fin; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($curso['profesor_nombre'] ?? 'Sin asignar'); ?></td>
                                <td>
                                    <?php
                                    $estado_clase = 'secondary';
                                    if ($estado == 'activo')
                                        $estado_clase = 'success';
                                    elseif ($estado == 'inactivo')
                                        $estado_clase = 'danger';
                                    elseif ($estado == 'completado')
                                        $estado_clase = 'info';
                                    ?>
                                    <span class="badge bg-<?php echo $estado_clase; ?>"><?php echo ucfirst($estado); ?></span>
                                </td>
                                <td>
                                    <div class="d-grid gap-2">
                                        <?php if ($ya_inscrito): ?>
                                            <a href="index.php?controller=Aula&action=index&id=<?php echo $curso['id']; ?>"
                                                class="btn btn-secondary btn-sm">
                                                <i class="fas fa-chalkboard"></i> Ir al Aula
                                            </a>
                                        <?php else: ?>
                                            <?php

                                            $rol = $_SESSION['user_role'] ?? 'estudiante';
                                            if ($rol !== 'profesor' && $rol !== 'admin'):
                                                ?>
                                                <a href="index.php?controller=Pago&action=planes" class="btn btn-success btn-sm">
                                                    <i class="fas fa-unlock"></i> Obtener Acceso
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <div class="btn-group btn-group-sm">
                                            <?php if ($usuarioModel->hasPermission($_SESSION['user_id'], 'editar_curso')): ?>
                                                <a href="index.php?controller=Curso&action=edit&id=<?php echo $curso['id']; ?>"
                                                    class="btn btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                                            <?php endif; ?>

                                            <?php if ($usuarioModel->hasPermission($_SESSION['user_id'], 'gestionar_inscripciones')): ?>
                                                <a href="index.php?controller=Curso&action=gestionarInscripciones&id=<?php echo $curso['id']; ?>"
                                                    class="btn btn-info" title="Alumnos"><i class="fas fa-users"></i></a>
                                            <?php endif; ?>

                                            <?php if ($usuarioModel->hasPermission($_SESSION['user_id'], 'eliminar_curso')): ?>
                                                <a href="index.php?controller=Curso&action=delete&id=<?php echo $curso['id']; ?>"
                                                    class="btn btn-danger" onclick="return confirm('¿Eliminar curso?')"
                                                    title="Eliminar"><i class="fas fa-trash"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
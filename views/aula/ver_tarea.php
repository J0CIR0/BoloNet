<?php require_once __DIR__ . '/../layouts/header.php';

function time_remaining($fecha_entrega)
{
    $now = new DateTime();
    $due = new DateTime($fecha_entrega);
    $diff = $now->diff($due);

    if ($now > $due) {
        return ['text' => 'La tarea está retrasada por ' . $diff->format('%a días, %h horas'), 'class' => 'text-danger', 'is_late' => true];
    } else {
        return ['text' => $diff->format('%a días, %h horas') . ' restantes', 'class' => 'text-success', 'is_late' => false];
    }
}

function format_date($date_str)
{
    if (!$date_str)
        return '-';
    $date = new DateTime($date_str);
    $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    return $days[$date->format('w')] . ', ' . $date->format('j') . ' de ' . $months[$date->format('n') - 1] . ' de ' . $date->format('Y') . ', ' . $date->format('H:i');
}

$timeInfo = time_remaining($tarea['fecha_entrega']);
?>

<div class="container-fluid py-5 bg-dark text-white" style="min-height: 100vh;">
    <div class="container">

        <h2 class="mb-4 fw-bold text-break"><?php echo htmlspecialchars($tarea['titulo']); ?></h2>

        <div class="mb-5">
            <div class="p-3 bg-secondary bg-opacity-10 rounded border border-secondary text-break">
                <?php echo nl2br(htmlspecialchars($tarea['descripcion'])); ?>
            </div>
        </div>

        <?php if ($esProfesor): ?>
            <div class="card bg-dark border-secondary shadow-lg">
                <div
                    class="card-header bg-secondary bg-opacity-25 border-secondary d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0 text-white"><i class="fas fa-chalkboard-teacher me-2"></i> Panel de Calificación</h5>
                    <span class="badge bg-primary mt-2 mt-md-0"><?php echo count($entregas); ?> Entregas</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-hover mb-0 align-middle">
                            <thead>
                                <tr class="text-white-50 small text-uppercase">
                                    <th>Estudiante</th>
                                    <th>Fecha Entrega</th>
                                    <th>Archivo</th>
                                    <th>Comentario</th>
                                    <th style="min-width: 150px;">Calificación (Max:
                                        <?php echo $tarea['puntaje_maximo']; ?>)
                                    </th>
                                    <th style="min-width: 200px;">Feedback</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($entregas)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-white-50">No hay entregas registradas aún.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($entregas as $ent): ?>
                                        <tr>
                                            <td class="fw-bold text-white">
                                                <?php echo htmlspecialchars($ent['nombre'] . ' ' . $ent['apellido']); ?>
                                            </td>
                                            <td class="small text-white-50">
                                                <?php echo format_date($ent['fecha_entrega']); ?>
                                                <?php if ($ent['fecha_entrega'] > $tarea['fecha_entrega']): ?>
                                                    <span class="badge bg-danger ms-1">Tardío</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($ent['archivo_url']): ?>
                                                    <a href="<?php echo htmlspecialchars($ent['archivo_url']); ?>" target="_blank"
                                                        class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="small text-white-50 fst-italic text-break" style="max-width: 200px;">
                                                <?php echo htmlspecialchars($ent['comentario'] ?? ''); ?>
                                            </td>

                                            <form action="index.php?controller=Aula&action=calificar_entrega" method="POST">
                                                <input type="hidden" name="entrega_id" value="<?php echo $ent['id']; ?>">
                                                <input type="hidden" name="tarea_id" value="<?php echo $tarea['id']; ?>">

                                                <td>
                                                    <input type="number" step="0.01" max="<?php echo $tarea['puntaje_maximo']; ?>"
                                                        name="calificacion"
                                                        class="form-control form-control-sm bg-dark text-white border-secondary"
                                                        value="<?php echo $ent['calificacion']; ?>"
                                                        placeholder="0-<?php echo $tarea['puntaje_maximo']; ?>">
                                                </td>
                                                <td>
                                                    <textarea name="retroalimentacion"
                                                        class="form-control form-control-sm bg-dark text-white border-secondary"
                                                        rows="1"
                                                        placeholder="Comentario..."><?php echo htmlspecialchars($ent['retroalimentacion'] ?? ''); ?></textarea>
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn btn-success btn-sm" title="Guardar">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php else: ?>

            <h4 class="mb-3 text-white border-bottom border-secondary pb-2">Estado de la entrega</h4>

            <div class="card bg-dark border-secondary mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-hover mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-white-50" style="width: 30%;">Estado de la entrega</td>
                                    <td>
                                        <?php if ($entrega): ?>
                                            <span class="bg-success bg-opacity-25 text-success px-2 py-1 rounded">Enviado para
                                                calificar</span>
                                        <?php else: ?>
                                            <span class="text-white-50">No entregado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold text-white-50">Estado de la calificación</td>
                                    <td>
                                        <?php if ($entrega && $entrega['calificacion'] !== null): ?>
                                            <span
                                                class="bg-success bg-opacity-25 text-success px-2 py-1 rounded">Calificado</span>
                                        <?php else: ?>
                                            <span class="bg-secondary bg-opacity-25 text-white-50 px-2 py-1 rounded">Sin
                                                calificar</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold text-white-50">Fecha de entrega</td>
                                    <td><?php echo format_date($tarea['fecha_entrega']); ?></td>
                                </tr>

                                <tr>
                                    <td class="fw-bold text-white-50">Tiempo restante</td>
                                    <td class="<?php echo $timeInfo['class']; ?>">
                                        <?php
                                        if ($entrega) {
                                            echo "La tarea fue enviada " . ($entrega['fecha_entrega'] <= $tarea['fecha_entrega'] ? 'a tiempo' : 'con retraso');
                                        } else {
                                            echo $timeInfo['text'];
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold text-white-50">Última modificación</td>
                                    <td>
                                        <?php echo $entrega ? format_date($entrega['fecha_entrega']) : '-'; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold text-white-50">Archivos enviados</td>
                                    <td>
                                        <?php if ($entrega && $entrega['archivo_url']): ?>
                                            <a href="<?php echo htmlspecialchars($entrega['archivo_url']); ?>" target="_blank"
                                                class="text-decoration-none text-info">
                                                <i class="fas fa-file-archive me-1"></i>
                                                <?php echo basename($entrega['archivo_url']); ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold text-white-50">Comentarios</td>
                                    <td class="text-break">
                                        <?php echo $entrega && $entrega['comentario'] ? htmlspecialchars($entrega['comentario']) : '-'; ?>
                                    </td>
                                </tr>

                                <?php if ($entrega && $entrega['retroalimentacion']): ?>
                                    <tr class="table-success">
                                        <td class="fw-bold text-success">Retroalimentación</td>
                                        <td class="text-success text-break">
                                            <strong>Calificación: <?php echo $entrega['calificacion']; ?> /
                                                <?php echo $tarea['puntaje_maximo']; ?></strong><br>
                                            <?php echo nl2br(htmlspecialchars($entrega['retroalimentacion'])); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <?php if (!$entrega): ?>
                    <?php if (!$timeInfo['is_late']): ?>
                        <button class="btn btn-primary px-4 py-2 w-100 w-md-auto mb-2 mb-md-0" type="button"
                            data-bs-toggle="collapse" data-bs-target="#formEntrega">
                            Agregar entrega
                        </button>
                    <?php else: ?>
                        <button class="btn btn-danger px-4 py-2 w-100 w-md-auto" disabled>
                            La tarea está cerrada
                        </button>
                        <p class="text-danger mt-2 small">No se aceptan entregas fuera de plazo.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (!$entrega['calificacion']): ?>
                        <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                            <button class="btn btn-secondary px-4 py-2 w-100 w-md-auto" type="button" data-bs-toggle="collapse"
                                data-bs-target="#formEntrega">
                                Editar entrega
                            </button>

                            <form action="index.php?controller=Aula&action=eliminar_entrega" method="POST"
                                onsubmit="return confirm('¿Estás seguro de que quieres quitar tu envío? Se borrará el archivo y el comentario.');"
                                class="w-100 w-md-auto">
                                <input type="hidden" name="tarea_id" value="<?php echo $tarea['id']; ?>">
                                <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                                <button type="submit" class="btn btn-danger px-4 py-2 w-100">
                                    Quitar envío
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info d-inline-block">
                            Esta tarea ya ha sido calificada y no se puede editar.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <a href="index.php?controller=Aula&action=index&id=<?php echo $curso_id; ?>"
                    class="btn btn-outline-light ms-0 ms-md-2 mt-2 mt-md-0 w-100 w-md-auto px-4">
                    Volver al curso
                </a>
            </div>

            <div class="collapse mt-4 <?php echo (!$entrega && !$timeInfo['is_late']) ? 'show' : ''; ?>" id="formEntrega">
                <div class="card bg-dark border-secondary mx-auto" style="max-width: 800px;">
                    <div class="card-header border-secondary bg-secondary bg-opacity-10">
                        <?php echo $entrega ? 'Editar entrega' : 'Subir archivos'; ?>
                    </div>
                    <div class="card-body">
                        <form action="index.php?controller=Aula&action=subir_tarea" method="POST"
                            enctype="multipart/form-data" class="dropzone-style">
                            <input type="hidden" name="tarea_id" value="<?php echo $tarea['id']; ?>">
                            <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">

                            <div class="mb-4 text-center border border-secondary border-dashed p-4 p-md-5 rounded">
                                <i class="fas fa-cloud-upload-alt fa-3x text-white-50 mb-3"></i>

                                <?php if ($entrega && $entrega['archivo_url']): ?>
                                    <div class="mb-3 text-start bg-secondary bg-opacity-25 p-2 rounded">
                                        <span class="text-white-50 small d-block">Archivo actual:</span>
                                        <a href="<?php echo $entrega['archivo_url']; ?>" target="_blank"
                                            class="text-info fw-bold text-break">
                                            <i class="fas fa-file me-1"></i> <?php echo basename($entrega['archivo_url']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label
                                        class="form-label text-white-50 small mb-1"><?php echo $entrega ? 'Reemplazar archivo (opcional)' : 'Seleccionar archivo'; ?></label>
                                    <input type="file" name="archivo_tarea"
                                        class="form-control bg-dark text-white border-secondary" <?php echo $entrega ? '' : 'required'; ?>>
                                </div>
                                <p class="text-muted small">Arrastre y suelte los archivos aquí o use el selector de
                                    archivos.</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white">Comentarios:</label>
                                <textarea name="comentario" class="form-control bg-dark text-white border-secondary"
                                    rows="3"><?php echo $entrega['comentario'] ?? ''; ?></textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success px-4 w-100 w-md-auto mb-2 mb-md-0">Guardar
                                    cambios</button>
                                <button type="button" class="btn btn-outline-light px-4 w-100 w-md-auto"
                                    data-bs-toggle="collapse" data-bs-target="#formEntrega">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
    .border-dashed {
        border-style: dashed !important;
    }

    @media (min-width: 768px) {
        .w-md-auto {
            width: auto !important;
        }
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
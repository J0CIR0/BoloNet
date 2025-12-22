<?php require_once __DIR__ . '/../layouts/header.php';

if (!function_exists('format_date_grading')) {
    function format_date_grading($date_str)
    {
        if (!$date_str)
            return '-';
        $date = new DateTime($date_str);
        return $date->format('d/m/Y H:i');
    }
}
?>

<div class="container-fluid py-5 bg-dark text-white" style="min-height: 100vh;">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold">Calificar Tarea</h2>
                <p class="text-muted mb-0"><?php echo htmlspecialchars($tarea['titulo']); ?></p>
            </div>

            <div>
                <a href="index.php?controller=Aula&action=ver_tarea&id=<?php echo $tarea['id']; ?>"
                    class="btn btn-outline-light me-2">
                    <i class="fas fa-eye me-1"></i> Ver Tarea
                </a>
                <a href="index.php?controller=Aula&action=index&id=<?php echo $curso_id; ?>"
                    class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver al Curso
                </a>
            </div>
        </div>

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
                                <th>Comentario Estudiante</th>
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
                                            <?php echo format_date_grading($ent['fecha_entrega']); ?>
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

                                            <input type="hidden" name="redirect_view" value="calificar_tarea">

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

    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
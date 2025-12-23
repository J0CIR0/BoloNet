<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .aula-header {
        background-color: #1a1d20;
        border-bottom: 1px solid #2c3034;
        padding-top: 2rem;
        padding-bottom: 0;
        margin-bottom: 2rem;
        color: #e0e0e0;
    }

    .modal-content {
        background-color: #212529;
        color: #fff;
        border: 1px solid #495057;
    }

    .modal-header {
        border-bottom: 1px solid #495057;
    }

    .modal-footer {
        border-top: 1px solid #495057;
    }

    .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    .form-control,
    .form-select {
        background-color: #2c3034;
        border: 1px solid #495057;
        color: #e0e0e0;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: #2c3034;
        border-color: #198754;
        color: #fff;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }

    .nav-tabs .nav-link {
        border: none;
        color: #adb5bd;
        padding-bottom: 1rem;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        color: #198754;
        border-bottom: 3px solid #198754;
        background: transparent;
    }

    .modulo-card {
        border: 1px solid #2c3034;
        background-color: #212529;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .modulo-header {
        background-color: #1a1d20;
        padding: 1rem;
        border-bottom: 1px solid #2c3034;
        cursor: pointer;
    }

    .modulo-header:hover {
        background-color: #2c3034;
    }

    .modulo-header h5 {
        color: #f8f9fa !important;
    }

    .recurso-item {
        padding: 1rem;
        border-bottom: 1px solid #2c3034;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        transition: background 0.2s;
        background-color: #212529;
        color: #e0e0e0;
    }

    @media (min-width: 768px) {
        .recurso-item {
            flex-direction: row;
            align-items: center;
        }
    }

    .recurso-item:last-child {
        border-bottom: none;
    }

    .recurso-item:hover {
        background-color: #2c3034;
    }

    .recurso-icon {
        width: 40px;
        height: 40px;
        background-color: #343a40;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
        color: #e0e0e0;
    }

    @media (min-width: 768px) {
        .recurso-icon {
            margin-right: 1rem;
            margin-bottom: 0;
        }
    }

    .recurso-item a.text-dark {
        color: #f8f9fa !important;
    }

    .recurso-item .text-muted {
        color: #adb5bd !important;
    }

    @media (min-width: 768px) {
        .w-md-auto {
            width: auto !important;
        }
    }
</style>

<div class="aula-header">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-center mb-3 text-center text-md-start">
            <div class="me-md-3 mb-2 mb-md-0">
                <img src="https://picsum.photos/seed/<?php echo $cursoData['id']; ?>/60/60" class="rounded" alt="Logo">
            </div>
            <div>
                <h2 class="mb-0 fw-bold"><?php echo htmlspecialchars($cursoData['nombre']); ?></h2>
                <p class="text-muted mb-0 small"><?php echo htmlspecialchars($cursoData['codigo']); ?></p>
            </div>
        </div>

        <ul class="nav nav-tabs justify-content-center justify-content-md-start" id="aulaTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="curso-tab" data-bs-toggle="tab" data-bs-target="#curso-content"
                    type="button" role="tab" aria-selected="true">
                    Curso
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="calificaciones-tab" data-bs-toggle="tab"
                    data-bs-target="#calificaciones-content" type="button" role="tab" aria-selected="false">
                    Calificaciones
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="container py-4">
    <div class="tab-content" id="aulaTabContent">

        <div class="tab-pane fade show active" id="curso-content" role="tabpanel">

            <?php if (isset($cursoFinalizado) && $cursoFinalizado): ?>
                <div class="alert alert-dark border border-secondary shadow-sm mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <i class="fas fa-flag-checkered fa-2x me-3 text-secondary"></i>
                            <span class="h5 mb-0 align-middle">Curso Finalizado</span>
                        </div>
                        <?php if (isset($isAprobado) && $isAprobado): ?>
                            <a href="index.php?controller=Aula&action=certificado&id=<?php echo $id_curso; ?>" target="_blank"
                                class="btn btn-warning fw-bold w-100 w-md-auto">
                                <i class="fas fa-certificate"></i> Descargar Certificado
                            </a>
                        <?php else: ?>
                            <span class="badge bg-secondary">Finalizado: <?php echo $cursoData['fecha_fin']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($puedeInscribirse) && $puedeInscribirse && !$esProfesor && empty($estaInscrito)): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-7 mb-3 mb-md-0 text-center text-md-start">
                            <h5 class="alert-heading mb-1"><i class="fas fa-user-plus me-2"></i>¡Únete al Curso!</h5>
                            <p class="mb-1 small">Al participar, aparecerás en la lista del profesor y podrás entregar
                                tareas para ser calificado.</p>
                            <p class="mb-0 small text-muted">
                                <i class="far fa-calendar-alt"></i> El curso finaliza el:
                                <strong><?php echo date('d/m/Y', strtotime($cursoData['fecha_fin'])); ?></strong>
                            </p>
                        </div>
                        <div class="col-md-5 text-center text-md-end">
                            <button type="button" class="btn btn-success fw-bold px-4 w-100 w-md-auto"
                                data-bs-toggle="modal" data-bs-target="#modalConfirmarParticipacion">
                                Participar en el Curso
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($inscripcionesCerradas) && $inscripcionesCerradas): ?>
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Modo Oyente:</strong> El curso ha avanzado más del 50%. Puedes acceder al contenido pero no se
                    aceptan nuevas inscripciones.
                </div>
            <?php endif; ?>

            <?php if ($esProfesor): ?>
                <div class="mb-4 text-end">
                    <button class="btn btn-outline-success btn-sm w-100 w-md-auto" data-bs-toggle="modal"
                        data-bs-target="#modalNuevoModulo">
                        <i class="fas fa-plus"></i> Nuevo Módulo
                    </button>
                </div>
            <?php endif; ?>

            <?php if (empty($modulos)): ?>
                <div class="alert alert-info text-center bg-dark text-white border-secondary">
                    <i class="fas fa-info-circle fa-2x mb-3"></i><br>
                    Este curso aún no tiene contenido publicado.
                </div>
            <?php else: ?>
                <div class="accordion" id="accordionModulos">
                    <?php foreach ($modulos as $index => $mod): ?>
                        <?php 
                            // Lógica de Bloqueo: Bloquear si no es premium y no es el primer módulo (índice 0)
                            $isLocked = !$isPremiumAccess && $index > 0; 
                        ?>
                        <div class="modulo-card shadow-sm position-relative">
                            
                            <!-- Header del Módulo -->
                            <div class="modulo-header d-flex justify-content-between align-items-center"
                                <?php if (!$isLocked): ?>
                                    data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $mod['id']; ?>"
                                    aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                    style="cursor: pointer;"
                                <?php else: ?>
                                    style="cursor: not-allowed; opacity: 0.7;"
                                <?php endif; ?>
                                >
                                <h5 class="mb-0 fw-bold text-dark text-break">
                                    <?php if ($isLocked): ?>
                                        <i class="fas fa-lock text-muted me-2"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($mod['titulo']); ?>
                                    
                                    <?php if ($esProfesor): ?>
                                        <button class="btn btn-sm btn-outline-warning ms-2"
                                            onclick="event.stopPropagation(); prepararEditarModulo(<?php echo $mod['id']; ?>, '<?php echo addslashes($mod['titulo']); ?>', '<?php echo addslashes($mod['descripcion']); ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    <?php endif; ?>
                                </h5>
                                <?php if (!$isLocked): ?>
                                    <i class="fas fa-chevron-down text-muted"></i>
                                <?php endif; ?>
                            </div>

                            <!-- Contenido del Módulo -->
                            <div id="collapse<?php echo $mod['id']; ?>" 
                                 class="collapse <?php echo ($index === 0 && !$isLocked) ? 'show' : ''; ?>"
                                 data-bs-parent="#accordionModulos">
                                <div class="bg-dark position-relative">
                                    
                                    <?php if ($isLocked): ?>
                                        <!-- Overlay de Bloqueo -->
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center" 
                                             style="background: rgba(33, 37, 41, 0.85); z-index: 10; backdrop-filter: blur(4px);">
                                            <i class="fas fa-lock fa-3x text-warning mb-3"></i>
                                            <h4 class="text-white fw-bold">Contenido Bloqueado</h4>
                                            <p class="text-white-50 mb-3 text-center px-3">Suscríbete para acceder a todo el curso.</p>
                                            <a href="index.php?controller=Pago&action=planes" class="btn btn-warning fw-bold pulse-animation">
                                                <i class="fas fa-crown me-2"></i> Desbloquear Curso
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($mod['descripcion'])): ?>
                                        <div class="p-3 text-muted small bg-dark border-bottom border-secondary">
                                            <?php echo htmlspecialchars($mod['descripcion']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php foreach ($mod['contenidos'] as $rec): ?>
                                        <div class="recurso-item">
                                            <div
                                                class="d-flex flex-column flex-md-row align-items-start align-items-md-center w-100">
                                                <div class="recurso-icon flex-shrink-0">
                                                    <?php if ($rec['tipo'] == 'video'): ?><i class="fas fa-play text-danger"></i>
                                                    <?php elseif ($rec['tipo'] == 'archivo'): ?><i
                                                            class="fas fa-file-pdf text-danger"></i>
                                                    <?php else: ?><i class="fas fa-link text-primary"></i><?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1 w-100">
                                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                        <a href="<?php echo htmlspecialchars($rec['url_recurso']); ?>"
                                                            target="_blank"
                                                            class="text-decoration-none text-white fw-bold text-break">
                                                            <?php echo htmlspecialchars($rec['titulo']); ?>
                                                        </a>
                                                        <?php if ($esProfesor): ?>
                                                            <button class="btn btn-sm text-warning p-0 ms-2"
                                                                onclick="prepararEditarContenido(<?php echo $rec['id']; ?>, '<?php echo addslashes($rec['titulo']); ?>', '<?php echo addslashes($rec['url_recurso']); ?>', '<?php echo addslashes($rec['descripcion']); ?>')">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if ($rec['descripcion']): ?>
                                                        <p class="mb-0 text-muted small text-break">
                                                            <?php echo htmlspecialchars($rec['descripcion']); ?>
                                                        </p>
                                                    <?php endif; ?>

                                                    <!-- Video Embed Logic moved here for correct alignment -->
                                                    <?php if ($rec['tipo'] == 'video' && !empty($rec['url_recurso'])):
                                                        $video_url = $rec['url_recurso'];
                                                        $embed_code = '';

                                                        if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                                                            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match);
                                                            if (isset($match[1])) {
                                                                $embed_url = 'https://www.youtube.com/embed/' . $match[1];
                                                                $embed_code = '<div class="ratio ratio-16x9 mt-4 rounded overflow-hidden shadow-lg w-100 w-md-75 mx-auto border border-secondary"><iframe src="' . $embed_url . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
                                                            }
                                                        } elseif (strpos($video_url, 'vimeo.com') !== false) {
                                                            preg_match('/vimeo\.com\/([0-9]+)/', $video_url, $match);
                                                            if (isset($match[1])) {
                                                                $embed_url = 'https://player.vimeo.com/video/' . $match[1];
                                                                $embed_code = '<div class="ratio ratio-16x9 mt-4 rounded overflow-hidden shadow-lg w-100 w-md-75 mx-auto border border-secondary"><iframe src="' . $embed_url . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe></div>';
                                                            }
                                                        } elseif (preg_match('/\.(mp4|webm|ogg)$/i', $video_url)) {
                                                            $embed_code = '<div class="mt-4 w-100 w-md-75 mx-auto"><video controls class="w-100 rounded shadow-lg border border-secondary" style="max-height: 500px;"><source src="' . htmlspecialchars($video_url) . '">Tu navegador no soporta el elemento de video.</video></div>';
                                                        }

                                                        if ($embed_code) {
                                                            echo $embed_code;
                                                        } else {
                                                            echo '<div class="mt-3 w-100 w-md-50 mx-auto"><a href="' . htmlspecialchars($video_url) . '" target="_blank" class="btn btn-outline-danger btn-sm w-100"><i class="fas fa-external-link-alt"></i> Ver Video Original</a></div>';
                                                        }
                                                        ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>


                                        </div>
                                    <?php endforeach; ?>

                                    <?php foreach ($mod['tareas'] as $tarea): ?>
                                        <div class="recurso-item bg-dark border-start border-4 border-warning">
                                            <div class="d-flex flex-row align-items-center w-100">
                                                <div class="recurso-icon bg-warning bg-opacity-10 text-warning flex-shrink-0">
                                                    <i class="fas fa-tasks"></i>
                                                </div>
                                                <div
                                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center w-100">
                                                    <div class="mb-2 mb-md-0">
                                                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($tarea['titulo']); ?>
                                                        </h6>
                                                        <small class="text-muted d-block d-md-inline">Vence:
                                                            <?php echo $tarea['fecha_entrega']; ?></small>
                                                        <?php if ($esProfesor): ?>
                                                            <span class="badge bg-warning text-dark ms-0 ms-md-2">Por calificar</span>
                                                        <?php endif; ?>
                                                    </div>

                                                    <?php if ($gradingEnabled): ?>
                                                        <a href="index.php?controller=Aula&action=ver_tarea&id=<?php echo $tarea['id']; ?>"
                                                            class="btn btn-outline-light w-100 w-md-auto mt-2 mt-md-0 px-4">
                                                            <i class="fas fa-eye me-1"></i> Ver Tarea
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-outline-secondary w-100 w-md-auto mt-2 mt-md-0 px-4"
                                                            disabled>
                                                            <i class="fas fa-lock"></i> Modo Oyente
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php if ($esProfesor): ?>
                                        <div class="p-2 text-center border-top bg-light">
                                            <button class="btn btn-sm btn-link text-success text-decoration-none"
                                                onclick="prepararModalContenido(<?php echo $mod['id']; ?>)">
                                                <i class="fas fa-plus-circle"></i> Agregar Recurso
                                            </button>
                                            <button class="btn btn-sm btn-link text-warning text-decoration-none ms-3"
                                                onclick="prepararModalTarea(<?php echo $mod['id']; ?>)">
                                                <i class="fas fa-tasks"></i> Nueva Tarea
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<div class="tab-pane fade" id="calificaciones-content" role="tabpanel">

    <?php if ($esProfesor): ?>
        <div class="card bg-dark border-secondary mb-4">
            <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white"><i class="fas fa-users-cog me-2"></i> Gestión de Estudiantes</h5>
                <span class="badge bg-primary">Total: <?php echo count($inscritos ?? []); ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr class="text-secondary text-uppercase small">
                                <th>Estudiante</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Nota Final</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Obtener inscritos (Lógica simplificada en Vista, idealmente en Controller)
                            // Usaremos una variable provista por el controlador
                            if (!empty($inscritos)):
                                foreach ($inscritos as $est):
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-white">
                                                <?php echo htmlspecialchars($est['nombre'] . ' ' . $est['apellido']); ?>
                                            </div>
                                            <div class="small text-muted"><?php echo htmlspecialchars($est['ci']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($est['email']); ?></td>
                                        <td>
                                            <?php if ($est['estado'] === 'aprobado'): ?>
                                                <span class="badge bg-success">Aprobado</span>
                                                <div class="small text-success mt-1"><i class="fas fa-check-circle"></i> Certificado
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Cursando</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $est['nota_final'] ?? '-'; ?></td>
                                        <td class="text-end">
                                            <?php if ($est['estado'] !== 'aprobado'): ?>
                                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                                    data-bs-target="#modalAprobar<?php echo $est['usuario_id']; ?>">
                                                    <i class="fas fa-award me-1"></i> Aprobar
                                                </button>

                                                <!-- Modal Aprobar -->
                                                <div class="modal fade text-start" id="modalAprobar<?php echo $est['usuario_id']; ?>"
                                                    tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <form class="modal-content" method="POST"
                                                            action="index.php?controller=Aula&action=aprobar_estudiante">
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title">Aprobar Estudiante</h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-dark">
                                                                <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">
                                                                <input type="hidden" name="estudiante_id"
                                                                    value="<?php echo $est['usuario_id']; ?>">

                                                                <p>¿Deseas aprobar a
                                                                    <strong><?php echo htmlspecialchars($est['nombre']); ?></strong>?
                                                                </p>
                                                                <p class="small text-muted">Esto generará automáticamente su certificado
                                                                    de finalización.</p>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Nota Final (0-100)</label>
                                                                    <input type="number" name="nota_final" class="form-control" required
                                                                        min="0" max="100" value="100">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-success fw-bold">Confirmar
                                                                    Aprobación</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            <?php else: ?>
                                                <a href="index.php?controller=Aula&action=certificado&id=<?php echo $id_curso; ?>"
                                                    target="_blank" class="btn btn-sm btn-warning disabled" title="Ya aprobado">
                                                    <i class="fas fa-certificate"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No hay estudiantes inscritos aún.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- VISTA ESTUDIANTE: Mis Calificaciones -->
    <?php endif; ?>

    <div class="card bg-dark border-secondary">
        <div class="card-header border-secondary">
            <h5 class="mb-0 text-white">Reporte de Actividades</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Actividad</th>
                            <th>Módulo</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Nota</th>
                            <th>Retroalimentación</th>
                            <?php if ($esProfesor): ?><th>Acción</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($calificacionesData)): ?>
                            <?php foreach ($calificacionesData as $cal): ?>
                                <?php
                                // Estado para Estudiante
                                $estado = '<span class="badge bg-secondary">Pendiente</span>';
                                $nota = '-';
                                $feedback = '-';

                                if (isset($cal['entrega']) && $cal['entrega']) {
                                    $archivoLink = '';
                                    if (!empty($cal['entrega']['archivo_url'])) {
                                        $archivoLink = ' <a href="' . $cal['entrega']['archivo_url'] . '" target="_blank" class="text-info" title="Ver Archivo"><i class="fas fa-paperclip"></i></a>';
                                    }

                                    if ($cal['entrega']['calificacion'] !== null) {
                                        $estado = '<span class="badge bg-success">Calificado</span>' . $archivoLink;
                                        $nota = $cal['entrega']['calificacion'] . ' / ' . ($cal['puntaje_maximo'] ?? 0);
                                        $feedback = $cal['entrega']['retroalimentacion'] ? $cal['entrega']['retroalimentacion'] : 'Sin comentarios';
                                    } else {
                                        $estado = '<span class="badge bg-info text-dark">Entregado</span>' . $archivoLink;
                                    }
                                } else {
                                    if (strtotime($cal['fecha_entrega']) < time()) {
                                        $estado = '<span class="badge bg-danger">Vencido</span>';
                                    }
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-break"><?php echo htmlspecialchars($cal['titulo']); ?></div>
                                    </td>
                                    <td class="small text-muted"><?php echo htmlspecialchars($cal['modulo_titulo']); ?></td>
                                    <td class="small"><?php echo $cal['fecha_entrega']; ?></td>
                                    <td><?php echo $estado; ?></td>
                                    <td class="fw-bold text-success"><?php echo $nota; ?></td>
                                    <td class="small text-muted text-break"><?php echo htmlspecialchars($feedback); ?></td>
                                    <?php if ($esProfesor): ?>
                                        <td class="text-end">
                                            <a href="index.php?controller=Aula&action=ver_calificaciones_tarea&id=<?php echo $cal['id']; ?>" 
                                               class="btn btn-sm btn-warning fw-bold text-dark">
                                                <i class="fas fa-edit"></i> Calificar
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-clipboard-check mb-2 fa-2x"></i><br>
                                    No hay tareas asignadas en este curso.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>
</div>

<?php if ($esProfesor): ?>
    <div class="modal fade" id="modalNuevoModulo" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="index.php?controller=Aula&action=crear_modulo"
                onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = 'Creando...';">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Módulo / Tema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">
                    <div class="mb-3">
                        <label class="form-label">Título del Módulo</label>
                        <input type="text" name="titulo" class="form-control" required
                            placeholder="Ej: Introducción a HTML">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción (Opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Crear Módulo</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoContenido" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="index.php?controller=Aula&action=crear_contenido"
                enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Recurso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">
                    <input type="hidden" name="modulo_id" id="inputModuloId">

                    <div class="mb-3">
                        <label class="form-label">Tipo de Recurso</label>
                        <select name="tipo" id="selectTipoRecurso" class="form-select" onchange="toggleRecursoInputs()">
                            <option value="enlace">Enlace Web / Link</option>
                            <option value="video">Video (YouTube/Vimeo)</option>
                            <option value="archivo">Archivo PDF</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>

                    <div class="mb-3" id="groupUrl">
                        <label class="form-label">URL / Enlace</label>
                        <input type="url" name="url" id="inputUrl" class="form-control" placeholder="https://...">
                    </div>

                    <div class="mb-3 d-none" id="groupArchivo">
                        <label class="form-label">Seleccionar Archivo (PDF)</label>
                        <input type="file" name="archivo_pdf" id="inputArchivo" class="form-control" accept=".pdf">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar Recurso</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalNuevaTarea" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="index.php?controller=Aula&action=crear_tarea">
                <div class="modal-header">
                    <h5 class="modal-title">Asignar Nueva Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">
                    <input type="hidden" name="modulo_id" id="inputModuloIdTarea">

                    <div class="mb-3">
                        <label class="form-label">Título de la Tarea</label>
                        <input type="text" name="titulo" class="form-control" required placeholder="Ej: Práctica 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción / Instrucciones</label>
                        <textarea name="descripcion" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Entrega</label>
                            <input type="datetime-local" name="fecha_entrega" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Puntaje Máximo</label>
                            <input type="number" name="puntaje" class="form-control" value="100" min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Publicar Tarea</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalEditarModulo" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="index.php?controller=Aula&action=editar_modulo">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Módulo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_modulo_id">
                    <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" id="edit_modulo_titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit_modulo_desc" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalEditarContenido" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="index.php?controller=Aula&action=editar_contenido">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Recurso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_content_id">
                    <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" id="edit_content_titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL / Enlace</label>
                        <input type="text" name="url_recurso" id="edit_content_url" class="form-control">
                        <small class="text-muted">Para archivos subidos, la URL es interna.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit_content_desc" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function prepararModalContenido(moduloId) {
            document.getElementById('inputModuloId').value = moduloId;
            var myModal = new bootstrap.Modal(document.getElementById('modalNuevoContenido'));
            myModal.show();
            toggleRecursoInputs();
        }

        function prepararModalTarea(moduloId) {
            document.getElementById('inputModuloIdTarea').value = moduloId;
            var myModal = new bootstrap.Modal(document.getElementById('modalNuevaTarea'));
            myModal.show();
        }

        function prepararEditarModulo(id, titulo, descripcion) {
            document.getElementById('edit_modulo_id').value = id;
            document.getElementById('edit_modulo_titulo').value = titulo;
            document.getElementById('edit_modulo_desc').value = descripcion;
            var myModal = new bootstrap.Modal(document.getElementById('modalEditarModulo'));
            myModal.show();
        }

        function prepararEditarContenido(id, titulo, url, descripcion) {
            document.getElementById('edit_content_id').value = id;
            document.getElementById('edit_content_titulo').value = titulo;
            document.getElementById('edit_content_url').value = url;
            document.getElementById('edit_content_desc').value = descripcion;
            var myModal = new bootstrap.Modal(document.getElementById('modalEditarContenido'));
            myModal.show();
        }

        function toggleRecursoInputs() {
            const tipo = document.getElementById('selectTipoRecurso').value;
            const groupUrl = document.getElementById('groupUrl');
            const groupArchivo = document.getElementById('groupArchivo');
            const inputUrl = document.getElementById('inputUrl');
            const inputArchivo = document.getElementById('inputArchivo');

            if (tipo === 'archivo') {
                groupUrl.classList.add('d-none');
                groupArchivo.classList.remove('d-none');
                inputUrl.removeAttribute('required');
                inputArchivo.setAttribute('required', 'required');
            } else {
                groupUrl.classList.remove('d-none');
                groupArchivo.classList.add('d-none');
                inputUrl.setAttribute('required', 'required');
                inputArchivo.removeAttribute('required');
            }
        }
    </script>
<?php endif; ?>

<div class="modal fade" id="modalConfirmarParticipacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-user-check"></i> Confirmar Inscripción</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas participar en este curso?</p>
                <ul class="text-muted small">
                    <li>Aparecerás en la lista de estudiantes del profesor.</li>
                    <li>Podrás enviar tareas y recibir calificaciones.</li>
                    <li>Debes cumplir con las fechas de entrega establecidas.</li>
                </ul>
                <p class="mb-0"><strong>Nota:</strong> Esta acción no tiene costo adicional a tu suscripción.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="index.php?controller=Aula&action=participar" method="POST">
                    <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">
                    <button type="submit" class="btn btn-success fw-bold">Sí, Participar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
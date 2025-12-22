<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Dark Theme for Modals */
    .aula-header {
        background-color: #1a1d20; /* Darker header */
        border-bottom: 1px solid #2c3034;
        padding-top: 2rem;
        padding-bottom: 0;
        margin-bottom: 2rem;
        color: #e0e0e0;
    }

    .modal-content {
        background-color: #212529; /* Dark bg */
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

    .form-control, .form-select {
        background-color: #2c3034;
        border: 1px solid #495057;
        color: #e0e0e0;
    }

    .form-control:focus, .form-select:focus {
        background-color: #2c3034;
        border-color: #198754; /* Green focus */
        color: #fff;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
    
    /* General Styles */
    .nav-tabs .nav-link { /* Unchanged but context needed */
        border: none;
        color: #adb5bd; /* Lighter grey */
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
        align-items: center;
        transition: background 0.2s;
        background-color: #212529;
        color: #e0e0e0;
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
        margin-right: 1rem;
        color: #e0e0e0;
    }

    /* Links inside content */
    .recurso-item a.text-dark {
        color: #f8f9fa !important;
    }
    .recurso-item .text-muted {
        color: #adb5bd !important;
    }
</style>

<!-- HEADER DEL CURSO -->
<div class="aula-header">
    <div class="container">
        <div class="d-flex align-items-center mb-3">
            <!-- Imagen pequeña del curso si quisieras -->
            <div class="me-3">
                <img src="https://picsum.photos/seed/<?php echo $cursoData['id']; ?>/60/60" class="rounded" alt="Logo">
            </div>
            <div>
                <h2 class="mb-0 fw-bold"><?php echo htmlspecialchars($cursoData['nombre']); ?></h2>
                <p class="text-muted mb-0 small"><?php echo htmlspecialchars($cursoData['codigo']); ?></p>
            </div>
        </div>

        <!-- PESTAÑAS (NAVEGACIÓN) -->
        <ul class="nav nav-tabs" id="aulaTabs" role="tablist">
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
            <!-- Omitimos Participantes y Competencias como pediste -->
        </ul>
    </div>
</div>

<div class="container py-4">
    <div class="tab-content" id="aulaTabContent">

        <!-- PESTAÑA 1: CONTENIDO DEL CURSO -->
        <div class="tab-pane fade show active" id="curso-content" role="tabpanel">

            <!-- ESTADO DEL CURSO Y ACCIONES -->
            
            <!-- 1. Curso Finalizado -->
            <?php if (isset($cursoFinalizado) && $cursoFinalizado): ?>
                <div class="alert alert-dark border border-secondary shadow-sm mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-flag-checkered fa-2x me-3 text-secondary"></i>
                            <span class="h5 mb-0 align-middle">Curso Finalizado</span>
                        </div>
                        <?php if (isset($isAprobado) && $isAprobado): ?>
                            <a href="index.php?controller=Aula&action=certificado&id=<?php echo $id_curso; ?>" target="_blank" 
                               class="btn btn-warning fw-bold">
                                <i class="fas fa-certificate"></i> Descargar Certificado
                            </a>
                        <?php else: ?>
                            <span class="badge bg-secondary">Finalizado: <?php echo $cursoData['fecha_fin']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 2. Botón Participar (Si puede inscribirse y NO es profesor) -->
            <?php if (isset($puedeInscribirse) && $puedeInscribirse && !$esProfesor && empty($estaInscrito)): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h5 class="alert-heading mb-1"><i class="fas fa-user-plus me-2"></i>¡Únete al Curso!</h5>
                            <p class="mb-1 small">Al participar, aparecerás en la lista del profesor y podrás entregar tareas para ser calificado.</p>
                            <p class="mb-0 small text-muted">
                                <i class="far fa-calendar-alt"></i> El curso finaliza el: <strong><?php echo date('d/m/Y', strtotime($cursoData['fecha_fin'])); ?></strong>
                            </p>
                        </div>
                        <div class="col-md-5 text-end">
                            <button type="button" class="btn btn-success fw-bold px-4" data-bs-toggle="modal" data-bs-target="#modalConfirmarParticipacion">
                                Participar en el Curso
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 3. Alert para Oyentes (Inscripciones Cerradas) -->
            <?php if (isset($inscripcionesCerradas) && $inscripcionesCerradas): ?>
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Modo Oyente:</strong> El curso ha avanzado más del 50%. Puedes acceder al contenido pero no se aceptan nuevas inscripciones para calificación.
                </div>
            <?php endif; ?>

            <!-- 4. Ya participando (Opcional, feedback positivo) -->
            <?php if (isset($estaInscrito) && $estaInscrito && !isset($cursoFinalizado)): ?>
                <!-- <div class="badge bg-success mb-3"><i class="fas fa-check"></i> Participando</div> -->
            <?php endif; ?>

            <!-- Botón Agregar Módulo (Solo Profesor) -->
            <?php if ($esProfesor): ?>
                <div class="mb-4 text-end">
                    <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalNuevoModulo">
                        <i class="fas fa-plus"></i> Nuevo Módulo
                    </button>
                </div>
            <?php endif; ?>

            <!-- LISTADO DE MÓDULOS -->
            <?php if (empty($modulos)): ?>
                <div class="alert alert-info text-center bg-dark text-white border-secondary">
                    <i class="fas fa-info-circle fa-2x mb-3"></i><br>
                    Este curso aún no tiene contenido publicado.
                </div>
            <?php else: ?>
                <div class="accordion" id="accordionModulos">
                    <?php foreach ($modulos as $index => $mod): ?>
                        <div class="modulo-card shadow-sm">
                            <div class="modulo-header d-flex justify-content-between align-items-center"
                                data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $mod['id']; ?>"
                                aria-expanded="true">
                                <h5 class="mb-0 fw-bold text-dark">
                                    <?php echo htmlspecialchars($mod['titulo']); ?>

                                    <?php if ($esProfesor): ?>
                                        <button class="btn btn-sm btn-outline-warning ms-2" 
                                                onclick="event.stopPropagation(); prepararEditarModulo(<?php echo $mod['id']; ?>, '<?php echo addslashes($mod['titulo']); ?>', '<?php echo addslashes($mod['descripcion']); ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    <?php endif; ?>
                                </h5>
                                <i class="fas fa-chevron-down text-muted"></i>
                            </div>

                            <div id="collapse<?php echo $mod['id']; ?>" class="collapse show"
                                data-bs-parent="#accordionModulos">
                                <div class="bg-dark">
                                    <?php if (!empty($mod['descripcion'])): ?>
                                        <div class="p-3 text-muted small bg-dark border-bottom border-secondary">
                                            <?php echo htmlspecialchars($mod['descripcion']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- RECURSOS/CONTENIDOS -->
                                    <?php foreach ($mod['contenidos'] as $rec): ?>
                                        <div class="recurso-item d-block">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="recurso-icon">
                                                    <?php if ($rec['tipo'] == 'video'): ?><i class="fas fa-play text-danger"></i>
                                                    <?php elseif ($rec['tipo'] == 'archivo'): ?><i
                                                            class="fas fa-file-pdf text-danger"></i>
                                                    <?php else: ?><i class="fas fa-link text-primary"></i><?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                     <div class="d-flex justify-content-between align-items-center">
                                                        <a href="<?php echo htmlspecialchars($rec['url_recurso']); ?>" target="_blank"
                                                            class="text-decoration-none text-white fw-bold">
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
                                                        <p class="mb-0 text-muted small">
                                                            <?php echo htmlspecialchars($rec['descripcion']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Reproductor de Video Embebido -->
                                            <?php if ($rec['tipo'] == 'video' && !empty($rec['url_recurso'])): 
                                                $video_url = $rec['url_recurso'];
                                                $embed_code = ''; // HTML final del player

                                                // 1. YouTube
                                                if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                                                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match);
                                                    if (isset($match[1])) {
                                                        $embed_url = 'https://www.youtube.com/embed/' . $match[1];
                                                        $embed_code = '<div class="ratio ratio-16x9 mt-3 rounded overflow-hidden shadow-lg"><iframe src="' . $embed_url . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
                                                    }
                                                // 2. Vimeo
                                                } elseif (strpos($video_url, 'vimeo.com') !== false) {
                                                    preg_match('/vimeo\.com\/([0-9]+)/', $video_url, $match);
                                                    if (isset($match[1])) {
                                                        $embed_url = 'https://player.vimeo.com/video/' . $match[1];
                                                        $embed_code = '<div class="ratio ratio-16x9 mt-3 rounded overflow-hidden shadow-lg"><iframe src="' . $embed_url . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe></div>';
                                                    }
                                                // 3. Archivo Directo (MP4, WEBM, OGG)
                                                } elseif (preg_match('/\.(mp4|webm|ogg)$/i', $video_url)) {
                                                    $embed_code = '<div class="mt-3"><video controls class="w-100 rounded shadow-lg" style="max-height: 500px;"><source src="' . htmlspecialchars($video_url) . '">Tu navegador no soporta el elemento de video.</video></div>';
                                                }
                                                
                                                // Renderizar si se generó código
                                                if ($embed_code) {
                                                    echo $embed_code;
                                                } else {
                                                    // Fallback link si no se reconoció
                                                    echo '<div class="mt-2"><a href="' . htmlspecialchars($video_url) . '" target="_blank" class="btn btn-outline-danger btn-sm"><i class="fas fa-external-link-alt"></i> Ver Video Original</a></div>';
                                                }
                                            ?>
                                            <?php endif; ?>

                                        </div>
                                    <?php endforeach; ?>

                                    <!-- TAREAS -->
                                    <?php foreach ($mod['tareas'] as $tarea): ?>
                                        <div class="recurso-item bg-dark border-start border-4 border-warning">
                                            <div class="recurso-icon bg-warning bg-opacity-10 text-warning">
                                                <i class="fas fa-tasks"></i>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                            <div>
                                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($tarea['titulo']); ?></h6>
                                                <small class="text-muted">Vence: <?php echo $tarea['fecha_entrega']; ?></small>
                                                <?php if($esProfesor): ?>
                                                    <span class="badge bg-warning text-dark ms-2">Por calificar</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if ($gradingEnabled): ?>
                                            <?php if ($gradingEnabled): ?>
                                                <a href="index.php?controller=Aula&action=ver_tarea&id=<?php echo $tarea['id']; ?>" 
                                                   class="btn btn-outline-light btn-sm">
                                                    <i class="fas fa-eye me-1"></i> Ver Tarea
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                                    <i class="fas fa-lock"></i> Modo Oyente
                                                </button>
                                            <?php endif; ?>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                                    <i class="fas fa-lock"></i> Modo Oyente
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <!-- Botones Agregar Contenido (Solo Profesor) -->
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

        <!-- PESTAÑA 2: CALIFICACIONES -->
        <div class="tab-pane fade" id="calificaciones-content" role="tabpanel">
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
                                                // Si ya venció
                                                if (strtotime($cal['fecha_entrega']) < time()) {
                                                    $estado = '<span class="badge bg-danger">Vencido</span>';
                                                }
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?php echo htmlspecialchars($cal['titulo']); ?></div>
                                            </td>
                                            <td class="small text-muted"><?php echo htmlspecialchars($cal['modulo_titulo']); ?></td>
                                            <td class="small"><?php echo $cal['fecha_entrega']; ?></td>
                                            <td><?php echo $estado; ?></td>
                                            <td class="fw-bold text-success"><?php echo $nota; ?></td>
                                            <td class="small text-muted"><?php echo htmlspecialchars($feedback); ?></td>
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

<!-- MODAL NUEVO MÓDULO -->
<?php if ($esProfesor): ?>
    <div class="modal fade" id="modalNuevoModulo" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="index.php?controller=Aula&action=crear_modulo">
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

    <!-- MODAL NUEVO CONTENIDO (Genérico para Video/URL/Archivo) -->
    <div class="modal fade" id="modalNuevoContenido" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="index.php?controller=Aula&action=crear_contenido" enctype="multipart/form-data">
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

                    <!-- Input URL -->
                    <div class="mb-3" id="groupUrl">
                        <label class="form-label">URL / Enlace</label>
                        <input type="url" name="url" id="inputUrl" class="form-control" placeholder="https://...">
                    </div>

                    <!-- Input Archivo -->
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

    <!-- MODAL NUEVA TAREA -->
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

<!-- MODAL EDITAR MÓDULO -->
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

<!-- MODAL EDITAR CONTENIDO -->
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
                    <small class="text-muted">Para archivos subidos, la URL es interna (no cambiar a menos que sepa).</small>
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
    
    // --- NUEVAS FUNCIONES ---
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

    function verTarea(id, titulo, descripcion, fecha, puntaje) {
        // Fallback for old calls if any
        document.getElementById('tarea_titulo').innerText = titulo;
        document.getElementById('tarea_descripcion').innerText = descripcion;
        document.getElementById('tarea_fecha').innerText = fecha;
        document.getElementById('tarea_puntaje').innerText = puntaje + ' pts';
        var inputId = document.getElementById('tarea_id_input');
        if(inputId) inputId.value = id;
        var myModal = new bootstrap.Modal(document.getElementById('modalVerTarea'));
        myModal.show();
    }

    function verTareaFromButton(btn) {
        var id = btn.getAttribute('data-id');
        var titulo = btn.getAttribute('data-titulo');
        var descripcion = btn.getAttribute('data-descripcion');
        var fecha = btn.getAttribute('data-fecha');
        var puntaje = btn.getAttribute('data-puntaje');

        document.getElementById('tarea_titulo').innerText = titulo;
        document.getElementById('tarea_descripcion').innerText = descripcion;
        document.getElementById('tarea_fecha').innerText = fecha;
        document.getElementById('tarea_puntaje').innerText = puntaje + ' pts';
        
        var inputId = document.getElementById('tarea_id_input');
        if(inputId) inputId.value = id;
        
        // Modal is toggled by data-bs-toggle, but we can ensure it's updated. 
        // Logic handled by BS5 automatically for toggle, but we populated fields first.
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

<!-- MODAL CONFIRMAR PARTICIPACIÓN (Estudiantes) -->
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
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
                <div class="alert alert-info text-center">
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
                                </h5>
                                <i class="fas fa-chevron-down text-muted"></i>
                            </div>

                            <div id="collapse<?php echo $mod['id']; ?>" class="collapse show"
                                data-bs-parent="#accordionModulos">
                                <div class="bg-dark">
                                    <?php if (!empty($mod['descripcion'])): ?>
                                        <div class="p-3 text-muted small bg-light border-bottom">
                                            <?php echo htmlspecialchars($mod['descripcion']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- RECURSOS/CONTENIDOS -->
                                    <?php foreach ($mod['contenidos'] as $rec): ?>
                                        <div class="recurso-item">
                                            <div class="recurso-icon">
                                                <?php if ($rec['tipo'] == 'video'): ?><i class="fas fa-play text-danger"></i>
                                                <?php elseif ($rec['tipo'] == 'archivo'): ?><i
                                                        class="fas fa-file-pdf text-danger"></i>
                                                <?php else: ?><i class="fas fa-link text-primary"></i><?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <a href="<?php echo htmlspecialchars($rec['url_recurso']); ?>" target="_blank"
                                                    class="text-decoration-none text-dark fw-bold">
                                                    <?php echo htmlspecialchars($rec['titulo']); ?>
                                                </a>
                                                <?php if ($rec['descripcion']): ?>
                                                    <p class="mb-0 text-muted small">
                                                        <?php echo htmlspecialchars($rec['descripcion']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <!-- TAREAS -->
                                    <?php foreach ($mod['tareas'] as $tarea): ?>
                                        <div class="recurso-item bg-light border-start border-4 border-warning">
                                            <div class="recurso-icon bg-warning bg-opacity-25 text-warning">
                                                <i class="fas fa-tasks"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <a href="#" class="text-decoration-none text-dark fw-bold">
                                                    <?php echo htmlspecialchars($tarea['titulo']); ?>
                                                </a>
                                                <p class="mb-0 text-muted small">
                                                    Vence: <?php echo $tarea['fecha_entrega']; ?>
                                                    <?php if ($esProfesor): ?>
                                                        <span class="badge bg-warning text-dark ms-2">Por calificar</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-dark">Ver Tarea</button>
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

        <!-- PESTAÑA 2: CALIFICACIONES (Placeholder) -->
        <div class="tab-pane fade" id="calificaciones-content" role="tabpanel">
            <h4 class="mb-4">Calificaciones</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Actividad</th>
                            <th>Estado</th>
                            <th>Calificación</th>
                            <th>Retroalimentación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No tienes calificaciones registradas aún.
                            </td>
                        </tr>
                    </tbody>
                </table>
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

    <!-- MODAL NUEVO CONTENIDO (Genérico para Video/URL) -->
    <div class="modal fade" id="modalNuevoContenido" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="index.php?controller=Aula&action=crear_contenido">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Recurso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">
                    <input type="hidden" name="modulo_id" id="inputModuloId">

                    <div class="mb-3">
                        <label class="form-label">Tipo de Recurso</label>
                        <select name="tipo" class="form-select">
                            <option value="enlace">Enlace Web / Link</option>
                            <option value="video">Video (YouTube/Vimeo)</option>
                            <option value="archivo">Archivo PDF (Link Drive/Cloud)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL / Enlace</label>
                        <input type="url" name="url" class="form-control" required placeholder="https://...">
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

    <script>
        function prepararModalContenido(moduloId) {
            document.getElementById('inputModuloId').value = moduloId;
            var myModal = new bootstrap.Modal(document.getElementById('modalNuevoContenido'));
            myModal.show();
        }

        function prepararModalTarea(moduloId) {
            document.getElementById('inputModuloIdTarea').value = moduloId;
            var myModal = new bootstrap.Modal(document.getElementById('modalNuevaTarea'));
            myModal.show();
        }
    </script>
<?php endif; ?>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-5 bg-dark text-white" style="min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-bold neon-text"><?php echo htmlspecialchars($tarea['titulo']); ?></h2>
            <a href="index.php?controller=Aula&action=index&id=<?php echo $curso_id; ?>" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Volver al Curso
            </a>
        </div>

        <div class="row">
            <!-- Columna Izquierda: Información de Tarea -->
            <div class="col-lg-8 mb-4">
                <div class="card bg-secondary bg-opacity-10 border-secondary shadow-lg">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-primary fs-6">Puntos: <?php echo $tarea['puntaje_maximo']; ?>
                                pts</span>
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="far fa-calendar-alt"></i> Vence: <?php echo $tarea['fecha_entrega']; ?>
                            </span>
                        </div>

                        <h5 class="text-info mb-3">Instrucciones:</h5>
                        <p class="lead" style="font-size: 1.1rem; line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($tarea['descripcion'])); ?>
                        </p>
                    </div>
                </div>

                <!-- Retroalimentación (Si existe) -->
                <?php if ($entrega && $entrega['calificacion'] !== null): ?>
                    <div class="card mt-4 border-success bg-success bg-opacity-10">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="fas fa-check-circle"></i> Calificación y Retroalimentación
                        </div>
                        <div class="card-body">
                            <h3 class="display-6 fw-bold"><?php echo $entrega['calificacion']; ?> /
                                <?php echo $tarea['puntaje_maximo']; ?></h3>
                            <?php if (!empty($entrega['retroalimentacion'])): ?>
                                <hr>
                                <h6 class="fw-bold">Comentarios del Profesor:</h6>
                                <p><?php echo nl2br(htmlspecialchars($entrega['retroalimentacion'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Columna Derecha: Entrega -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg gradient-custom-card">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 text-center">
                        <h4 class="text-white mb-0">Tu Entrega</h4>
                    </div>
                    <div class="card-body p-4">

                        <?php if ($entrega): ?>
                            <!-- Estado Entregado -->
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="fas fa-check-circle fa-4x text-success"></i>
                                </div>
                                <h5 class="text-success fw-bold">¡Tarea Entregada!</h5>
                                <p class="text-white-50 small">Enviado el: <?php echo $entrega['fecha_entrega']; ?></p>

                                <?php if ($entrega['archivo_url']): ?>
                                    <a href="<?php echo $entrega['archivo_url']; ?>" target="_blank"
                                        class="btn btn-outline-light w-100 mb-2">
                                        <i class="fas fa-download me-2"></i> Ver Archivo Subido
                                    </a>
                                <?php endif; ?>

                                <div class="alert alert-dark mt-3 text-start small border-secondary">
                                    <strong>Tu comentario:</strong><br>
                                    <?php echo htmlspecialchars($entrega['comentario']); ?>
                                </div>
                            </div>

                            <?php if ($entrega['calificacion'] === null): ?>
                                <!-- Opción de Re-subir (Editar entrega) si aun no califican y no venció (Opcional, lo dejamos simple por ahora solo aviso) -->
                                <button class="btn btn-secondary w-100" disabled>Esperando Calificación</button>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- Formulario de Entrega -->
                            <?php
                            $vencido = strtotime($tarea['fecha_entrega']) < time();
                            ?>

                            <?php if ($vencido): ?>
                                <div class="text-center">
                                    <i class="fas fa-clock fa-4x text-danger mb-3"></i>
                                    <h5 class="text-danger">La fecha límite ha pasado.</h5>
                                    <p class="text-white-50">Ya no puedes entregar esta tarea.</p>
                                </div>
                            <?php else: ?>
                                <form action="index.php?controller=Aula&action=subir_tarea" method="POST"
                                    enctype="multipart/form-data">
                                    <input type="hidden" name="tarea_id" value="<?php echo $tarea['id']; ?>">
                                    <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">

                                    <div class="mb-3">
                                        <label class="form-label text-white-50">Adjuntar Archivo</label>
                                        <input type="file" name="archivo_tarea"
                                            class="form-control bg-dark text-white border-secondary" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-white-50">Comentario (Opcional)</label>
                                        <textarea name="comentario" class="form-control bg-dark text-white border-secondary"
                                            rows="3"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold hover-scale">
                                        <i class="fas fa-paper-plane me-2"></i> Enviar Tarea
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale {
        transition: transform 0.2s;
    }

    .hover-scale:hover {
        transform: scale(1.02);
    }

    .gradient-custom-card {
        background: linear-gradient(145deg, #1f2937, #111827);
    }

    .neon-text {
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-success"><i class="fas fa-network-wired"></i> Monitor de Sesiones Activas</h2>
            <p class="text-muted mb-0">Vista en tiempo real de usuarios conectados y sus dispositivos.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-dark border border-success text-success p-2" id="last-update">
                <i class="fas fa-sync fa-spin me-1"></i> Actualizando...
            </span>
        </div>
    </div>
</div>

<div id="session-container">
    <?php
    // Cargar la vista parcial inicialmente
    require __DIR__ . '/partials/session_list.php';
    ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('session-container');
        const badge = document.getElementById('last-update');

        function updateSessions() {
            fetch('index.php?controller=Session&action=refresh')
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    const now = new Date();
                    badge.innerHTML = '<i class="fas fa-check me-1"></i> Actualizado: ' + now.toLocaleTimeString();
                    badge.classList.remove('text-warning');
                    badge.classList.add('text-success');
                })
                .catch(error => {
                    console.error('Error actualizando sesiones:', error);
                    badge.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Error conexion';
                    badge.classList.remove('text-success');
                    badge.classList.add('text-warning');
                });
        }

        // Actualizar cada 3 segundos
        setInterval(updateSessions, 3000);
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-success"><i class="fas fa-network-wired"></i> Monitor de Sesiones Activas</h2>
        <p class="text-muted">Vista en tiempo real de usuarios conectados y sus dispositivos.</p>
    </div>
</div>

<?php if (empty($groupedSessions)): ?>
    <div class="alert alert-info">No hay sesiones activas en este momento (aparte de la tuya si no se muestra).</div>
<?php else: ?>
    <div class="row">
        <?php foreach ($groupedSessions as $userId => $data): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-success">
                    <div class="card-header bg-dark text-success d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold">
                                <?php echo htmlspecialchars($data['user_data']['nombre'] . ' ' . $data['user_data']['apellido']); ?>
                            </h5>
                            <small class="text-muted"><?php echo htmlspecialchars($data['user_data']['email']); ?></small>
                        </div>
                        <span
                            class="badge bg-<?php echo ($data['user_data']['plan_type'] == 'premium') ? 'warning' : 'secondary'; ?>">
                            <?php echo strtoupper($data['user_data']['plan_type']); ?>
                        </span>
                    </div>
                    <div class="card-body bg-dark text-light p-0">
                        <ul class="list-group list-group-flush bg-transparent">
                            <?php foreach ($data['sessions'] as $s): ?>
                                <li
                                    class="list-group-item bg-dark text-light border-secondary d-flex justify-content-between align-items-center">
                                    <div style="font-size: 0.85rem; max-width: 80%;">
                                        <div><strong>IP:</strong> <?php echo htmlspecialchars($s['ip_address']); ?></div>
                                        <div class="text-truncate" title="<?php echo htmlspecialchars($s['user_agent']); ?>">
                                            <i class="fas fa-desktop"></i>
                                            <?php echo htmlspecialchars(substr($s['user_agent'], 0, 30)) . '...'; ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <i class="fas fa-clock"></i> <?php echo $s['last_activity']; ?>
                                        </div>
                                    </div>
                                    <a href="index.php?controller=Session&action=revoke&id=<?php echo $s['session_id']; ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('¿Seguro que deseas cerrar esta sesión?');" title="Cerrar Sesión">
                                        <i class="fas fa-power-off"></i>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-footer bg-dark border-success">
                        <small class="text-muted">Total Sesiones:
                            <strong><?php echo count($data['sessions']); ?></strong></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
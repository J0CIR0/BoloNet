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
    <?php
    function parseUserAgent($ua)
    {
        $platform = 'Desconocido';
        $browser = 'Desconocido';
        $deviceType = 'desktop'; // desktop, mobile, tablet

        // Detectar Plataforma
        if (preg_match('/linux/i', $ua))
            $platform = 'Linux';
        elseif (preg_match('/macintosh|mac os x/i', $ua))
            $platform = 'Mac';
        elseif (preg_match('/windows|win32/i', $ua))
            $platform = 'Windows';
        elseif (preg_match('/android/i', $ua)) {
            $platform = 'Android';
            $deviceType = 'mobile';
        } elseif (preg_match('/iphone/i', $ua)) {
            $platform = 'iPhone';
            $deviceType = 'mobile';
        } elseif (preg_match('/ipad/i', $ua)) {
            $platform = 'iPad';
            $deviceType = 'tablet';
        }

        // Detectar Navegador
        if (preg_match('/MSIE/i', $ua) && !preg_match('/Opera/i', $ua))
            $browser = 'Internet Explorer';
        elseif (preg_match('/Firefox/i', $ua))
            $browser = 'Firefox';
        elseif (preg_match('/Chrome/i', $ua))
            $browser = 'Chrome';
        elseif (preg_match('/Safari/i', $ua))
            $browser = 'Safari';
        elseif (preg_match('/Opera/i', $ua))
            $browser = 'Opera';
        elseif (preg_match('/Netscape/i', $ua))
            $browser = 'Netscape';
        elseif (preg_match('/Edge/i', $ua))
            $browser = 'Edge';
        elseif (preg_match('/Edg/i', $ua))
            $browser = 'Edge';

        return ['platform' => $platform, 'browser' => $browser, 'type' => $deviceType];
    }
    ?>

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
                            <?php foreach ($data['sessions'] as $s):
                                $info = parseUserAgent($s['user_agent']);
                                $icon = 'fa-desktop';
                                if ($info['type'] == 'mobile')
                                    $icon = 'fa-mobile-alt';
                                if ($info['type'] == 'tablet')
                                    $icon = 'fa-tablet-alt';
                                ?>
                                <li
                                    class="list-group-item bg-dark text-light border-secondary d-flex justify-content-between align-items-center">
                                    <div style="font-size: 0.9rem; width: 85%;">
                                        <div class="mb-1">
                                            <i class="fas <?php echo $icon; ?> me-1 text-info"></i>
                                            <strong><?php echo $info['platform']; ?></strong> - <?php echo $info['browser']; ?>
                                        </div>
                                        <div class="small">
                                            <strong>IP:</strong> <?php echo htmlspecialchars($s['ip_address']); ?>
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">
                                            <i class="fas fa-clock"></i> Última act: <?php echo $s['last_activity']; ?>
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
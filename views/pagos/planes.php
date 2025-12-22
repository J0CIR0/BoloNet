<?php
$title = 'Seleccionar Plan';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Elige tu Plan de Suscripción</h1>
        <p class="lead text-muted">Accede a todos nuestros cursos con un solo pago mensual.</p>
    </div>

    <?php
    $plans = [
        'basic' => ['level' => 1, 'name' => 'Básico', 'price' => '$9.99', 'btn_class' => 'btn-outline-primary', 'header_class' => '', 'border_class' => ''],
        'pro' => ['level' => 2, 'name' => 'Pro', 'price' => '$19.99', 'btn_class' => 'btn-primary', 'header_class' => 'text-white bg-primary border-primary', 'border_class' => 'border-primary'],
        'premium' => ['level' => 3, 'name' => 'Premium', 'price' => '$29.99', 'btn_class' => 'btn-success', 'header_class' => 'text-white bg-success border-success', 'border_class' => 'border-success']
    ];

    $currentPlan = $_SESSION['plan_type'] ?? '';
    $currentStatus = $_SESSION['subscription_status'] ?? 'inactive';
    $currentLevel = 0;

    if ($currentStatus === 'active' && isset($plans[$currentPlan])) {
        $currentLevel = $plans[$currentPlan]['level'];
    }
    ?>

    <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
        <div class="col">
            <div class="card mb-4 rounded-3 shadow-sm <?php echo $plans['basic']['border_class']; ?>">
                <div class="card-header py-3 <?php echo $plans['basic']['header_class']; ?>">
                    <h4 class="my-0 fw-normal">Básico</h4>
                </div>
                <div class="card-body">
                    <h1 class="card-title pricing-card-title">$9.99<small class="text-muted fw-light">/mes</small></h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li>Acceso a todos los cursos</li>
                        <li>1 Dispositivo simultáneo</li>
                        <li>Soporte por email</li>
                        <li>Certificado de finalización</li>
                    </ul>
                    <?php if ($currentLevel == 1): ?>
                        <button class="w-100 btn btn-lg btn-secondary" disabled>Tu Plan Actual</button>
                    <?php elseif ($currentLevel > 1): ?>
                        <button class="w-100 btn btn-lg btn-outline-secondary" disabled>No disponible</button>
                    <?php else: ?>
                        <a href="index.php?controller=Pago&action=checkout&plan=basic"
                            class="w-100 btn btn-lg btn-outline-primary">Seleccionar Básico</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card mb-4 rounded-3 shadow-sm <?php echo $plans['pro']['border_class']; ?>">
                <div class="card-header py-3 <?php echo $plans['pro']['header_class']; ?>">
                    <h4 class="my-0 fw-normal">Pro</h4>
                </div>
                <div class="card-body">
                    <h1 class="card-title pricing-card-title">$19.99<small class="text-muted fw-light">/mes</small></h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li>Acceso a todos los cursos</li>
                        <li><strong>3 Dispositivos simultáneos</strong></li>
                        <li>Soporte prioritario</li>
                        <li>Descargas offline</li>
                    </ul>
                    <?php if ($currentLevel == 2): ?>
                        <button class="w-100 btn btn-lg btn-secondary" disabled>Tu Plan Actual</button>
                    <?php elseif ($currentLevel > 2): ?>
                        <button class="w-100 btn btn-lg btn-outline-secondary" disabled>No disponible</button>
                    <?php else: ?>
                        <a href="index.php?controller=Pago&action=checkout&plan=pro" class="w-100 btn btn-lg btn-primary">
                            <?php echo ($currentLevel > 0) ? 'Mejorar a Pro' : 'Seleccionar Pro'; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card mb-4 rounded-3 shadow-sm <?php echo $plans['premium']['border_class']; ?>">
                <div class="card-header py-3 <?php echo $plans['premium']['header_class']; ?>">
                    <h4 class="my-0 fw-normal">Premium</h4>
                </div>
                <div class="card-body">
                    <h1 class="card-title pricing-card-title">$29.99<small class="text-muted fw-light">/mes</small></h1>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li>Acceso ilimitado total</li>
                        <li><strong>5 Dispositivos simultáneos</strong></li>
                        <li>Tutoría personalizada 1h/mes</li>
                        <li>Acceso anticipado a contenido</li>
                    </ul>
                    <?php if ($currentLevel == 3): ?>
                        <button class="w-100 btn btn-lg btn-secondary" disabled>Tu Plan Actual</button>
                    <?php else: ?>
                        <a href="index.php?controller=Pago&action=checkout&plan=premium"
                            class="w-100 btn btn-lg btn-success">
                            <?php echo ($currentLevel > 0) ? 'Mejorar a Premium' : 'Seleccionar Premium'; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
$title = 'Seleccionar Plan';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Elige tu Plan de Suscripción</h1>
        <p class="lead text-muted">Accede a todos nuestros cursos con un solo pago mensual.</p>
    </div>

    <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
        <!-- PLAN BÁSICO -->
        <div class="col">
            <div class="card mb-4 rounded-3 shadow-sm">
                <div class="card-header py-3">
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
                    <a href="index.php?controller=Pago&action=checkout&plan=basic"
                        class="w-100 btn btn-lg btn-outline-primary">Seleccionar Básico</a>
                </div>
            </div>
        </div>

        <!-- PLAN PRO -->
        <div class="col">
            <div class="card mb-4 rounded-3 shadow-sm border-primary">
                <div class="card-header py-3 text-white bg-primary border-primary">
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
                    <a href="index.php?controller=Pago&action=checkout&plan=pro"
                        class="w-100 btn btn-lg btn-primary">Seleccionar Pro</a>
                </div>
            </div>
        </div>

        <!-- PLAN PREMIUM -->
        <div class="col">
            <div class="card mb-4 rounded-3 shadow-sm border-success">
                <div class="card-header py-3 text-white bg-success border-success">
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
                    <a href="index.php?controller=Pago&action=checkout&plan=premium"
                        class="w-100 btn btn-lg btn-success">Seleccionar Premium</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
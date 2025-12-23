<?php
$title = 'Seleccionar Plan';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    :root {
        --card-bg: #212529;
        --card-border: #2c3034;
        --text-main: #e0e0e0;
        --text-muted: #adb5bd;
        --accent-basic: #0d6efd;
        --accent-pro: #8a2be2;
        --accent-premium: #ffc107;
    }

    body {
        background-color: #121416;
    }

    .plans-header {
        margin-bottom: 4rem;
    }

    .plans-title {
        font-size: 2.5rem;
        background: linear-gradient(45deg, #fff, #adb5bd);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .plan-card {
        background-color: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 16px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .plan-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .plan-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, currentColor, transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .plan-card:hover::before {
        opacity: 1;
    }

    .plan-basic {
        color: var(--accent-basic);
    }

    .plan-basic:hover {
        box-shadow: 0 10px 30px rgba(13, 110, 253, 0.15);
    }

    .plan-pro {
        color: var(--accent-pro);
        border-color: rgba(138, 43, 226, 0.3);
    }

    .plan-pro:hover {
        box-shadow: 0 10px 40px rgba(138, 43, 226, 0.25);
        border-color: var(--accent-pro);
    }

    .plan-premium {
        color: var(--accent-premium);
        border-color: rgba(255, 193, 7, 0.3);
    }

    .plan-premium:hover {
        box-shadow: 0 10px 40px rgba(255, 193, 7, 0.2);
        border-color: var(--accent-premium);
    }

    .badge-popular {
        position: absolute;
        top: 20px;
        right: -35px;
        background: var(--accent-pro);
        color: white;
        padding: 5px 40px;
        transform: rotate(45deg);
        font-size: 0.8rem;
        font-weight: bold;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        z-index: 2;
    }

    .card-header-custom {
        padding: 2rem 1.5rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .plan-name {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
        color: #fff;
    }

    .plan-price {
        font-size: 3.5rem;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }

    .plan-period {
        font-size: 1rem;
        color: var(--text-muted);
        font-weight: 400;
    }

    .card-body-custom {
        padding: 2rem 1.5rem;
        flex-grow: 1;
    }

    .feature-list li {
        margin-bottom: 1rem;
        color: var(--text-main);
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .feature-icon {
        margin-right: 12px;
        font-size: 1.1rem;
    }

    .check-included {
        color: #2fb344;
    }

    .check-excluded {
        color: #495057;
    }

    .btn-plan {
        padding: 12px 20px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
        width: 100%;
        margin-top: auto;
    }

    .btn-plan:hover {
        transform: translateY(-2px);
        filter: brightness(1.1);
    }

    .btn-basic {
        background: transparent;
        border: 2px solid var(--accent-basic);
        color: var(--accent-basic);
    }

    .btn-basic:hover {
        background: var(--accent-basic);
        color: white;
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
    }

    .btn-pro {
        background: linear-gradient(45deg, #8a2be2, #d63384);
        color: white;
        box-shadow: 0 5px 15px rgba(138, 43, 226, 0.4);
    }

    .btn-pro:hover {
        box-shadow: 0 8px 25px rgba(138, 43, 226, 0.6);
    }

    .btn-premium {
        background: linear-gradient(45deg, #ffc107, #ff9800);
        color: #000;
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
    }

    .btn-premium:hover {
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.6);
    }

    .btn-current {
        background: #343a40;
        color: #adb5bd;
        cursor: default;
    }

    .btn-current:hover {
        transform: none;
        filter: none;
    }
</style>

<div class="container py-5">
    <div class="text-center plans-header animate__animated animate__fadeInDown">
        <h1 class="plans-title fw-bold mb-3">Elige tu Nivel de Acceso</h1>
        <p class="lead text-secondary mx-auto" style="max-width: 600px;">
            Desbloquea todo el potencial de tu aprendizaje con nuestros planes flexibles.
            Sin compromisos a largo plazo.
        </p>
    </div>

    <?php
    $plans = [
        'basic' => ['level' => 1, 'name' => 'Básico', 'price' => '9.99', 'class' => 'plan-basic', 'btn' => 'btn-basic'],
        'pro' => ['level' => 2, 'name' => 'Pro', 'price' => '19.99', 'class' => 'plan-pro', 'btn' => 'btn-pro'],
        'premium' => ['level' => 3, 'name' => 'Premium', 'price' => '29.99', 'class' => 'plan-premium', 'btn' => 'btn-premium']
    ];

    $currentPlan = $_SESSION['plan_type'] ?? '';
    $currentStatus = $_SESSION['subscription_status'] ?? 'inactive';
    $currentLevel = 0;

    if ($currentStatus === 'active' && isset($plans[$currentPlan])) {
        $currentLevel = $plans[$currentPlan]['level'];
    }
    ?>

    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">

        <div class="col animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="plan-card plan-basic">
                <div class="card-header-custom text-center">
                    <div class="plan-name">BÁSICO</div>
                    <div class="plan-price">$9.99<span class="plan-period">/mes</span></div>
                </div>
                <div class="card-body-custom d-flex flex-column">
                    <ul class="list-unstyled feature-list">
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> Acceso a todos los cursos
                        </li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> 1 Dispositivo simultáneo
                        </li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> Soporte por email</li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> Certificado de finalización
                        </li>
                        <li class="text-muted"><i class="fas fa-times-circle feature-icon check-excluded"></i> Descargas
                            offline</li>
                        <li class="text-muted"><i class="fas fa-times-circle feature-icon check-excluded"></i> Tutoría
                            personalizada</li>
                    </ul>

                    <div class="mt-auto">
                        <?php if ($currentLevel == 1): ?>
                            <button class="btn btn-plan btn-current" disabled><i class="fas fa-check me-2"></i>Tu Plan
                                Actual</button>
                        <?php elseif ($currentLevel > 1): ?>
                            <button class="btn btn-plan btn-outline-secondary" disabled>Incluido en tu nivel</button>
                        <?php else: ?>
                            <a href="index.php?controller=Pago&action=checkout&plan=basic" class="btn btn-plan btn-basic">
                                Empezar Ahora
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="plan-card plan-pro">
                <div class="badge-popular">POPULAR</div>
                <div class="card-header-custom text-center">
                    <div class="plan-name">PRO</div>
                    <div class="plan-price">$19.99<span class="plan-period">/mes</span></div>
                </div>
                <div class="card-body-custom d-flex flex-column">
                    <ul class="list-unstyled feature-list">
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> <strong>Todo lo del
                                Básico</strong></li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> <strong>3
                                Dispositivos</strong> simultáneos</li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> Soporte prioritario 24/7
                        </li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> Descargas offline</li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> HD 1080p Streaming</li>
                        <li class="text-muted"><i class="fas fa-times-circle feature-icon check-excluded"></i> Tutoría
                            personalizada</li>
                    </ul>

                    <div class="mt-auto">
                        <?php if ($currentLevel == 2): ?>
                            <button class="btn btn-plan btn-current" disabled><i class="fas fa-check me-2"></i>Tu Plan
                                Actual</button>
                        <?php elseif ($currentLevel > 2): ?>
                            <button class="btn btn-plan btn-outline-secondary" disabled>Incluido en tu nivel</button>
                        <?php else: ?>
                            <a href="index.php?controller=Pago&action=checkout&plan=pro" class="btn btn-plan btn-pro">
                                <?php echo ($currentLevel > 0) ? 'Mejorar a Pro' : 'Seleccionar Pro'; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="plan-card plan-premium">
                <div class="card-header-custom text-center">
                    <div class="plan-name">PREMIUM</div>
                    <div class="plan-price">$29.99<span class="plan-period">/mes</span></div>
                </div>
                <div class="card-body-custom d-flex flex-column">
                    <ul class="list-unstyled feature-list">
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> <strong>Acceso Ilimitado
                                Total</strong></li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> <strong>5
                                Dispositivos</strong> simultáneos</li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> Tutoría personalizada 1h/mes
                        </li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> Acceso anticipado a
                            contenido</li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> Certificados Físicos</li>
                        <li><i class="fas fa-check-circle feature-icon check-included"></i> Badge de Perfil Exclusivo
                        </li>
                    </ul>

                    <div class="mt-auto">
                        <?php if ($currentLevel == 3): ?>
                            <button class="btn btn-plan btn-current" disabled><i class="fas fa-check me-2"></i>Tu Plan
                                Actual</button>
                        <?php else: ?>
                            <a href="index.php?controller=Pago&action=checkout&plan=premium"
                                class="btn btn-plan btn-premium">
                                <?php echo ($currentLevel > 0) ? 'Obtener Premium' : 'Seleccionar Premium'; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row justify-content-center mt-5 animate__animated animate__fadeIn" style="animation-delay: 0.5s;">
        <div class="col-md-8 text-center text-muted">
            <p><small><i class="fas fa-lock me-1"></i> Pagos seguros y encriptados. Puedes cancelar tu suscripción en
                    cualquier momento desde tu panel de usuario.</small></p>
            <div class="mt-3">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Stripe_Logo%2C_revised_2016.svg/2560px-Stripe_Logo%2C_revised_2016.svg.png"
                    alt="Stripe" style="height: 25px; opacity: 0.5;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/2560px-Visa_Inc._logo.svg.png"
                    alt="Visa" style="height: 20px; margin-left: 15px; opacity: 0.5;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1280px-Mastercard-logo.svg.png"
                    alt="Mastercard" style="height: 25px; margin-left: 15px; opacity: 0.5;">
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
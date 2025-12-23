<?php
$title = 'Finalizar Compra';
require_once __DIR__ . '/../layouts/header.php';
$version = time();
?>

<style>
    :root {
        --checkout-bg: #121416;
        --card-bg: rgba(33, 37, 41, 0.7);
        --accent-color: #2fb344;
        --text-main: #e0e0e0;
        --text-muted: #adb5bd;
    }

    body {
        background-color: var(--checkout-bg);
        background-image: radial-gradient(circle at 10% 20%, rgba(47, 179, 68, 0.05) 0%, transparent 20%),
            radial-gradient(circle at 90% 80%, rgba(47, 179, 68, 0.05) 0%, transparent 20%);
    }

    .checkout-section {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
    }

    .checkout-card {
        background: var(--card-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        max-width: 500px;
        width: 100%;
        animation: fadeInUp 0.6s ease-out;
    }

    .checkout-header {
        background: rgba(47, 179, 68, 0.1);
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid rgba(47, 179, 68, 0.2);
    }

    .checkout-header h4 {
        color: #fff;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .checkout-body {
        padding: 2.5rem;
        text-align: center;
    }

    .plan-info {
        margin-bottom: 2rem;
    }

    .plan-label {
        color: var(--text-muted);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }

    .plan-name {
        color: #fff;
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .price-tag {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.05);
        display: inline-block;
        width: 100%;
    }

    .total-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .total-amount {
        font-size: 2.5rem;
        color: var(--accent-color);
        font-weight: 700;
        line-height: 1;
    }

    .total-currency {
        font-size: 1rem;
        font-weight: 500;
        opacity: 0.8;
    }

    .divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        margin: 2rem 0;
    }

    .cancel-link {
        color: var(--text-muted);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 1.5rem;
    }

    .cancel-link:hover {
        color: #fff;
    }

    .secure-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.75rem;
        margin-top: 2rem;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="checkout-section">
    <div class="checkout-card">
        <div class="checkout-header">
            <h4><i class="fas fa-shopping-cart me-2"></i>Resumen de Compra</h4>
        </div>
        <div class="checkout-body">
            <div class="plan-info">
                <div class="plan-label">Estás por suscribirte al</div>
                <h2 class="plan-name"><?php echo htmlspecialchars($nombre_plan ?? 'Plan'); ?></h2>

                <div class="price-tag">
                    <div class="total-label">Total a pagar ahora</div>
                    <div class="total-amount">
                        $<?php echo number_format((float) ($precio ?? 0), 2); ?>
                        <span class="total-currency">USD</span>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <div id="paypal-button-container"></div>

            <a href="index.php?controller=Curso&action=index" class="cancel-link">
                <i class="fas fa-arrow-left me-2"></i> Cancelar y volver
            </a>

            <div class="secure-badge">
                <i class="fas fa-lock me-2"></i> Pagos procesados de forma segura por PayPal
            </div>
        </div>
    </div>
</div>

<script
    src="https://www.paypal.com/sdk/js?client-id=AQ_ew9c3lZ235TMP8f8rDpTkCNp6A6IGwrG-iTk0R35etuJNxSH-kS2MVVYQPaq0OlSvz15NRCIKHhzl&currency=USD"></script>

<script>
    console.log("--> Checkout Premium v<?php echo $version; ?> <--");

    const precioCurso = "<?php echo number_format((float) ($precio > 0 ? $precio : 1.00), 2, '.', ''); ?>";
    const planType = "<?php echo isset($plan_type) ? $plan_type : ''; ?>";
    const usuarioId = "<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>";

    paypal.Buttons({
        style: {
            layout: 'vertical',
            color: 'gold',
            shape: 'pill',
            label: 'pay'
        },
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    reference_id: 'plan_' + planType,
                    amount: { value: precioCurso }
                }]
            });
        },

        onApprove: function (data, actions) {
            console.log("1. Autorizado. Iniciando captura...");

            const btnContainer = document.getElementById('paypal-button-container');
            btnContainer.style.display = 'none';

            const msgContainer = document.createElement('div');
            msgContainer.id = 'checkout-message';
            msgContainer.innerHTML = `
                 <div class="text-center py-4">
                    <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;"></div>
                    <h5 class="text-white">Procesando pago...</h5>
                    <p class="text-muted small">Por favor no cierres esta ventana.</p>
                </div>
            `;
            btnContainer.parentNode.insertBefore(msgContainer, btnContainer);

            return actions.order.capture().then(function (details) {
                console.log("2. Captura completada. Dinero recibido.");

                msgContainer.innerHTML = `
                    <div class="alert alert-success border-0 bg-success bg-opacity-25 text-white">
                        <h4><i class="fas fa-check-circle"></i> ¡Pago Exitoso!</h4>
                        <p class="mb-0">Activando tu suscripción...</p>
                    </div>
                `;

                return fetch('index.php?controller=Pago&action=procesarPagoExitoso', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        orderID: data.orderID,
                        estado: details.status,
                        usuario_id: usuarioId,
                        plan_type: planType,
                        monto: precioCurso
                    })
                });
            })
                .then(response => response.text())
                .then(text => {
                    console.log("3. Respuesta Servidor:", text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error("El servidor no respondió JSON válido: " + text);
                    }
                })
                .then(data => {
                    if (data.status === 'success') {
                        setTimeout(() => {
                            window.location.href = "index.php?controller=Curso&action=mis_cursos";
                        }, 1500);
                    } else {
                        throw new Error(data.mensaje);
                    }
                })
                .catch(error => {
                    console.error("ERROR:", error);
                    const msgDiv = document.getElementById('checkout-message');
                    if (msgDiv) {
                        msgDiv.innerHTML = `
                        <div class="alert alert-danger bg-danger bg-opacity-10 border-danger text-danger">
                            <i class="fas fa-exclamation-circle me-2"></i> Error: ${error.message} <br>
                            <a href="javascript:location.reload()" class="text-danger fw-bold mt-2 d-inline-block">Reintentar</a>
                        </div>`;
                    }
                });
        },

        onCancel: function (data) {
            console.log("Pago cancelado", data);
        },
        onError: function (err) {
            console.error(err);
            const btnContainer = document.getElementById('paypal-button-container');
            const msgContainer = document.createElement('div');
            msgContainer.innerHTML = '<div class="alert alert-warning">Ocurrió un error con PayPal. Intenta de nuevo.</div>';
            btnContainer.parentNode.insertBefore(msgContainer, btnContainer);
        }

    }).render('#paypal-button-container');
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
$title = 'Inscribirse en Curso';
require_once __DIR__ . '/../../models/Curso.php';
$cursoModel = new Curso();
$cursos_activos = $cursoModel->getCursosActivos();
$cursos_inscritos = $cursoModel->getCursosInscritos($_SESSION['user_id']);
$cursos_inscritos_ids = array_column($cursos_inscritos, 'id');
$cursos_disponibles = array_filter($cursos_activos, function($curso) use ($cursos_inscritos_ids) {
    return !in_array($curso['id'], $cursos_inscritos_ids);
});
?>

<script src="https://www.paypal.com/sdk/js?client-id=TU_CLIENT_ID_AQUI&currency=USD"></script>

<div class="card">
    <div class="card-header"><h4 class="mb-0">Inscribirse en Curso</h4></div>
    <div class="card-body">
        <div class="row">
            <?php if(!empty($cursos_disponibles)): ?>
                <?php foreach($cursos_disponibles as $curso): ?>
                <div class="col-md-6 mb-3">
                    <div class="card bg-dark">
                        <div class="card-body">
                            <h5><?php echo htmlspecialchars($curso['nombre']); ?></h5>
                            <p><strong>Precio:</strong> $<?php echo number_format($curso['precio'] ?? 10.00, 2); ?> USD</p>
                            <div id="paypal-button-container-<?php echo $curso['id']; ?>"></div>
                        </div>
                    </div>
                </div>

                <script>
                  paypal.Buttons({
                    style: { layout: 'vertical', color: 'blue', shape: 'rect', label: 'pay' },
                    createOrder: function(data, actions) {
                      return actions.order.create({
                        purchase_units: [{
                          description: "Inscripción: <?php echo htmlspecialchars($curso['nombre']); ?>",
                          amount: { value: '<?php echo $curso['precio'] ?? 10.00; ?>' }
                        }]
                      });
                    },
                    onApprove: function(data, actions) {
                      return actions.order.capture().then(function(details) {
                        // Enviamos los datos al controlador mediante una petición POST
                        fetch('../../controllers/inscripcion_pago.php', {
                          method: 'POST',
                          headers: { 'Content-Type': 'application/json' },
                          body: JSON.stringify({
                            curso_id: <?php echo $curso['id']; ?>,
                            order_id: data.orderID,
                            monto: details.purchase_units[0].amount.value,
                            status: details.status
                          })
                        }).then(res => res.json()).then(res => {
                            if(res.success) {
                                alert('¡Pago exitoso! Ya estás inscrito.');
                                window.location.href = 'mis_cursos.php';
                            }
                        });
                      });
                    }
                  }).render('#paypal-button-container-<?php echo $curso['id']; ?>');
                </script>
                <?php endforeach; ?>
            <?php else: ?>
                <?php endif; ?>
        </div>
    </div>
</div>
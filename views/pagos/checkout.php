<?php
$title = 'Finalizar Compra';
require_once __DIR__ . '/../layouts/header.php'; 
// Generamos un número aleatorio para obligar a recargar el script
$version = time(); 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-shopping-cart"></i> Resumen de Compra</h4>
                </div>
                <div class="card-body text-center">
                    <h5 class="text-muted mb-3">Estás a un paso de inscribirte en:</h5>
                    <h3 class="card-title fw-bold mb-4"><?php echo htmlspecialchars($nombre_curso ?? 'Curso'); ?></h3>
                    
                    <div class="alert alert-light border">
                        <p class="mb-0">Total a pagar:</p>
                        <h2 class="text-success fw-bold">$<?php echo number_format((float)($precio ?? 0), 2); ?> USD</h2>
                    </div>

                    <hr class="my-4">
                    
                    <div id="paypal-button-container"></div>
                    
                    <div class="mt-3">
                        <a href="index.php?controller=Curso&action=index" class="text-secondary small">Cancelar y volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://www.paypal.com/sdk/js?client-id=AQ_ew9c3lZ235TMP8f8rDpTkCNp6A6IGwrG-iTk0R35etuJNxSH-kS2MVVYQPaq0OlSvz15NRCIKHhzl&currency=USD"></script>

<script>
    console.log("--> CARGANDO VERSION FINAL CORREGIDA v<?php echo $version; ?> <--");

    const precioCurso = "<?php echo number_format((float)($precio > 0 ? $precio : 1.00), 2, '.', ''); ?>";
    const cursoId = "<?php echo isset($id_curso) ? $id_curso : ''; ?>";
    const usuarioId = "<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>";

    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    reference_id: 'curso_' + cursoId,
                    amount: { value: precioCurso }
                }]
            });
        },

        onApprove: function(data, actions) {
            console.log("1. Autorizado. Iniciando captura...");
            
            // NO TOCAMOS EL HTML AQUI. DEJAMOS QUE PAYPAL TERMINE.
            
            return actions.order.capture().then(function(details) {
                console.log("2. Captura completada. Dinero recibido.");
                
                // AHORA SÍ: Actualizamos la pantalla
                const container = document.getElementById('paypal-button-container');
                container.innerHTML = `
                    <div class="alert alert-success">
                        <h4><i class="fas fa-check-circle"></i> ¡Pago Exitoso!</h4>
                        <p>Estamos registrando tu inscripción...</p>
                        <div class="spinner-border text-success"></div>
                    </div>
                `;

                // Enviamos a PHP
                return fetch('index.php?controller=Pago&action=procesarPagoExitoso', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        orderID: data.orderID,
                        estado: details.status,
                        usuario_id: usuarioId,
                        curso_id: cursoId,
                        monto: precioCurso
                    })
                });
            })
            .then(response => response.text()) // Leemos texto primero para ver errores PHP
            .then(text => {
                console.log("3. Respuesta Servidor:", text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error("El servidor no respondió JSON válido: " + text);
                }
            })
            .then(data => {
                if(data.status === 'success'){
                    // Redirigimos
                    window.location.href = "index.php?controller=Curso&action=mis_cursos";
                } else {
                    throw new Error(data.mensaje);
                }
            })
            .catch(error => {
                console.error("ERROR:", error);
                document.getElementById('paypal-button-container').innerHTML = `
                    <div class="alert alert-danger">
                        Error: ${error.message} <br>
                        <a href="javascript:location.reload()">Reintentar</a>
                    </div>
                `;
            });
        },
        
        onCancel: function(data) { alert("Pago cancelado"); },
        onError: function(err) { console.error(err); alert("Error de conexión PayPal"); }

    }).render('#paypal-button-container');
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
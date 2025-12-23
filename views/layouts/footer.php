<?php if (isset($_SESSION['user_id'])): ?>
    <script>
        // Verificación de Sesión Activa (Heartbeat)
        setInterval(function () {
            fetch('index.php?controller=Auth&action=checkSessionStatus')
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        if (data.reason === 'concurrent_login') {
                            alert('Has excedido el límite. Cerrando sesión en este dispositivo.');
                        }
                        window.location.href = 'index.php?controller=Auth&action=logout';
                    }
                })
                .catch(error => console.error('Error verificando sesión:', error));
        }, 2000); // Revisar cada 2 segundos

        // Notificar al servidor si cerramos la pestaña (Beacon)
        window.addEventListener('beforeunload', function () {
            const data = new FormData();
            data.append('closing_tab', 'true');
            navigator.sendBeacon('index.php?controller=Auth&action=checkSessionStatus&beacon=1', data);
        });
    </script>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
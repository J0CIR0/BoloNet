<?php if (isset($_SESSION['user_id'])): ?>
    <script>
        // Verificación de Sesión Activa (Heartbeat)
        setInterval(function () {
            fetch('index.php?controller=Auth&action=checkSessionStatus')
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        if (data.reason === 'concurrent_login') {
                            alert('Sesión cerrada: Sesiones simultáneas máximas alcanzadas. Mejora tu plan.');
                        }
                        window.location.href = 'index.php?controller=Auth&action=logout';
                    }
                })
                .catch(error => console.error('Error verificando sesión:', error));
        }, 5000); // Revisar cada 5 segundos
    </script>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
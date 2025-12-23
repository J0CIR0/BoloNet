<?php
require_once 'config/conexion.php';
require_once 'models/Database.php';

$db = Database::getConnection();

$sql = "CREATE TABLE IF NOT EXISTS session_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    device VARCHAR(255),
    ip_address VARCHAR(45),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuario(id) ON DELETE CASCADE
)";

if ($db->query($sql)) {
    echo "Tabla 'session_logs' creada o verificada exitosamente.";
} else {
    echo "Error al crear tabla: " . $db->error;
}
?>
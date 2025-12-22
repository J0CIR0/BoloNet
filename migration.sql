-- Actualizar tabla USUARIO para soporte de suscripciones
ALTER TABLE usuario ADD COLUMN plan_type ENUM('basic', 'pro', 'premium') DEFAULT NULL;
ALTER TABLE usuario ADD COLUMN subscription_status ENUM('active', 'inactive', 'cancelled') DEFAULT 'inactive';
ALTER TABLE usuario ADD COLUMN subscription_end TIMESTAMP NULL;

-- Tabla para Control de Sesiones Concurrentes
CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    user_agent VARCHAR(255) NULL,
    ip_address VARCHAR(45) NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuario(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id)
);

-- 1. Crear base de datos
DROP DATABASE IF EXISTS bolonet;
CREATE DATABASE bolonet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bolonet;

-- 2. Tabla rol
CREATE TABLE rol (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla permiso
CREATE TABLE permiso (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    modulo VARCHAR(50),
    descripcion VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabla rol_permiso_detalle
CREATE TABLE rol_permiso_detalle (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rol_id INT NOT NULL,
    permiso_id INT NOT NULL,
    FOREIGN KEY (rol_id) REFERENCES rol(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permiso(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rol_permiso (rol_id, permiso_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabla usuario (COMPLETA CON TODAS LAS COLUMNAS NECESARIAS)
CREATE TABLE usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(15),
    direccion TEXT,
    rol_id INT NOT NULL,
    estado TINYINT(1) DEFAULT 0 COMMENT '0=no verificado, 1=verificado',
    verification_token VARCHAR(64) NULL,
    token_expires DATETIME NULL,
    reset_token VARCHAR(6) NULL,
    reset_expires DATETIME NULL,
    password_history TEXT NULL COMMENT 'JSON con historial de contraseñas anteriores',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES rol(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Insertar datos iniciales de roles
INSERT INTO rol (nombre, descripcion) VALUES 
('administrador', 'Acceso completo al sistema'),
('profesor', 'Puede gestionar cursos'),
('estudiante', 'Puede ver cursos');

-- 7. Insertar permisos
INSERT INTO permiso (nombre, modulo, descripcion) VALUES 
('ver_dashboard', 'dashboard', 'Ver panel principal'),
('ver_usuarios', 'usuarios', 'Ver lista de usuarios'),
('crear_usuario', 'usuarios', 'Crear nuevo usuario'),
('editar_usuario', 'usuarios', 'Editar usuario existente'),
('eliminar_usuario', 'usuarios', 'Eliminar usuario'),
('ver_roles', 'roles', 'Ver lista de roles'),
('crear_rol', 'roles', 'Crear nuevo rol'),
('editar_rol', 'roles', 'Editar rol existente'),
('eliminar_rol', 'roles', 'Eliminar rol'),
('ver_permisos', 'permisos', 'Ver lista de permisos'),
('asignar_permisos', 'roles', 'Asignar permisos a roles');

-- 8. Asignar permisos a roles
INSERT INTO rol_permiso_detalle (rol_id, permiso_id) VALUES 
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5),
(1, 6), (1, 7), (1, 8), (1, 9), (1, 10), (1, 11);

INSERT INTO rol_permiso_detalle (rol_id, permiso_id) VALUES 
(2, 1), (2, 2);

INSERT INTO rol_permiso_detalle (rol_id, permiso_id) VALUES 
(3, 1);

-- 9. Insertar usuarios iniciales (contraseñas encriptadas posteriormente)
INSERT INTO usuario (nombre, apellido, email, password, rol_id, estado) VALUES 
('josue', 'claros roca', 'admin@bolonet.com', '123456', 1, 1),
('juan', 'perez', 'profesor@bolonet.com', '123456', 2, 1),
('maria', 'gonzalez', 'estudiante@bolonet.com', '123456', 3, 1);

-- 10. Crear índices para mejor rendimiento
CREATE INDEX idx_usuario_email ON usuario(email);
CREATE INDEX idx_usuario_estado ON usuario(estado);
CREATE INDEX idx_usuario_verification_token ON usuario(verification_token);
CREATE INDEX idx_usuario_reset_token ON usuario(reset_token);

-- 11. Mostrar estructura final
SHOW TABLES;

DESCRIBE usuario;
DESCRIBE rol;
DESCRIBE permiso;
DESCRIBE rol_permiso_detalle;
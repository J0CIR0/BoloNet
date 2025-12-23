CREATE DATABASE IF NOT EXISTS bolonet;
USE bolonet;

CREATE TABLE rol (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE permiso (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    modulo VARCHAR(50),
    descripcion VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE rol_permiso_detalle (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rol_id INT NOT NULL,
    permiso_id INT NOT NULL,
    FOREIGN KEY (rol_id) REFERENCES rol(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permiso(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rol_permiso (rol_id, permiso_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE persona (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ci VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    genero ENUM('M', 'F', 'O') NOT NULL,
    telefono VARCHAR(15),
    direccion TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    persona_id INT NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    estado TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(64) NULL,
    token_expires DATETIME NULL,
    reset_token VARCHAR(6) NULL,
    reset_expires DATETIME NULL,
    password_history TEXT NULL,
    conteo_interrupciones INT DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (persona_id) REFERENCES persona(id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES rol(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE curso (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    duracion_horas INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado ENUM('activo', 'inactivo', 'completado') DEFAULT 'activo',
    profesor_id INT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profesor_id) REFERENCES usuario(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inscripcion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    estudiante_id INT NOT NULL,
    curso_id INT NOT NULL,
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('inscrito', 'aprobado', 'reprobado', 'retirado') DEFAULT 'inscrito',
    nota_final DECIMAL(5,2) NULL,
    FOREIGN KEY (estudiante_id) REFERENCES usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES curso(id) ON DELETE CASCADE,
    UNIQUE KEY unique_inscripcion (estudiante_id, curso_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO rol (nombre, descripcion) VALUES 
('registro', 'Puede registrar en todas las tablas'),
('adm', 'Puede modificar y eliminar información'),
('estudiante', 'Puede ver información de usuarios y cursos'),
('profesor', 'Profesor de cursos');

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
('asignar_permisos', 'roles', 'Asignar permisos a roles'),
('ver_personas', 'personas', 'Ver lista de personas'),
('crear_persona', 'personas', 'Crear nueva persona'),
('editar_persona', 'personas', 'Editar persona existente'),
('eliminar_persona', 'personas', 'Eliminar persona'),
('ver_cursos', 'cursos', 'Ver lista de cursos'),
('crear_curso', 'cursos', 'Crear nuevo curso'),
('editar_curso', 'cursos', 'Editar curso existente'),
('eliminar_curso', 'cursos', 'Eliminar curso'),
('inscribir_curso', 'cursos', 'Inscribirse en cursos'),
('ver_inscripciones', 'cursos', 'Ver inscripciones'),
('gestionar_inscripciones', 'cursos', 'Gestionar inscripciones');

INSERT INTO rol_permiso_detalle (rol_id, permiso_id) VALUES 
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22);

INSERT INTO rol_permiso_detalle (rol_id, permiso_id) VALUES 
(2, 1),
(2, 2),
(2, 4),
(2, 5),
(2, 6),
(2, 8),
(2, 9),
(2, 10),
(2, 12),
(2, 14),
(2, 15),
(2, 16),
(2, 18),
(2, 19),
(2, 21),
(2, 22);

INSERT INTO rol_permiso_detalle (rol_id, permiso_id) VALUES 
(3, 1),
(3, 2),
(3, 12),
(3, 16),
(3, 21);

INSERT INTO rol_permiso_detalle (rol_id, permiso_id) VALUES 
(4, 1),
(4, 16),
(4, 21),
(4, 22);

INSERT INTO persona (ci, nombre, apellido, fecha_nacimiento, genero, telefono, direccion) VALUES 
('1234567', 'Admin', 'Sistema', '1990-01-01', 'M', '77711111', 'Dirección 1'),
('7654321', 'Administrador', 'Sistema', '1985-05-15', 'M', '77722222', 'Dirección 2'),
('9876543', 'Estudiante', 'Demo', '2000-03-20', 'F', '77733333', 'Dirección 3'),
('5555555', 'Carlos', 'Mendoza', '1980-08-25', 'M', '77755555', 'Av. Universitaria 123');

INSERT INTO usuario (persona_id, email, password, rol_id, estado) VALUES 
(1, 'registro@bolonet.com', '123456', 1, 1),
(2, 'adm@bolonet.com', '123456', 2, 1),
(3, 'estudiante@bolonet.com', '123456', 3, 1),
(4, 'profesor@bolonet.com', '123456', 4, 1);

INSERT INTO curso (codigo, nombre, descripcion, duracion_horas, fecha_inicio, fecha_fin, estado, profesor_id) VALUES 
('PROG101', 'Programación Básica', 'Introducción a la programación con Python', 60, '2024-01-15', '2024-03-15', 'activo', 4),
('WEB101', 'Desarrollo Web I', 'HTML, CSS y JavaScript básico', 80, '2024-02-01', '2024-04-01', 'activo', 4),
('BD101', 'Bases de Datos', 'SQL y diseño de bases de datos relacionales', 70, '2024-01-20', '2024-03-20', 'activo', 4);

INSERT INTO inscripcion (estudiante_id, curso_id, estado) VALUES 
(3, 1, 'inscrito');

CREATE INDEX idx_usuario_email ON usuario(email);
CREATE INDEX idx_usuario_estado ON usuario(estado);
CREATE INDEX idx_persona_ci ON persona(ci);
CREATE INDEX idx_curso_codigo ON curso(codigo);
CREATE INDEX idx_curso_estado ON curso(estado);
CREATE INDEX idx_inscripcion_estudiante ON inscripcion(estudiante_id);
CREATE INDEX idx_inscripcion_curso ON inscripcion(curso_id);

select * from curso;

-- ==========================================
-- ACTUALIZACIÓN PARA SUSCRIPCIONES (SaaS)
-- ==========================================

-- 1. Actualizar tabla USUARIO
ALTER TABLE usuario ADD COLUMN plan_type ENUM('basic', 'pro', 'premium') DEFAULT 'basic';
ALTER TABLE usuario ADD COLUMN subscription_status ENUM('active', 'inactive', 'cancelled') DEFAULT 'inactive';
ALTER TABLE usuario ADD COLUMN subscription_end TIMESTAMP NULL;

-- 2. Tabla para Control de Sesiones Concurrentes
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    user_agent VARCHAR(255) NULL,
    ip_address VARCHAR(45) NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuario(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Índices nuevos
CREATE INDEX idx_user_subscription ON usuario(subscription_status, plan_type);

-- 4. Tabla de Pagos (Faltante)
CREATE TABLE IF NOT EXISTS pago (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    curso_id INT NOT NULL DEFAULT 0, -- 0 si es suscripción
    transaccion_id VARCHAR(100) NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- ACTUALIZACIÓN PARA AULA VIRTUAL (LMS)
-- ==========================================

-- 5. Módulos/Temas del Curso
CREATE TABLE IF NOT EXISTS curso_modulo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    curso_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    orden INT DEFAULT 0,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (curso_id) REFERENCES curso(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tareas/Actividades
CREATE TABLE IF NOT EXISTS curso_tarea (
    id INT PRIMARY KEY AUTO_INCREMENT,
    modulo_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fecha_entrega DATETIME NULL,
    puntaje_maximo DECIMAL(5,2) DEFAULT 100.00,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (modulo_id) REFERENCES curso_modulo(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Contenidos/Recursos (Videos, PDF, Links)
CREATE TABLE IF NOT EXISTS curso_contenido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    modulo_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    tipo ENUM('video', 'archivo', 'enlace') NOT NULL,
    url_recurso TEXT NOT NULL,
    descripcion TEXT,
    orden INT DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (modulo_id) REFERENCES curso_modulo(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Entregas de Tareas
CREATE TABLE IF NOT EXISTS curso_entrega (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tarea_id INT NOT NULL,
    estudiante_id INT NOT NULL,
    archivo_url TEXT,
    comentario TEXT,
    fecha_entrega TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    calificacion DECIMAL(5,2) NULL,
    retroalimentacion TEXT,
    FOREIGN KEY (tarea_id) REFERENCES curso_tarea(id) ON DELETE CASCADE,
    FOREIGN KEY (estudiante_id) REFERENCES usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- Crear base de datos
CREATE DATABASE IF NOT EXISTS comedor_escolar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE comedor_escolar;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'base', 'cocinero') NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de unidad
CREATE TABLE IF NOT EXISTS unidad (
    id_unidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    simbolo VARCHAR(10) NOT NULL
);

-- Tabla de producto
CREATE TABLE IF NOT EXISTS producto (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    stock DECIMAL(10,2) DEFAULT 0,
    stock_minimo DECIMAL(10,2) DEFAULT 0,
    id_unidad INT,
    estado BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_unidad) REFERENCES unidad(id_unidad)
);

-- Tabla de gramaje
CREATE TABLE IF NOT EXISTS gramaje (
    id_gramaje INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT,
    gramaje_por_plato DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto),
    UNIQUE KEY unique_producto (id_producto)
);

-- Tabla de menú
CREATE TABLE IF NOT EXISTS menu (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    observacion TEXT,
    fecha DATE NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES usuarios(id)
);

-- Tabla de menu_producto (relación entre menú y productos con cantidad)
CREATE TABLE IF NOT EXISTS menu_producto (
    id_menu_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_menu INT,
    id_producto INT,
    cantidad_por_plato DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_menu) REFERENCES menu(id_menu),
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto),
    UNIQUE KEY unique_menu_producto (id_menu, id_producto)
);

-- Tabla de matricula
CREATE TABLE IF NOT EXISTS matricula (
    id_matricula INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('estudiante', 'docente', 'otros') NOT NULL,
    grado VARCHAR(20),
    seccion VARCHAR(10),
    lapso_academico VARCHAR(20) NOT NULL,
    total_masculino INT DEFAULT 0,
    total_femenino INT DEFAULT 0,
    estado BOOLEAN DEFAULT TRUE
);

-- Tabla de asistencia
CREATE TABLE IF NOT EXISTS asistencia (
    id_asistencia INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    total_masculino INT NOT NULL,
    total_femenino INT NOT NULL,
    id_matricula INT,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_matricula) REFERENCES matricula(id_matricula)
);

-- Tabla de platos_servidos
CREATE TABLE IF NOT EXISTS platos_servidos (
    id_platos_servidos INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    id_menu INT,
    platos_servidos INT NOT NULL DEFAULT 0,
    platos_devueltos INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_menu) REFERENCES menu(id_menu)
);

-- Insertar unidades por defecto
INSERT INTO unidad (nombre, simbolo) VALUES 
('Kilogramos', 'kg'),
('Litros', 'L'),
('Unidades', 'u');

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (username, password, nombre_completo, rol) 
VALUES ('yerson', 'yerson123', 'Administrador', 'admin');
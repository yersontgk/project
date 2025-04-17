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

-- Tabla de menú
CREATE TABLE IF NOT EXISTS menu (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    observacion TEXT,
    fecha DATE NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES usuarios(id)
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

-- Tabla de consumo
CREATE TABLE IF NOT EXISTS consumo (
    id_consumo INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    observacion TEXT,
    id_menu INT,
    estado BOOLEAN DEFAULT TRUE,
    created_by INT,
    FOREIGN KEY (id_menu) REFERENCES menu(id_menu),
    FOREIGN KEY (created_by) REFERENCES usuarios(id)
);

-- Tabla de consumo_detalle
CREATE TABLE IF NOT EXISTS consumo_detalle (
    id_consumo_detalle INT AUTO_INCREMENT PRIMARY KEY,
    cantidad DECIMAL(10,2) NOT NULL,
    id_producto INT,
    id_consumo INT,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto),
    FOREIGN KEY (id_consumo) REFERENCES consumo(id_consumo)
);

-- Tabla de matricula
CREATE TABLE IF NOT EXISTS matricula (
    id_matricula INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('estudiante', 'personal') NOT NULL,
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
    FOREIGN KEY (id_matricula) REFERENCES matricula(id_matricula)
);

-- Tabla de consumo_asistencia
CREATE TABLE IF NOT EXISTS consumo_asistencia (
    id_consumo_asistencia INT AUTO_INCREMENT PRIMARY KEY,
    id_consumo INT,
    id_asistencia INT,
    FOREIGN KEY (id_consumo) REFERENCES consumo(id_consumo),
    FOREIGN KEY (id_asistencia) REFERENCES asistencia(id_asistencia)
);

-- Tabla de ingreso_insumo
CREATE TABLE IF NOT EXISTS ingreso_insumo (
    id_ingreso_insumo INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    observacion TEXT,
    estado BOOLEAN DEFAULT TRUE,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES usuarios(id)
);

-- Tabla de ingreso_detalle
CREATE TABLE IF NOT EXISTS ingreso_detalle (
    id_ingreso_detalle INT AUTO_INCREMENT PRIMARY KEY,
    cantidad DECIMAL(10,2) NOT NULL,
    id_producto INT,
    id_ingreso_insumo INT,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto),
    FOREIGN KEY (id_ingreso_insumo) REFERENCES ingreso_insumo(id_ingreso_insumo)
);

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (username, password, nombre_completo, rol) 
VALUES ('yerson', 'yerson123', 'Administrador', 'admin');
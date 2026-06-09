CREATE DATABASE techzone;
USE techzone;

CREATE TABLE usuarios(
id INT AUTO_INCREMENT PRIMARY KEY,
usuario VARCHAR(50) UNIQUE,
password VARCHAR(255),
rol VARCHAR(20) DEFAULT 'user'
);

CREATE TABLE productos(
id INT AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(100),
precio DECIMAL(10,2),
imagen VARCHAR(100)
);

CREATE TABLE compras(
id INT AUTO_INCREMENT PRIMARY KEY,
usuario_id INT,
producto_id INT,
fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Datos de ejemplo (productos)
INSERT INTO productos (nombre, precio, imagen) VALUES
('Laptop', 45000.00, 'laptop.jpg'),
('Mouse', 850.00, 'mouse.jpg'),
('Teclado', 1200.00, 'teclado.png'),
('Monitor', 9500.00, 'monitor.jpg'),
('Audífonos', 1800.00, 'Aud#U00edfonos.png'),
('Bocina', 2200.00, 'Bocina.png'),
('Cámara', 15500.00, 'camara.png'),
('Router WiFi', 2100.00, 'router.jpg'),
('Memoria USB', 450.00, 'Memoria USB.png'),
('Disco duro', 3200.00, 'Disco duro.png'),
('Webcam', 1700.00, 'webcam.jpg'),
('Impresora', 12500.00, 'Impresora.png');

-- Creación de la base de datos  

CREATE DATABASE IF NOT EXISTS ventas_catalogo 

    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; 

USE ventas_catalogo; 

SET FOREIGN_KEY_CHECKS = 0; 

 

-- Tabla: roles  

CREATE TABLE roles ( 

    idRol       INT          NOT NULL AUTO_INCREMENT, 

    nombre      VARCHAR(30)  NOT NULL COMMENT 'Administrador | Vendedor | Cliente', 

    descripcion VARCHAR(120), 

    PRIMARY KEY (idRol), 

    UNIQUE KEY uk_rol_nombre (nombre) 

) ENGINE=InnoDB COMMENT='Catálogo de roles del sistema'; 

 

-- Tabla: usuarios  

CREATE TABLE usuarios ( 

    idUsuario       INT          NOT NULL AUTO_INCREMENT, 

    nombreUsuario   VARCHAR(60)  NOT NULL, 

    contrasena      VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt', 

    email           VARCHAR(100) NOT NULL, 

    activo          TINYINT(1)   NOT NULL DEFAULT 1, 

    idRol           INT          NOT NULL, 

    fechaRegistro   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP, 

    PRIMARY KEY (idUsuario), 

    UNIQUE KEY uk_usuario_email (email), 

    CONSTRAINT fk_usuario_rol FOREIGN KEY (idRol) 

        REFERENCES roles (idRol) ON UPDATE CASCADE 

) ENGINE=InnoDB COMMENT='Credenciales y rol de cada usuario'; 

 

-- Tabla: administradores  

CREATE TABLE administradores ( 

    idAdmin     INT         NOT NULL AUTO_INCREMENT, 

    nombre      VARCHAR(60) NOT NULL, 

    apellidos   VARCHAR(60) NOT NULL, 

    cedula      VARCHAR(20) NOT NULL, 

    telefono    VARCHAR(20), 

    idUsuario   INT         NOT NULL, 

    PRIMARY KEY (idAdmin), 

    UNIQUE KEY uk_admin_cedula  (cedula), 

    UNIQUE KEY uk_admin_usuario (idUsuario), 

    CONSTRAINT fk_admin_usuario FOREIGN KEY (idUsuario) 

        REFERENCES usuarios (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

-- Tabla: vendedores  

CREATE TABLE vendedores ( 

    idVendedor  INT         NOT NULL AUTO_INCREMENT, 

    nombre      VARCHAR(60) NOT NULL, 

    apellidos   VARCHAR(60) NOT NULL, 

    cedula      VARCHAR(20) NOT NULL, 

    telefono    VARCHAR(20), 

    idUsuario   INT         NOT NULL, 

    PRIMARY KEY (idVendedor), 

    UNIQUE KEY uk_vend_cedula  (cedula), 

    UNIQUE KEY uk_vend_usuario (idUsuario), 

    CONSTRAINT fk_vendedor_usuario FOREIGN KEY (idUsuario) 

        REFERENCES usuarios (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

-- Tabla: clientes  

CREATE TABLE clientes ( 

    idCliente   INT          NOT NULL AUTO_INCREMENT, 

    nombre      VARCHAR(60)  NOT NULL, 

    apellidos   VARCHAR(60)  NOT NULL, 

    cedula      VARCHAR(20)  NOT NULL, 

    telefono    VARCHAR(20), 

    email       VARCHAR(100) NOT NULL, 

    idUsuario   INT          NOT NULL, 

    PRIMARY KEY (idCliente), 

    UNIQUE KEY uk_cliente_cedula  (cedula), 

    UNIQUE KEY uk_cliente_email   (email), 

    UNIQUE KEY uk_cliente_usuario (idUsuario), 

    CONSTRAINT fk_cliente_usuario FOREIGN KEY (idUsuario) 

        REFERENCES usuarios (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

-- Tabla: direcciones  

CREATE TABLE direcciones ( 

    idDireccion     INT          NOT NULL AUTO_INCREMENT, 

    calle           VARCHAR(120) NOT NULL, 

    ciudad          VARCHAR(60)  NOT NULL, 

    provincia       VARCHAR(60)  NOT NULL, 

    codigoPostal    VARCHAR(10), 

    esPrincipal     TINYINT(1)   NOT NULL DEFAULT 0, 

    idCliente       INT          NOT NULL, 

    PRIMARY KEY (idDireccion), 

    CONSTRAINT fk_dir_cliente FOREIGN KEY (idCliente) 

        REFERENCES clientes (idCliente) ON DELETE CASCADE ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

-- Tabla: categorias  

CREATE TABLE categorias ( 

    idCategoria INT         NOT NULL AUTO_INCREMENT, 

    nombre      VARCHAR(60) NOT NULL, 

    descripcion VARCHAR(200), 

    PRIMARY KEY (idCategoria), 

    UNIQUE KEY uk_categoria_nombre (nombre) 

) ENGINE=InnoDB; 

 

-- Tabla: productos  

CREATE TABLE productos ( 

    idProducto      INT             NOT NULL AUTO_INCREMENT, 

    nombre          VARCHAR(120)    NOT NULL, 

    descripcion     TEXT, 

    precio          DECIMAL(10,2)   NOT NULL, 

    descuento       DECIMAL(5,2)    NOT NULL DEFAULT 0.00, 

    stock           INT             NOT NULL DEFAULT 0, 

    imagenes        VARCHAR(500)    COMMENT 'Rutas separadas por coma', 

    activo          TINYINT(1)      NOT NULL DEFAULT 1, 

    fechaCreacion   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP, 

    idCategoria     INT             NOT NULL, 

    PRIMARY KEY (idProducto), 

    INDEX idx_producto_categoria (idCategoria), 

    INDEX idx_producto_activo    (activo), 

    CONSTRAINT fk_producto_categoria FOREIGN KEY (idCategoria) 

        REFERENCES categorias (idCategoria) ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

-- Tabla: carritos  

CREATE TABLE carritos ( 

    idCarrito       INT         NOT NULL AUTO_INCREMENT, 

    fechaCreacion   DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP, 

    estado          VARCHAR(20) NOT NULL DEFAULT 'activo' 

                                COMMENT 'activo | abandonado | convertido', 

    idCliente       INT         NOT NULL, 

    PRIMARY KEY (idCarrito), 

    UNIQUE KEY uk_carrito_cliente (idCliente), 

    CONSTRAINT fk_carrito_cliente FOREIGN KEY (idCliente) 

        REFERENCES clientes (idCliente) ON DELETE CASCADE ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

-- Tabla: items_carrito  

CREATE TABLE items_carrito ( 

    idItem          INT           NOT NULL AUTO_INCREMENT, 

    cantidad        INT           NOT NULL DEFAULT 1, 

    precioUnitario  DECIMAL(10,2) NOT NULL, 

    idCarrito       INT           NOT NULL, 

    idProducto      INT           NOT NULL, 

    PRIMARY KEY (idItem), 

    UNIQUE KEY uk_item_carrito_prod (idCarrito, idProducto), 

    CONSTRAINT fk_item_carrito  FOREIGN KEY (idCarrito) 

        REFERENCES carritos  (idCarrito)  ON DELETE CASCADE ON UPDATE CASCADE, 

    CONSTRAINT fk_item_producto FOREIGN KEY (idProducto) 

        REFERENCES productos (idProducto) ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

 

 

 

-- Tabla: cupones  

CREATE TABLE cupones ( 

    idCupon         INT           NOT NULL AUTO_INCREMENT, 

    codigo          VARCHAR(30)   NOT NULL, 

    tipo            ENUM('Porcentaje','Monto_fijo','envio_gratis') 

                                  NOT NULL DEFAULT 'Porcentaje', 

    descuento       DECIMAL(10,2) NOT NULL DEFAULT 0.00, 

    usoMaximo       INT           NOT NULL DEFAULT 1, 

    usosActuales    INT           NOT NULL DEFAULT 0, 

    fechaInicio     DATE          NOT NULL, 

    fechaVencimiento DATE         NOT NULL, 

    activo          TINYINT(1)    NOT NULL DEFAULT 1, 

    PRIMARY KEY (idCupon), 

    UNIQUE KEY uk_cupon_codigo (codigo) 

) ENGINE=InnoDB; 

 

-- Tabla: pedidos  

CREATE TABLE pedidos ( 

    idPedido        INT           NOT NULL AUTO_INCREMENT, 

    numeroPedido    VARCHAR(20)   NOT NULL COMMENT 'Ej: PED-2026-00128', 

    fechaPedido     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP, 

    estado          ENUM('Pendiente','Confirmado','En_proceso', 

                        'Enviado','Entregado','Cancelado','Devuelto') 

                                  NOT NULL DEFAULT 'Pendiente', 

    subtotal        DECIMAL(10,2) NOT NULL, 

    descuento       DECIMAL(10,2) NOT NULL DEFAULT 0.00, 

    total           DECIMAL(10,2) NOT NULL, 

    notas           TEXT, 

    idCliente       INT           NOT NULL, 

    idCupon         INT, 

    PRIMARY KEY (idPedido), 

    UNIQUE KEY uk_pedido_numero (numeroPedido), 

    INDEX idx_pedido_cliente (idCliente), 

    INDEX idx_pedido_estado  (estado), 

    CONSTRAINT fk_pedido_cliente FOREIGN KEY (idCliente) 

        REFERENCES clientes (idCliente) ON UPDATE CASCADE, 

    CONSTRAINT fk_pedido_cupon   FOREIGN KEY (idCupon) 

        REFERENCES cupones  (idCupon)  ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

-- Tabla: detalle_pedido  

CREATE TABLE detalle_pedido ( 

    idDetalle       INT           NOT NULL AUTO_INCREMENT, 

    cantidad        INT           NOT NULL, 

    precioUnitario  DECIMAL(10,2) NOT NULL, 

    subtotal        DECIMAL(10,2) NOT NULL, 

    idPedido        INT           NOT NULL, 

    idProducto      INT           NOT NULL, 

    PRIMARY KEY (idDetalle), 

    INDEX idx_detalle_pedido (idPedido), 

    CONSTRAINT fk_detalle_pedido   FOREIGN KEY (idPedido) 

        REFERENCES pedidos   (idPedido)   ON DELETE CASCADE ON UPDATE CASCADE, 

    CONSTRAINT fk_detalle_producto FOREIGN KEY (idProducto) 

        REFERENCES productos (idProducto) ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

-- Tabla: pagos 

CREATE TABLE pagos ( 

    idPago      INT           NOT NULL AUTO_INCREMENT, 

    monto       DECIMAL(10,2) NOT NULL, 

    fechaPago   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP, 

    estado      ENUM('Pendiente','Aprobado','Rechazado','Reembolsado') 

                              NOT NULL DEFAULT 'Pendiente', 

    referencia  VARCHAR(80)   COMMENT 'Referencia de la transacción', 

    metodoPago  ENUM('Tarjeta_Credito','Tarjeta_Debito', 

                     'Transferencia','Efectivo') 

                              NOT NULL DEFAULT 'Efectivo', 

    idPedido    INT           NOT NULL, 

    PRIMARY KEY (idPago), 

    UNIQUE KEY uk_pago_pedido (idPedido), 

    CONSTRAINT fk_pago_pedido FOREIGN KEY (idPedido) 

        REFERENCES pedidos (idPedido) ON DELETE CASCADE ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

-- Tabla: envios 

CREATE TABLE envios ( 

    idEnvio         INT         NOT NULL AUTO_INCREMENT, 

    codigoRastreo   VARCHAR(60), 

    empresa         VARCHAR(80) COMMENT 'Empresa transportista', 

    fechaEstimada   DATE, 

    fechaEntrega    DATE, 

    estado          ENUM('Pendiente','En_Camino','En_Destino','Entregado') 

                                NOT NULL DEFAULT 'Pendiente', 

    idPedido        INT         NOT NULL, 

    idDireccion     INT         NOT NULL, 

    PRIMARY KEY (idEnvio), 

    UNIQUE KEY uk_envio_pedido (idPedido), 

    CONSTRAINT fk_envio_pedido    FOREIGN KEY (idPedido) 

        REFERENCES pedidos     (idPedido)     ON UPDATE CASCADE, 

    CONSTRAINT fk_envio_direccion FOREIGN KEY (idDireccion) 

        REFERENCES direcciones (idDireccion) ON UPDATE CASCADE 

) ENGINE=InnoDB; 

 

-- Tabla: calificaciones  

CREATE TABLE calificaciones ( 

    idCalificacion  INT      NOT NULL AUTO_INCREMENT, 

    nota            TINYINT  NOT NULL COMMENT '1 a 5 estrellas', 

    comentario      TEXT, 

    fecha           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 

    idProducto      INT      NOT NULL, 

    idCliente       INT      NOT NULL, 

    PRIMARY KEY (idCalificacion), 

    UNIQUE KEY uk_calif_prod_cli (idProducto, idCliente), 

    INDEX idx_calif_producto (idProducto), 

    CONSTRAINT fk_calif_producto FOREIGN KEY (idProducto) 

        REFERENCES productos (idProducto) ON DELETE CASCADE ON UPDATE CASCADE, 

    CONSTRAINT fk_calif_cliente  FOREIGN KEY (idCliente) 

        REFERENCES clientes  (idCliente)  ON UPDATE CASCADE 

) ENGINE=InnoDB; 

-- Vistas útiles 

-- Vista: productos con promedio de calificación 

CREATE OR REPLACE VIEW v_productos_rating AS 

    SELECT p.idProducto, p.nombre, p.precio, p.stock, 

           c.nombre AS categoria, 

           ROUND(AVG(cal.nota), 1) AS promedio, 

           COUNT(cal.idCalificacion)  AS totalResenas 

    FROM productos p 

    JOIN categorias c   ON p.idCategoria = c.idCategoria 

    LEFT JOIN calificaciones cal ON cal.idProducto = p.idProducto 

    WHERE p.activo = 1 

    GROUP BY p.idProducto; 

 

-- Vista: resumen de ventas por vendedor 

CREATE OR REPLACE VIEW v_ventas_vendedor AS 

    SELECT v.idVendedor, 

           CONCAT(v.nombre,' ',v.apellidos) AS vendedor, 

           COUNT(pe.idPedido)    AS totalPedidos, 

           SUM(pe.total)         AS montoTotal 

    FROM vendedores v 

    JOIN clientes cl ON cl.idUsuario = v.idUsuario 

    JOIN pedidos  pe ON pe.idCliente = cl.idCliente 

    WHERE pe.estado NOT IN ('Cancelado','Devuelto') 

    GROUP BY v.idVendedor;  

-- Datos iniciales (INSERT básicos)  
 
SET FOREIGN_KEY_CHECKS = 0;

-- Roles 

INSERT INTO roles (nombre, descripcion) VALUES 

    ('Administrador', 'Acceso total al sistema'), 

    ('Vendedor',      'Gestión de catálogo y clientes asignados'), 

    ('Cliente',       'Consulta de catálogo, carrito y pedidos'); 

 

-- Categorías base 

INSERT INTO categorias (nombre, descripcion) VALUES 

    ('Ropa',        'Prendas de vestir para hombre, mujer y niños'),
    ('Calzado',     'Zapatos, tenis y sandalias de todas las tallas'),
    ('Hogar',       'Artículos para decoración y uso del hogar'),
    ('Belleza',     'Cosméticos, perfumes y cuidado personal'),
    ('Accesorios',  'Bolsos, cinturones, relojes y joyería'),
    ('Tecnología',  'Gadgets, accesorios electrónicos y cables'),
    ('Deportes',    'Ropa y equipos para actividades deportivas'); 

-- Usuario administrador por defecto (contraseña: Admin2026!)
-- Usuarios clientes y vendedor (contraseña: Test2026! para todos)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFpXfelC/ 

INSERT INTO usuarios (nombreUsuario, contrasena, email, idRol) VALUES 

    ('admin', '$2y$10$9eQyuIVk9YIXzvc0dQOcPeDAqT4XuBfU7Hks7La1Gu8gVaY2gxg1K', 'admin@catalogopro.do', 1),
    ('vendedor1',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFpXfelC/', 'vendedor1@ventavista.do',  2),
    ('vendedor2',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFpXfelC/', 'vendedor2@ventavista.do',  2),
    ('cliente1',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFpXfelC/', 'cliente1@gmail.com',       3),
    ('cliente2',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFpXfelC/', 'cliente2@gmail.com',       3),
    ('cliente3',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFpXfelC/', 'cliente3@gmail.com',       3),
    ('cliente4',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFpXfelC/', 'cliente4@gmail.com',       3),
    ('cliente5',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFpXfelC/', 'cliente5@hotmail.com',     3),
    ('cliente6',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFpXfelC/', 'cliente6@hotmail.com',     3); 

INSERT INTO administradores (nombre, apellidos, cedula, telefono, idUsuario) 

    VALUES ('Arowarlin', 'Suárez Díaz', '001-0000001-1', '809-000-0001', 1); 

 -- Vendedores 
INSERT INTO vendedores (nombre, apellidos, cedula, telefono, idUsuario) VALUES
('Carlos',   'Martínez Pérez',  '402-1234567-1', '809-555-0101', 2),
('Luisa',    'Ramírez Díaz',    '402-2345678-2', '809-555-0102', 3);

-- Clientes 
INSERT INTO clientes (nombre, apellidos, cedula, telefono, email, idUsuario) VALUES
('María',     'González López',  '001-1111111-1', '829-600-0001', 'cliente1@gmail.com',   4),
('Juan',      'Pérez Sánchez',   '001-2222222-2', '829-600-0002', 'cliente2@gmail.com',   5),
('Ana',       'Rodríguez Cruz',  '001-3333333-3', '829-600-0003', 'cliente3@gmail.com',   6),
('Pedro',     'Fernández Mora',  '001-4444444-4', '829-600-0004', 'cliente4@gmail.com',   7),
('Sofía',     'Torres Vargas',   '001-5555555-5', '829-600-0005', 'cliente5@hotmail.com', 8),
('Luis',      'Herrera Castillo','001-6666666-6', '829-600-0006', 'cliente6@hotmail.com', 9);

-- Direcciones
INSERT INTO direcciones (calle, ciudad, provincia, codigoPostal, esPrincipal, idCliente) VALUES
('Calle del Sol 12',        'Santiago',        'Santiago',          '51000', 1, 1),
('Av. Independencia 45',    'Santo Domingo',   'Distrito Nacional', '10101', 1, 2),
('Calle Las Flores 8',      'La Vega',         'La Vega',           '41000', 1, 3),
('Calle Duarte 100',        'San Pedro',       'San Pedro',         '21000', 1, 4),
('Residencial Los Pinos 3', 'Puerto Plata',    'Puerto Plata',      '57000', 1, 5),
('Av. España 77',           'Santo Domingo',   'Distrito Nacional', '10205', 1, 6),
('Calle 5 #22',             'Santiago',        'Santiago',          '51001', 0, 1),
('Calle Mella 9',           'Bonao',           'Monseñor Nouel',    '43000', 0, 2);
 
-- Productos 
INSERT INTO productos (nombre, descripcion, precio, descuento, stock, imagenes, idCategoria) VALUES
('Camiseta Polo Classic',      'Camiseta polo de algodón peinado, disponible en varios colores.',           850.00,  10.00, 120, 'polo_classic.jpg',      1),
('Vestido Floral Verano',      'Vestido midi con estampado floral, tela liviana y fresca.',                1250.00,  15.00,  60, 'vestido_floral.jpg',    1),
('Pantalón Chino Slim',        'Pantalón chino de corte slim, ideal para uso casual y semi-formal.',       1100.00,   0.00,  85, 'chino_slim.jpg',        1),
('Tenis Running ProStep',      'Tenis de alto rendimiento para correr, suela antideslizante.',             2800.00,  20.00,  45, 'tenis_prostep.jpg',     2),
('Sandalia Casual Dama',       'Sandalia de cuero sintético con plantilla acolchada.',                      750.00,   5.00,  70, 'sandalia_casual.jpg',   2),
('Bota Urbana Caballero',      'Bota de cuero genuino con suela de goma resistente.',                      3200.00,  10.00,  30, 'bota_urbana.jpg',       2),
('Lámpara LED Decorativa',     'Lámpara de mesa con luz cálida y fría, control táctil.',                   1800.00,   0.00,  40, 'lampara_led.jpg',       3),
('Juego de Sábanas 200 Hilos', 'Juego de sábanas king size, 100% algodón egipcio.',                        2200.00,  12.00,  55, 'sabanas_200.jpg',       3),
('Crema Hidratante Premium',   'Crema facial con ácido hialurónico y vitamina C, 50ml.',                    950.00,   0.00,  90, 'crema_hidratante.jpg',  4),
('Perfume Noche Elegante',     'Fragancia amaderada para hombre, duración 12 horas, 100ml.',               3500.00,   8.00,  25, 'perfume_noche.jpg',     4),
('Bolso Tote Cuero',           'Bolso de mano estilo tote, cuero genuino, varios compartimientos.',        4200.00,   0.00,  20, 'bolso_tote.jpg',        5),
('Reloj Análogo Minimalista',  'Reloj de pulsera con diseño minimalista, correa de cuero.',                2600.00,  15.00,  35, 'reloj_minimal.jpg',     5),
('Cable USB-C Trenzado 2m',    'Cable de carga rápida USB-C, nylon trenzado, 2 metros.',                    450.00,   0.00, 200, 'cable_usbc.jpg',        6),
('Audífonos Bluetooth Sport',  'Audífonos inalámbricos resistentes al agua, batería 8 horas.',             1950.00,  10.00,  50, 'audifonos_sport.jpg',   6),
('Leggins Deportivo Mujer',    'Leggins de alto rendimiento con bolsillo lateral y cintura alta.',          980.00,   5.00,  75, 'leggins_deporte.jpg',   7);
 
-- Cupones
INSERT INTO cupones (codigo, tipo, descuento, usoMaximo, usosActuales, fechaInicio, fechaVencimiento, activo) VALUES
('BIENVENIDO10',  'Porcentaje',   10.00, 100,  3, '2026-01-01', '2026-12-31', 1),
('VERANO20',      'Porcentaje',   20.00,  50,  8, '2026-03-01', '2026-06-30', 1),
('DESCUENTO500',  'Monto_fijo',  500.00,  30,  0, '2026-01-01', '2026-06-30', 1),
('ENVIOGRATIS',   'envio_gratis',  0.00,  80, 12, '2026-01-01', '2026-12-31', 1),
('BLACKFRIDAY',   'Porcentaje',   30.00, 200,  0, '2026-11-25', '2026-11-30', 1),
('EXPIRADO2025',  'Porcentaje',   15.00,  20, 20, '2025-01-01', '2025-12-31', 0);
 
-- Pedidos
INSERT INTO pedidos (numeroPedido, fechaPedido, estado, subtotal, descuento, total, notas, idCliente, idCupon) VALUES
('PED-2026-00001', '2026-01-15 10:30:00', 'Entregado',   2100.00,  210.00, 1890.00, NULL,                      1, 1),
('PED-2026-00002', '2026-01-22 14:15:00', 'Entregado',   5000.00,  500.00, 4500.00, 'Entregar en horario AM.',  2, 3),
('PED-2026-00003', '2026-02-03 09:00:00', 'Enviado',     3750.00,    0.00, 3750.00, NULL,                       3, NULL),
('PED-2026-00004', '2026-02-14 16:45:00', 'Confirmado',  6800.00, 1360.00, 5440.00, 'Regalo, incluir tarjeta.', 4, 2),
('PED-2026-00005', '2026-02-28 11:20:00', 'Pendiente',   1900.00,    0.00, 1900.00, NULL,                       5, NULL),
('PED-2026-00006', '2026-03-05 08:00:00', 'En_proceso',  4150.00,  415.00, 3735.00, NULL,                       6, 1),
('PED-2026-00007', '2026-03-10 13:30:00', 'Cancelado',   2800.00,    0.00, 2800.00, 'Cliente canceló.',         1, NULL),
('PED-2026-00008', '2026-03-18 17:00:00', 'Pendiente',   5200.00,    0.00, 5200.00, NULL,                       2, NULL),
('PED-2026-00009', '2026-03-25 10:10:00', 'Confirmado',  3480.00,  348.00, 3132.00, NULL,                       3, 1),
('PED-2026-00010', '2026-04-01 09:45:00', 'Pendiente',    950.00,    0.00,  950.00, NULL,                       4, NULL),
('PED-2026-00011', '2026-04-02 14:00:00', 'En_proceso',  7400.00,    0.00, 7400.00, 'Urgente.',                 5, NULL),
('PED-2026-00012', '2026-04-03 11:30:00', 'Pendiente',   2550.00,  510.00, 2040.00, NULL,                       6, 2);
 
-- Detalle de pedidos
INSERT INTO detalle_pedido (cantidad, precioUnitario, subtotal, idPedido, idProducto) VALUES
(1,  850.00,   850.00,  1,  1),
(1, 1250.00,  1250.00,  1,  2),
(2,  750.00,  1500.00,  2,  5),
(1, 3500.00,  3500.00,  2, 10),
(1, 2800.00,  2800.00,  3,  4),
(1,  950.00,   950.00,  3,  9),
(1, 4200.00,  4200.00,  4, 11),
(1, 2600.00,  2600.00,  4, 12),
(1, 1950.00,  1950.00,  5, 14),
(1, 1800.00,  1800.00,  6,  7),
(1, 2200.00,  2200.00,  6,  8),
(1, 2800.00,  2800.00,  7,  4),
(2,  850.00,  1700.00,  8,  1),
(1, 3500.00,  3500.00,  8, 10),
(2, 1100.00,  2200.00,  9,  3),
(1,  450.00,   450.00,  9, 13),
(1,  950.00,   950.00, 10,  9),
(1, 3200.00,  3200.00, 11,  6),
(2,  980.00,  1960.00, 11, 15),
(1, 2200.00,  2200.00, 11,  8),
(1, 1100.00,  1100.00, 12,  3),
(1, 1250.00,  1250.00, 12,  2),
(1,  450.00,   450.00, 12, 13);
 
-- Pagos
INSERT INTO pagos (monto, fechaPago, estado, referencia, metodoPago, idPedido) VALUES
(1890.00, '2026-01-15 10:35:00', 'Aprobado',  'TXN-001-2026', 'Tarjeta_Credito', 1),
(4500.00, '2026-01-22 14:20:00', 'Aprobado',  'TXN-002-2026', 'Transferencia',   2),
(3750.00, '2026-02-03 09:05:00', 'Aprobado',  'TXN-003-2026', 'Tarjeta_Debito',  3),
(5440.00, '2026-02-14 16:50:00', 'Aprobado',  'TXN-004-2026', 'Tarjeta_Credito', 4),
(1900.00, '2026-02-28 11:25:00', 'Pendiente', NULL,            'Efectivo',        5),
(3735.00, '2026-03-05 08:05:00', 'Aprobado',  'TXN-006-2026', 'Transferencia',   6),
(2800.00, '2026-03-10 13:35:00', 'Reembolsado','TXN-007-2026','Tarjeta_Credito', 7),
(5200.00, '2026-03-18 17:05:00', 'Pendiente', NULL,            'Efectivo',        8),
(3132.00, '2026-03-25 10:15:00', 'Aprobado',  'TXN-009-2026', 'Tarjeta_Debito',  9),
(7400.00, '2026-04-02 14:05:00', 'Pendiente', NULL,            'Transferencia',  11);
 
-- Envíos 
INSERT INTO envios (codigoRastreo, empresa, fechaEstimada, fechaEntrega, estado, idPedido, idDireccion) VALUES
('DHL-00001-DO', 'DHL',      '2026-01-18', '2026-01-17', 'Entregado',  1, 1),
('FEDEX-00002',  'FedEx',    '2026-01-26', '2026-01-25', 'Entregado',  2, 2),
('DHL-00003-DO', 'DHL',      '2026-02-07', NULL,         'En_Camino',  3, 3),
('CAEX-00004',   'Caex',     '2026-02-18', NULL,         'En_Destino', 4, 4),
('DHL-00006-DO', 'DHL',      '2026-03-09', NULL,         'En_Camino',  6, 6);
 
-- Calificaciones
INSERT INTO calificaciones (nota, comentario, fecha, idProducto, idCliente) VALUES
(5, 'Excelente calidad, el color es exacto a la foto.',         '2026-01-20', 1,  1),
(4, 'Muy bonito vestido, la tela es cómoda y fresca.',          '2026-01-28',  2,  1),
(5, 'Los tenis son perfectos para correr, muy cómodos.',        '2026-02-10',  4,  2),
(3, 'La sandalia está bien pero el tamaño es un poco grande.',  '2026-02-10',  5,  2),
(5, 'El perfume huele increíble y dura todo el día.',           '2026-02-20', 10,  4),
(4, 'El bolso es precioso y muy espacioso.',                    '2026-02-20', 11,  4),
(5, 'La lámpara quedó perfecta en mi sala.',                    '2026-03-12',  7,  6),
(4, 'Las sábanas son suaves y frescas, buena compra.',          '2026-03-12',  8,  6),
(5, 'La crema hidrata muy bien, la recomiendo.',                '2026-03-28',  9,  3),
(2, 'El cable duró poco, se dañó en un mes.',                   '2026-03-28', 13,  3),
(5, 'Los audífonos tienen excelente sonido y batería.',         '2026-03-02', 14,  5),
(4, 'El pantalón chino se ve elegante y es cómodo.',            '2026-03-30',  3,  3);
 
-- Carritos activos
INSERT INTO carritos (fechaCreacion, estado, idCliente) VALUES
('2026-04-05 10:00:00', 'activo', 4),
('2026-04-05 14:30:00', 'activo', 5),
('2026-04-06 08:15:00', 'activo', 6);
 
INSERT INTO items_carrito (cantidad, precioUnitario, idCarrito, idProducto) VALUES
(2,  850.00, 1,  1),
(1, 1950.00, 1, 14),
(1, 3500.00, 2, 10),
(2,  980.00, 2, 15),
(1, 2600.00, 3, 12),
(1,  450.00, 3, 13);

SET FOREIGN_KEY_CHECKS = 1;
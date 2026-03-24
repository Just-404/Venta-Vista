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

 

-- Datos iniciales (INSERT básicos)  

SET FOREIGN_KEY_CHECKS = 1; 

 

-- Roles 

INSERT INTO roles (nombre, descripcion) VALUES 

    ('Administrador', 'Acceso total al sistema'), 

    ('Vendedor',      'Gestión de catálogo y clientes asignados'), 

    ('Cliente',       'Consulta de catálogo, carrito y pedidos'); 

 

-- Categorías base 

INSERT INTO categorias (nombre) VALUES 

    ('Ropa'), ('Calzado'), ('Hogar'), ('Belleza'), ('Accesorios'); 

 

-- Usuario administrador por defecto (contraseña: Admin2026!) 

INSERT INTO usuarios (nombreUsuario, contrasena, email, idRol) VALUES 

    ('admin', '$2y$12$wXk4U...hashEjemplo...', 'admin@catalogopro.do', 1); 

 

INSERT INTO administradores (nombre, apellidos, cedula, telefono, idUsuario) 

    VALUES ('Arowarlin', 'Suárez Díaz', '001-0000001-1', '809-000-0001', 1); 

 

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
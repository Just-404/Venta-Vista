-- ─────────────────────────────────────────────────────────────────────────────
-- Migración: Módulo de Configuración
-- ─────────────────────────────────────────────────────────────────────────────

USE ventas_catalogo;

CREATE TABLE IF NOT EXISTS notificaciones (
    idNotificacion  INT          NOT NULL AUTO_INCREMENT,
    idUsuario       INT          NOT NULL,
    tipo            ENUM('pedido_nuevo','estado_pedido','stock_bajo','factura','sistema')
                                 NOT NULL DEFAULT 'sistema',
    titulo          VARCHAR(120) NOT NULL,
    mensaje         TEXT,
    url             VARCHAR(255),
    leida           TINYINT(1)   NOT NULL DEFAULT 0,
    fechaCreacion   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idNotificacion),
    INDEX idx_notif_usuario_leida (idUsuario, leida),
    CONSTRAINT fk_notif_usuario FOREIGN KEY (idUsuario)
        REFERENCES usuarios (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Notificaciones in-app por usuario';

ALTER TABLE notificaciones
    MODIFY COLUMN tipo ENUM(
        'pedido_nuevo',
        'estado_pedido',
        'stock_bajo',
        'factura',
        'sistema',
        'nuevo_cliente',
        'cupon_vence',
        'envio_actualizado',
        'pago'
    ) NOT NULL DEFAULT 'sistema';
    
-- Tabla: configuracion_sistema (clave → valor, singleton por clave)
CREATE TABLE IF NOT EXISTS configuracion_sistema (
    clave       VARCHAR(60)  NOT NULL,
    valor       TEXT,
    descripcion VARCHAR(150),
    PRIMARY KEY (clave)
) ENGINE=InnoDB COMMENT='Parámetros globales del negocio';

-- Valores por defecto
INSERT INTO configuracion_sistema (clave, valor, descripcion) VALUES
    ('negocio_nombre',    'Venta Vista S.R.L.',              'Nombre fiscal del negocio'),
    ('negocio_rnc',       '1-31-00001-1',                    'RNC / Cédula Fiscal'),
    ('negocio_telefono',  '809-000-0000',                    'Teléfono del negocio'),
    ('negocio_direccion', 'Av. 27 de Febrero, Santo Domingo, RD', 'Dirección fiscal'),
    ('negocio_email',     'info@ventavista.do',              'Correo institucional'),
    ('itbis_porcentaje',  '18',                              'Tasa ITBIS (%)'),
    ('envio_costo_base',  '200',                             'Costo de envío base (RD$)')
ON DUPLICATE KEY UPDATE clave = clave;

-- Tabla: preferencias_notificacion (una fila por usuario)
CREATE TABLE IF NOT EXISTS preferencias_notificacion (
    idPreferencia       INT          NOT NULL AUTO_INCREMENT,
    idUsuario           INT          NOT NULL,
    confirmar_pedido    TINYINT(1)   NOT NULL DEFAULT 1  COMMENT 'Email al confirmar pedido',
    alerta_stock        TINYINT(1)   NOT NULL DEFAULT 1  COMMENT 'Alerta stock ≤ 5 unidades',
    factura_automatica  TINYINT(1)   NOT NULL DEFAULT 1  COMMENT 'Envío automático de facturas',
    notif_estado_pedido TINYINT(1)   NOT NULL DEFAULT 1  COMMENT 'Notificar cambio de estado',
    registro_publico    TINYINT(1)   NOT NULL DEFAULT 0  COMMENT 'Solo Admin: registro público clientes',
    PRIMARY KEY (idPreferencia),
    UNIQUE KEY uk_pref_usuario (idUsuario),
    CONSTRAINT fk_pref_usuario FOREIGN KEY (idUsuario)
        REFERENCES usuarios (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Preferencias de notificación por usuario';

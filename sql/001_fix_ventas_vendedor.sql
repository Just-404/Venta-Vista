-- ══════════════════════════════════════════════════════════════════════════════
-- ARCHIVO: sql/001_fix_ventas_vendedor.sql
--
--Ventas por vendedor no mostraba datos porque la vista SQL 
--v_ventas_vendedor tenía un JOIN imposible, cruzando vendedores 
--con clientes por idUsuario cuando ambos son usuarios distintos 
--que nunca comparten ese campo. Pero el problema de fondo era que 
--el schema no tenía ninguna columna que vinculara los productos con 
--los vendedores, así que agregué idVendedor a la tabla productos, 
--recreé la vista con el join correcto siguiendo la cadena 
--vendedores → productos → detalle_pedido → pedidos
--
-- Qué hace:
--  1. Agrega columna idVendedor a la tabla productos
--  2. Asigna los productos existentes entre los vendedores
--  3. Corrige la vista v_ventas_vendedor con el join correcto
--  4. Crea la vista v_ventas_vendedor_mensual para el gráfico de líneas
-- ══════════════════════════════════════════════════════════════════════════════
 
USE ventas_catalogo;
 
-- ─────────────────────────────────────────────────────────────────────────────
--  Añadir idVendedor a la tabla productos
--  (NULL permitido para productos sin vendedor asignado aún)
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE productos
    ADD COLUMN idVendedor INT NULL
        COMMENT 'Vendedor responsable/dueño del producto'
        AFTER idCategoria,
    ADD CONSTRAINT fk_producto_vendedor
        FOREIGN KEY (idVendedor)
        REFERENCES vendedores (idVendedor)
        ON DELETE SET NULL
        ON UPDATE CASCADE;
 
-- ─────────────────────────────────────────────────────────────────────────────
-- Distribuye los productos de ejemplo entre los dos vendedores
--   (Carlos Martínez = 1 · Luisa Ramírez = 2)
--
--   Vendedor 1 (Carlos): Ropa, Calzado, Deportes → productos 1-6, 15
--   Vendedor 2 (Luisa):  Hogar, Belleza, Accesorios, Tecnología → productos 7-14
-- ─────────────────────────────────────────────────────────────────────────────
UPDATE productos SET idVendedor = 1 WHERE idProducto IN (1, 2, 3, 4, 5, 6, 15);
UPDATE productos SET idVendedor = 2 WHERE idProducto IN (7, 8, 9, 10, 11, 12, 13, 14);
 
-- ─────────────────────────────────────────────────────────────────────────────
-- Recrea la vista v_ventas_vendedor con el join correcto
--
--  Cadena lógica real:
--    vendedores → productos → detalle_pedido → pedidos
-- ─────────────────────────────────────────────────────────────────────────────
CREATE OR REPLACE VIEW v_ventas_vendedor AS
    SELECT
        v.idVendedor,
        CONCAT(v.nombre, ' ', v.apellidos) AS vendedor,
        COUNT(DISTINCT pe.idPedido)         AS totalPedidos,
        COALESCE(SUM(dp.subtotal), 0)       AS montoTotal
    FROM vendedores v
    JOIN productos       pr ON pr.idVendedor = v.idVendedor
    JOIN detalle_pedido  dp ON dp.idProducto = pr.idProducto
    JOIN pedidos         pe ON pe.idPedido   = dp.idPedido
    WHERE pe.estado NOT IN ('Cancelado', 'Devuelto')
    GROUP BY v.idVendedor, v.nombre, v.apellidos;
 
-- ─────────────────────────────────────────────────────────────────────────────
-- Crea la vista mensual para el gráfico de líneas
-- ─────────────────────────────────────────────────────────────────────────────
CREATE OR REPLACE VIEW v_ventas_vendedor_mensual AS
    SELECT
        v.idVendedor,
        CONCAT(v.nombre, ' ', v.apellidos)    AS vendedor,
        DATE_FORMAT(pe.fechaPedido, '%Y-%m')  AS mes,
        COALESCE(SUM(dp.subtotal), 0)         AS total
    FROM vendedores v
    JOIN productos       pr ON pr.idVendedor = v.idVendedor
    JOIN detalle_pedido  dp ON dp.idProducto = pr.idProducto
    JOIN pedidos         pe ON pe.idPedido   = dp.idPedido
    WHERE pe.estado NOT IN ('Cancelado', 'Devuelto')
    GROUP BY v.idVendedor, v.nombre, v.apellidos, mes
    ORDER BY mes ASC, v.idVendedor ASC;
 
-- ─────────────────────────────────────────────────────────────────────────────
-- VERIFICACIÓN (opcional — para confirmar que hay datos)
-- ─────────────────────────────────────────────────────────────────────────────
-- SELECT * FROM v_ventas_vendedor;
-- SELECT * FROM v_ventas_vendedor_mensual;

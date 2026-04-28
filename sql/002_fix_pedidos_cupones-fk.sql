-- Paso 1 — Eliminar la FK existente:
ALTER TABLE pedidos DROP FOREIGN KEY fk_pedido_cupon;

-- Paso 2 — Permitir NULL en la columna:
ALTER TABLE pedidos
    MODIFY COLUMN idCupon INT NULL;

-- Paso 3 - Limpiar todos los valores huérfanos (para que no quede en cero):
UPDATE pedidos p
LEFT JOIN cupones c ON p.idCupon = c.idCupon
SET p.idCupon = NULL
WHERE c.idCupon IS NULL;

-- Paso 4 — Verificar que no queden valores huérfanos:
SELECT DISTINCT idCupon FROM pedidos WHERE idCupon IS NOT NULL;

-- Paso 5 — Crear la FK con ON DELETE SET NULL:
ALTER TABLE pedidos
    ADD CONSTRAINT fk_pedido_cupon
        FOREIGN KEY (idCupon)
        REFERENCES cupones (idCupon)
        ON UPDATE CASCADE
        ON DELETE SET NULL;

-- Paso 6 — Confirmar que quedó correctamente:
SELECT CONSTRAINT_NAME, DELETE_RULE, UPDATE_RULE
FROM information_schema.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = 'ventas_catalogo'
  AND TABLE_NAME = 'pedidos';
<?php

namespace app\models;

class Carrito {

    public static function obtenerPorCliente(int $idCliente): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM carritos WHERE idCliente = :idCliente AND estado = 'activo'"
        );
        $stmt->execute(['idCliente' => $idCliente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function crear(int $idCliente): int {
        $stmt = self::db()->prepare(
            "INSERT INTO carritos (idCliente) VALUES (:idCliente)"
        );
        $stmt->execute(['idCliente' => $idCliente]);
        return (int) self::db()->lastInsertId();
    }
 
    /** Cambia el estado a 'convertido' cuando se genera un pedido */
    public static function convertir(int $idCarrito): bool {
        $stmt = self::db()->prepare(
            "UPDATE carritos SET estado = 'convertido' WHERE idCarrito = :id"
        );
        return $stmt->execute(['id' => $idCarrito]);
    }
 
    public static function marcarAbandonado(int $idCarrito): bool {
        $stmt = self::db()->prepare(
            "UPDATE carritos SET estado = 'abandonado' WHERE idCarrito = :id"
        );
        return $stmt->execute(['id' => $idCarrito]);
    }
 
    // Items del carrito 
 
    public static function obtenerItems(int $idCarrito): array {
        $sql = "SELECT ic.*, p.nombre, p.imagenes, p.descuento,
                       (ic.cantidad * ic.precioUnitario) AS subtotal
                FROM items_carrito ic
                JOIN productos p ON ic.idProducto = p.idProducto
                WHERE ic.idCarrito = :idCarrito";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['idCarrito' => $idCarrito]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    /**
     * Agrega un producto al carrito. Si ya existe, incrementa la cantidad.
     * $data = ['idCarrito','idProducto','cantidad','precioUnitario']
     */
    public static function agregarItem(array $data): bool {
        $sql = "INSERT INTO items_carrito
                    (idCarrito, idProducto, cantidad, precioUnitario)
                VALUES
                    (:idCarrito, :idProducto, :cantidad, :precioUnitario)
                ON DUPLICATE KEY UPDATE
                    cantidad = cantidad + VALUES(cantidad)";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    /**
     * $data = ['cantidad','idCarrito','idProducto']
     */
    public static function actualizarCantidad(array $data): bool {
        $sql = "UPDATE items_carrito
                SET cantidad = :cantidad
                WHERE idCarrito = :idCarrito AND idProducto = :idProducto";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    public static function eliminarItem(int $idCarrito, int $idProducto): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM items_carrito
             WHERE idCarrito = :idCarrito AND idProducto = :idProducto"
        );
        return $stmt->execute(['idCarrito' => $idCarrito, 'idProducto' => $idProducto]);
    }
 
    public static function vaciar(int $idCarrito): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM items_carrito WHERE idCarrito = :idCarrito"
        );
        return $stmt->execute(['idCarrito' => $idCarrito]);
    }
 
    // Totales 
 
    public static function calcularTotal(int $idCarrito): float {
        $stmt = self::db()->prepare(
            "SELECT COALESCE(SUM(cantidad * precioUnitario), 0)
             FROM items_carrito WHERE idCarrito = :id"
        );
        $stmt->execute(['id' => $idCarrito]);
        return (float) $stmt->fetchColumn();
    }
 
    public static function contarItems(int $idCarrito): int {
        $stmt = self::db()->prepare(
            "SELECT COALESCE(SUM(cantidad), 0) FROM items_carrito WHERE idCarrito = :id"
        );
        $stmt->execute(['id' => $idCarrito]);
        return (int) $stmt->fetchColumn();
    }

}
<?php

namespace app\models;

use app\core\Model;

class DetallePedido extends Model {
 
    public static function obtenerPorPedido(int $idPedido): array {
        $sql = "SELECT dp.*, p.nombre AS producto, p.imagenes
                FROM detalle_pedido dp
                JOIN productos p ON dp.idProducto = p.idProducto
                WHERE dp.idPedido = :idPedido";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['idPedido' => $idPedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    /*
      $data = ['cantidad','precioUnitario','subtotal','idPedido','idProducto']
     */
    public static function crear(array $data): bool {
        $sql = "INSERT INTO detalle_pedido
                    (cantidad, precioUnitario, subtotal, idPedido, idProducto)
                VALUES
                    (:cantidad, :precioUnitario, :subtotal, :idPedido, :idProducto)";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    /* Inserta múltiples ítems de una sola vez */
    public static function crearLote(array $items): bool {
        $db = self::db();
        $sql = "INSERT INTO detalle_pedido
                    (cantidad, precioUnitario, subtotal, idPedido, idProducto)
                VALUES
                    (:cantidad, :precioUnitario, :subtotal, :idPedido, :idProducto)";
 
        $stmt = $db->prepare($sql);
        foreach ($items as $item) {
            if (!$stmt->execute($item)) return false;
        }
        return true;
    }
 
    public static function eliminarPorPedido(int $idPedido): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM detalle_pedido WHERE idPedido = :idPedido"
        );
        return $stmt->execute(['idPedido' => $idPedido]);
    }
}
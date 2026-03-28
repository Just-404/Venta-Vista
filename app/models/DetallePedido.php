<?php

namespace app\models;

use app\core\Model;

class DetallePedido extends Model {

    public static function crear($data){

        $sql = "INSERT INTO detalle_pedido
        (cantidad, precioUnitario, subtotal, idPedido, idProducto)
        VALUES
        (:cantidad, :precioUnitario, :subtotal, :idPedido, :idProducto)";

        return self::db()->prepare($sql)->execute($data);
    }

}
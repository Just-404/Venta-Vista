<?php

namespace app\models;

use app\core\Model;

class Pago extends Model {

    public static function crear($data){

        $sql = "INSERT INTO pagos
        (monto, estado, referencia, metodoPago, idPedido)
        VALUES
        (:monto, :estado, :referencia, :metodoPago, :idPedido)";

        return self::db()->prepare($sql)->execute($data);
    }

}
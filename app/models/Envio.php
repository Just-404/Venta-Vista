<?php

namespace app\models;

use app\core\Model;

class Envio extends Model {

    public static function crear($data){

        $sql = "INSERT INTO envios
        (codigoRastreo, empresa, fechaEstimada, fechaEntrega, idPedido, idDireccion)
        VALUES
        (:codigoRastreo, :empresa, :fechaEstimada, :fechaEntrega, :idPedido, :idDireccion)";

        return self::db()->prepare($sql)->execute($data);
    }

}
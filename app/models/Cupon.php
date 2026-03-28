<?php

namespace app\models;

use app\core\Model;
use PDO;

class Cupon extends Model {

    public static function validar($codigo){

        $sql = "SELECT * FROM cupones
        WHERE codigo = :codigo
        AND fecha_inicio <= NOW()
        AND fecha_fin >= NOW()";

        $stmt = self::db()->prepare($sql);

        $stmt->execute(['codigo'=>$codigo]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function crear($data){
         $sql = "INSERT INTO cupones
        (codigo, tipo, descuento, usoMaximo, usosActuales, fechaInicio, fechaVencimiento, activo)
        VALUES
        (:codigo, :tipo, :descuento, :usoMaximo, :usosActuales, :fechaInicio, :fechaVencimiento, :activo)";

        return self::db()->prepare($sql)->execute($data);
    }
}
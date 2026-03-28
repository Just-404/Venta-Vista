<?php

namespace app\models;

use app\core\Model;
use PDO;

class Direccion extends Model {

    public static function obtenerPorCliente($id){

        $sql = "SELECT * FROM direcciones
                WHERE idCliente = :id";

        $stmt = self::db()->prepare($sql);

        $stmt->execute(['id'=>$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($data){

        $sql = "INSERT INTO direcciones
        (calle, ciudad, provincia, codigoPostal, esPrincipal, idCliente)
        VALUES
        (:calle, :ciudad, :provincia, :codigoPostal, :esPrincipal, :idCliente)";

        return self::db()->prepare($sql)->execute($data);
    }

}
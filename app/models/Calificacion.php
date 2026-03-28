<?php

namespace app\models;

use app\core\Model;
use PDO;

class Calificacion extends Model {

    public static function crear($data){

        $sql = "INSERT INTO calificaciones
        (nota, comentario, idProducto, idCliente)
        VALUES
        (:nota, :comentario, :idProducto, :idCliente)";

        return self::db()->prepare($sql)->execute($data);
    }

    public static function obtenerPorProducto($id){

        $sql = "SELECT * FROM calificaciones
                WHERE idProducto = :id";

        $stmt = self::db()->prepare($sql);

        $stmt->execute(['id'=>$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
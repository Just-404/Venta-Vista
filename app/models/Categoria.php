<?php

namespace app\models;

use app\core\Model;
use PDO;

class Categoria extends Model {

    public static function obtenerTodas(){

        return self::db()
            ->query("SELECT * FROM categorias")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

     public static function crear($data){

        $sql = "INSERT INTO categorias 
        (nombre, descripcion)
        VALUES 
        (:nombre,:descripcion)";

        return self::db()->prepare($sql)->execute($data);
    }
}
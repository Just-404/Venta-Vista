<?php

namespace app\models;

use app\core\Model;
use PDO;

class Vendedor extends Model {

    public static function obtenerTodos(){

        $sql = "SELECT * FROM usuarios 
                WHERE rol = 'Vendedor'";

        return self::db()->query($sql)
        ->fetchAll(PDO::FETCH_ASSOC);
    }

}
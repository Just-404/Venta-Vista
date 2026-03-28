<?php

namespace app\models;

use app\core\Model;
use PDO;

class Cliente extends Model {

    public static function obtenerTodos(){

        return self::db()
        ->query("SELECT * FROM clientes")
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($data){

        $sql = "INSERT INTO clientes 
        (nombre, apellidos, cedula, telefono, email, :idUsuario)
        VALUES
        (:nombre,:apellidos,:cedula,:telefono,:email, :idUsuario)";

        return self::db()->prepare($sql)->execute($data);
    }

}
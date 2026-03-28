<?php

namespace app\models;

use app\core\Model;
use PDO;

class Usuario extends Model{
 
    public static function obtenerTodos(){
        $sql = "SELECT * FROM usuarios";
        $stmt = self::db()->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function buscarPorUsuario($username) {

        $sql = `SELECT * 
            FROM usuarios 
            WHERE nombreUsuario = :usuario
            AND activo = 1`;

        $stmt = self::db()->prepare($sql);

        $stmt->execute([
            'usuario' => $username
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

     public static function crear($data){
        /* Estrcutura esperada de $data = [
            'nombreUsuario' => 'juan123',
            'contrasena' => password_hash('123456', PASSWORD_DEFAULT),
            'email' => 'juan@gmail.com',
            'idRol' => 2,
            'estado' => 1
            ]; */
            
        $sql = "INSERT INTO usuarios 
                (nombreUsuario, contrasena, email, idRol)
                VALUES 
                (:nombreUsuario, :contrasena, :email, :idRol)";

        $stmt = self::db()->prepare($sql);

        return $stmt->execute($data);
    }
}

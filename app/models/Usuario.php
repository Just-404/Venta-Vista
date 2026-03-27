<?php

namespace app\models;

use app\core\Database;
use PDO;

class Usuario{
    public static function buscarPorUsuario($username) {

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT * 
            FROM usuarios 
            WHERE nombreUsuario = :username
            AND activo = 1
        ");

        $stmt->execute([
            'username' => $username
        ]);
    
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }
}

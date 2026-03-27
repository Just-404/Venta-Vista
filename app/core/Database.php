<?php

namespace app\core;

use PDO;
use PDOException;

class Database {

    private static $instance = null;

    public static function getConnection() {

        if (self::$instance === null) {


            try {

               self::$instance = new PDO(
                    "mysql:host=" . DB_HOST . 
                    ";dbname=" . DB_NAME . 
                    ";charset=" . charset,
                    DB_USER,
                    DB_PASS
                );

                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {

                die("Error de conexión: " . $e->getMessage());

            }
        }

        return self::$instance;
    }

}
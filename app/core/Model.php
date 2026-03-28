<?php

namespace app\core;

use app\core\Database;

class Model {

    protected static function db(){
        return Database::getConnection();
    }

}
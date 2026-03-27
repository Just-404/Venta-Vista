<?php

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use app\core\Router;

$router = new Router();

$router->run();
?>
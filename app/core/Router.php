<?php

namespace app\core;

class Router {
    public function run(){
        $url = $_GET['url'] ?? '';
        $url = trim($url, '/');

        $routes = require dirname(__DIR__, 2) . '/config/routes.php';

        if (isset($routes[$url])) {
            $controllerName = $routes[$url]['controller'];
            $action = $routes[$url]['action'];
        }
        else{
            $controllerName = 'AuthController';
            $action = 'login';
        }

        $controllerClass = "app\\controllers\\$controllerName";

        if(!class_exists($controllerClass)){
            die("Controlador no encontrado: $controllerClass");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            die("Método no encontrado: $action");
        }

        $controller->$action();
    }
}

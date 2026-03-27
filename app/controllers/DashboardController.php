<?php

namespace app\controllers;

class DashboardController {

    public function index() {

        AuthController::checkAuth();

        echo "Bienvenido " . $_SESSION['usuario']['username'];
    }

}
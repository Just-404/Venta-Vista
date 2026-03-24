<?php
namespace app\controllers;
class AuthController {

    public function login() {

        if (isset($_SESSION['user'])) {
            header("Location: " . BASE_URL . "dashboard");
            exit;
        }

        require '../app/views/auth/login.php';
    }

}
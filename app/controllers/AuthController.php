<?php
namespace app\controllers;
use app\models\Usuario;
class AuthController {

    public function login() {

    // Si el usuario está logueado, se redirigue a dashboard
        if (isset($_SESSION['usuario'])) {
            header("Location: " . BASE_URL . "dashboard");
            exit;
        }
        require '../app/views/auth/login.php';
    }

    public function autenticar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nombreUsuario = $_POST['usuario'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $usuario = Usuario::buscarPorUsuario($nombreUsuario);

            if($usuario && password_verify($password, trim($usuario['contrasena']))){
                $_SESSION['usuario'] = [
                    'id' => $usuario['idUsuario'],
                    'username' => $usuario['nombreUsuario'],
                    'rol' => $usuario['idRol']
                ];

                header("Location: " . BASE_URL . "dashboard");
                exit;
            }
            else{
                $error = "Usuario o contraseña incorrectas";
                require '../app/views/auth/login.php';
            }
        }
    }

    public function logout(){
        session_destroy();

        header("Location: " . BASE_URL);
        exit;
    }

    // Funcion para proteger rutas que no esten autorizadas
    public static function checkAuth(){
        if(!isset($_SESSION['usuario'])){

        header("Location: ".BASE_URL);
        exit;
        }
    }
}
<?php
namespace app\controllers;
use app\core\Controller;
use app\models\Usuario;

class AuthController extends Controller {

    // GET /
    public function login(): void {
        if (isset($_SESSION['usuario'])) {
            $this->redirect('dashboard');
        }

        $this->render('auth/login', [
            'flash' => $this->getFlash(),
        ]);
    }

    // POST /login
    public function autenticar(): void {
        if (!$this->isPost()) {
            $this->redirect('');
        }

        $nombreUsuario = $this->post('usuario');
        $password      = $this->post('password');
        $usuario       = Usuario::buscarPorUsuario($nombreUsuario);
        
        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            $_SESSION['usuario'] = [
                'id'       => $usuario['idUsuario'],
                'username' => $usuario['nombreUsuario'],
                'rol'      => $usuario['idRol'],
            ];

            $this->redirect('dashboard');
        }

        $this->setFlash('error', 'Usuario o contraseña incorrectos.');
        $this->redirect('');
    }

    // GET /logout
    public function logout(): void {
        session_destroy();
        $this->redirect('');
    }

    // Método estático usado por Controller::requireAuth()
    public static function checkAuth(): void {
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . BASE_URL);
            exit;
        }
    }
}
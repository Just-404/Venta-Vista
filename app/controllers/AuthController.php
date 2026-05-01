<?php
namespace app\controllers;
use app\core\Controller;
use app\models\Usuario;
use app\models\Cliente;
use app\models\Notificacion;

class AuthController extends Controller {

    // GET /
    public function login(): void {
        if (isset($_SESSION['usuario'])) {
            $this->redirect('dashboard');
        }

        $this->renderSinLayout('auth/login', [
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

    public function registrarUsuario(): void{
         if (isset($_SESSION['usuario'])) {
            $this->redirect('dashboard');
        }

        $this->renderSinLayout('auth/register', [
            'flash' => $this->getFlash(),
        ]);
    }

    public function procesarRegistro() : void{
        if (!$this->isPost()) {
        $this->redirect('registro');
    }

    //  1. Obtener datos
    $nombre     = trim($this->post('nombre'));
    $apellidos  = trim($this->post('apellidos'));
    $cedula     = trim($this->post('cedula'));
    $telefono   = trim($this->post('telefono'));
    $email      = trim($this->post('email'));
    $nombreUsuario = trim($this->post("usuario"));
    $password   = $this->post('password');
    $confirmar  = $this->post('confirmar');

    //  2. Validaciones básicas
    if (!$nombreUsuario || !$nombre || !$apellidos || !$cedula || !$email || !$password) {
        $this->setFlash('error', 'Todos los campos obligatorios deben completarse.');
        $this->redirect('registro');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->setFlash('error', 'Correo electrónico inválido.');
        $this->redirect('registro');
    }

    if ($password !== $confirmar) {
        $this->setFlash('error', 'Las contraseñas no coinciden.');
        $this->redirect('registro');
    }

    if (strlen($password) < 8) {
        $this->setFlash('error', 'La contraseña debe tener al menos 8 caracteres.');
        $this->redirect('registro');
    }

    //  4. Crear usuario
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $idUsuario = Usuario::crearYObtenerID([
        'nombreUsuario' => $nombreUsuario, 
        'contrasena'    => $passwordHash,
        'email'         => $email,
        'idRol'         => 3, // Cliente
    ]);

    if (!$idUsuario) {
        $this->setFlash('error', 'Error al crear el usuario.');
        $this->redirect('registro');
    }

    // 5. Crear cliente
    $ok = Cliente::crear([
        'nombre'    => $nombre,
        'apellidos' => $apellidos,
        'cedula'    => $cedula,
        'telefono'  => $telefono,
        'email'     => $email,
        'idUsuario' => $idUsuario,
    ]);

    if (!$ok) {
        $this->setFlash('error', 'Error al crear el cliente.');
        $this->redirect('registro');
    }

    //  6. Notificaciones
    $nombreCompleto = $nombre . ' ' . $apellidos;

    $notifData = [
        'tipo'    => 'nuevo_cliente',
        'titulo'  => 'Nuevo cliente registrado',
        'mensaje' => $nombreCompleto . ' se ha registrado en el sistema.',
        'url'     => BASE_URL . 'clientes',
    ];

    Notificacion::crearParaRol(1, $notifData); // Admin

    //  7. Éxito
    $this->autenticar();
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
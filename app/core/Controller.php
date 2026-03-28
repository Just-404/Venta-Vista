<?php

namespace app\core;

use app\controllers\AuthController;

abstract class Controller {

    // Redireccion de Vistas 

    /**
     * Carga una vista pasándole variables.
     * Uso: $this->render('productos/index', ['productos' => $lista])
     */
    protected function render(string $vista, array $datos = []): void {
        extract($datos);
        $ruta = dirname(__DIR__) . "/views/{$vista}.php";

        if (!file_exists($ruta)) {
            die("Vista no encontrada: {$vista}");
        }

        require $ruta;
    }

    // Redirecciones 

    protected function redirect(string $ruta): void {
        header("Location: " . BASE_URL . ltrim($ruta, '/'));
        exit;
    }

    // Respuestas JSON (para peticiones AJAX)

    protected function json(mixed $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Request helpers

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /* Obtiene un valor del POST con trim y null si no existe */
    protected function post(string $key, mixed $default = null): mixed {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    /* Obtiene un valor del GET con trim y null si no existe */
    protected function get(string $key, mixed $default = null): mixed {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }

    // Sesión / Auth

    /** Protege cualquier ruta: redirige al login si no hay sesión */
    protected function requireAuth(): void {
        AuthController::checkAuth();
    }

    /* Devuelve el usuario de la sesión actual */
    protected function usuarioActual(): array|null {
        return $_SESSION['usuario'] ?? null;
    }

    /** Comprueba si el rol del usuario coincide con el requerido */
    protected function requireRol(int $idRol): void {
        $usuario = $this->usuarioActual();
        if (!$usuario || (int) $usuario['rol'] !== $idRol) {
            $this->redirect('dashboard');
        }
    }

    // Mensajes flash

    protected function setFlash(string $tipo, string $mensaje): void {
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
    }

    protected function getFlash(): array|null {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}
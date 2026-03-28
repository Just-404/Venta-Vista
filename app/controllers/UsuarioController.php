<?php
namespace app\controllers;
 
use app\core\Controller;
use app\models\Usuario;
use app\models\Administrador;
use app\models\Vendedor;
 
class UsuarioController extends Controller {
 
    // GET /usuarios
    public function index(): void {
        $this->requireAuth();
 
        $this->render('usuarios/index', [
            'usuarios' => Usuario::obtenerTodos(),
            'flash'    => $this->getFlash(),
            'usuario'  => $this->usuarioActual(),
        ]);
    }
 
    // GET  /usuarios/crear
    // POST /usuarios/crear
    public function crear(): void {
        $this->requireAuth();
 
        if ($this->isPost()) {
            $idRol = (int) $this->post('idRol');
 
            $idUsuario = Usuario::crearYObtenerID([
                'nombreUsuario' => $this->post('nombreUsuario'),
                'contrasena'    => password_hash($this->post('password'), PASSWORD_DEFAULT),
                'email'         => $this->post('email'),
                'idRol'         => $idRol,
            ]);
 
            // Crear perfil según rol
            if ($idUsuario) {
                $perfil = [
                    'nombre'    => $this->post('nombre'),
                    'apellidos' => $this->post('apellidos'),
                    'cedula'    => $this->post('cedula'),
                    'telefono'  => $this->post('telefono'),
                    'idUsuario' => $idUsuario,
                ];
 
                match ($idRol) {
                    1 => Administrador::crear($perfil),
                    2 => Vendedor::crear($perfil),
                    default => null,
                };
 
                $this->setFlash('success', 'Usuario creado correctamente.');
            } else {
                $this->setFlash('error', 'Error al crear el usuario.');
            }
 
            $this->redirect('usuarios');
        }
 
        $this->render('usuarios/crear', ['usuario' => $this->usuarioActual()]);
    }
 
    // POST /usuarios/estado  — activar / desactivar
    public function estado(): void {
        $this->requireAuth();
 
        $id     = (int)  $this->post('id');
        $activo = (bool) $this->post('activo');
        $ok     = Usuario::cambiarEstado($id, $activo);
 
        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Estado actualizado.' : 'Error al cambiar estado.');
 
        $this->redirect('usuarios');
    }
 
    // POST /usuarios/password
    public function password(): void {
        $this->requireAuth();
 
        $id   = (int) $this->post('id');
        $hash = password_hash($this->post('password'), PASSWORD_DEFAULT);
        $ok   = Usuario::actualizarContrasena($id, $hash);
 
        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Contraseña actualizada.' : 'Error al actualizar contraseña.');
 
        $this->redirect('usuarios');
    }
 
    // POST /usuarios/eliminar
    public function eliminar(): void {
        $this->requireAuth();
 
        $id = (int) $this->post('id');
        $ok = Usuario::eliminarFisico($id);
 
        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Usuario eliminado.' : 'Error al eliminar.');
 
        $this->redirect('usuarios');
    }
}
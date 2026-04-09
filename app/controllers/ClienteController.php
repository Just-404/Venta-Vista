<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Cliente;
use app\models\Usuario;
use app\models\Direccion;

class ClienteController extends Controller {

    // GET /clientes
    public function index(): void {
        $this->requireAuth();

        $this->render('clientes/index', [
            'clientes' => Cliente::obtenerTodos(),
            'flash'    => $this->getFlash(),
            'usuario'  => $this->usuarioActual(),
        ]);
    }

    // GET  /clientes/crear
    // POST /clientes/crear
    public function crear(): void {
        $this->requireAuth();

        if ($this->isPost()) {
            // 1. Crear usuario
            $passwordHash = password_hash($this->post('password'), PASSWORD_DEFAULT);

            $idUsuario = Usuario::crearYObtenerID([
                'nombreUsuario' => $this->post('nombreUsuario'),
                'contrasena'    => $passwordHash,
                'email'         => $this->post('email'),
                'idRol'         => 3, // Rol Cliente
            ]);

            if (!$idUsuario) {
                $this->setFlash('error', 'Error al crear el usuario.');
                $this->redirect('clientes');
            }

            // 2. Crear perfil cliente
            $ok = Cliente::crear([
                'nombre'    => $this->post('nombre'),
                'apellidos' => $this->post('apellidos'),
                'cedula'    => $this->post('cedula'),
                'telefono'  => $this->post('telefono'),
                'email'     => $this->post('email'),
                'idUsuario' => $idUsuario,
            ]);

            if ($ok) {
                $this->setFlash('success', 'Cliente registrado correctamente.');
            } else {
                $this->setFlash('error', 'Error al crear el cliente.');
            }

            $this->redirect('clientes');
        }

        $this->render('clientes/crear', [
            'usuario' => $this->usuarioActual(),
        ]);
    }

    // GET  /clientes/editar?id=X
    // POST /clientes/editar
    public function editar(): void {
        $this->requireAuth();

        $id      = (int) $this->get('id');
        $cliente = Cliente::obtenerPorId($id);

        if (!$cliente) {
            $this->setFlash('error', 'Cliente no encontrado.');
            $this->redirect('clientes');
        }

        if ($this->isPost()) {
            $ok = Cliente::actualizar([
                'nombre'     => $this->post('nombre'),
                'apellidos'  => $this->post('apellidos'),
                'cedula'     => $this->post('cedula'),
                'telefono'   => $this->post('telefono'),
                'email'      => $this->post('email'),
                'idCliente'  => $id,
            ]);

            $this->setFlash($ok ? 'success' : 'error',
                $ok ? 'Cliente actualizado.' : 'Error al actualizar.');

            $this->redirect('clientes');
        }

        $this->render('clientes/editar', [
            'cliente' => $cliente,
            'usuario' => $this->usuarioActual(),
        ]);
    }

    // GET /clientes/ver?id=X
    public function ver(): void {
        $this->requireAuth();

        $id      = (int) $this->get('id');
        $cliente = Cliente::obtenerPorId($id);

        if (!$cliente) {
            $this->setFlash('error', 'Cliente no encontrado.');
            $this->redirect('clientes');
        }

        $this->render('clientes/ver', [
            'cliente'     => $cliente,
            'direcciones' => Direccion::obtenerPorCliente($id),
            'usuario'     => $this->usuarioActual(),
        ]);
    }

    // POST /clientes/eliminar
    public function eliminar(): void {
        $this->requireAuth();

        $id = (int) $this->post('id');

        // Elimina en cascada por FK (usuario → cliente)
        $cliente = Cliente::obtenerPorId($id);
        if ($cliente) {
            Usuario::eliminarFisico((int) $cliente['idUsuario']);
            $this->setFlash('success', 'Cliente eliminado.');
        } else {
            $this->setFlash('error', 'Cliente no encontrado.');
        }

        $this->redirect('clientes');
    }
}
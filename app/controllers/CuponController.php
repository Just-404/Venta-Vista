<?php

namespace app\controllers;
 
use app\core\Controller;
use app\models\Cupon;
 
class CuponController extends Controller {
 
    // GET /cupones
    public function index(): void {
        $this->requireAuth();
 
        $this->render('cupones/index', [
            'cupones' => Cupon::obtenerTodos(),
            'flash'   => $this->getFlash(),
            'usuario' => $this->usuarioActual(),
        ]);
    }
 
    // GET  /cupones/crear
    // POST /cupones/crear
    public function crear(): void {
        $this->requireAuth();
 
        if ($this->isPost()) {
            $ok = Cupon::crear([
                'codigo'           => strtoupper($this->post('codigo')),
                'tipo'             => $this->post('tipo'),
                'descuento'        => $this->post('descuento'),
                'usoMaximo'        => $this->post('usoMaximo', 1),
                'usosActuales'     => 0,
                'fechaInicio'      => $this->post('fechaInicio'),
                'fechaVencimiento' => $this->post('fechaVencimiento'),
                'activo'           => 1,
            ]);
 
            $this->setFlash($ok ? 'success' : 'error',
                $ok ? 'Cupón creado.' : 'Error al crear el cupón.');
            $this->redirect('cupones');
        }
 
        $this->render('cupones/crear', ['usuario' => $this->usuarioActual()]);
    }
 
    // GET  /cupones/editar?id=X
    // POST /cupones/editar
    public function editar(): void {
        $this->requireAuth();
 
        $id    = (int) $this->get('id');
        $cupon = Cupon::obtenerPorId($id);
 
        if (!$cupon) {
            $this->setFlash('error', 'Cupón no encontrado.');
            $this->redirect('cupones');
        }
 
        if ($this->isPost()) {
            $ok = Cupon::actualizar([
                'codigo'           => strtoupper($this->post('codigo')),
                'tipo'             => $this->post('tipo'),
                'descuento'        => $this->post('descuento'),
                'usoMaximo'        => $this->post('usoMaximo'),
                'fechaInicio'      => $this->post('fechaInicio'),
                'fechaVencimiento' => $this->post('fechaVencimiento'),
                'activo'           => $this->post('activo', 1),
                'idCupon'          => $id,
            ]);
 
            $this->setFlash($ok ? 'success' : 'error',
                $ok ? 'Cupón actualizado.' : 'Error al actualizar.');
            $this->redirect('cupones');
        }
 
        $this->render('cupones/editar', [
            'cupon'   => $cupon,
            'usuario' => $this->usuarioActual(),
        ]);
    }
 
    // POST /cupones/eliminar
    public function eliminar(): void {
        $this->requireAuth();
 
        $id = (int) $this->post('id');
        $ok = Cupon::eliminar($id);
 
        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Cupón eliminado.' : 'Error al eliminar.');
        $this->redirect('cupones');
    }
 
    // POST /cupones/validar y respuesta JSON para AJAX
    public function validar(): void {
        $this->requireAuth();
 
        $codigo = $this->post('codigo');
        $cupon  = Cupon::validar($codigo);
 
        if ($cupon) {
            $this->json(['valido' => true, 'cupon' => $cupon]);
        } else {
            $this->json(['valido' => false, 'mensaje' => 'Cupón inválido o expirado.'], 404);
        }
    }
}
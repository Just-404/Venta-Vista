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
            $codigo = strtoupper(trim($this->post('codigo')));

            // 1. Validar si el código ya existe
            if (Cupon::existeCodigo($codigo)) {
                // En vez de redirigir, renderizamos la vista de nuevo y le pasamos los datos previos
                $this->render('cupones/crear', [
                    'error'   => "Ya existe un cupón con el código «{$codigo}». Intenta con otro.",
                    'old'     => $_POST, // Pasamos lo que el usuario escribió
                    'usuario' => $this->usuarioActual()
                ]);
                return; // Detenemos la ejecución
            }

            // 2. Si no existe, lo creamos
            $ok = Cupon::crear([
                'codigo'           => $codigo,
                'tipo'             => $this->post('tipo'),
                'descuento'        => $this->post('descuento'),
                'usoMaximo'        => $this->post('usoMaximo', 1),
                'usosActuales'     => 0,
                'fechaInicio'      => $this->post('fechaInicio'),
                'fechaVencimiento' => $this->post('fechaVencimiento'),
                'activo'           => 1,
            ]);
 
            $this->setFlash($ok ? 'success' : 'error',
                $ok ? 'Cupón creado con éxito.' : 'Error al crear el cupón.');
            $this->redirect('cupones'); // Si todo sale bien, va al índice
            return;
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
            return;
        }
 
        if ($this->isPost()) {
            $codigo = strtoupper(trim($this->post('codigo')));

            // 1. Validar duplicado excluyendo el ID de este mismo cupón
            if (Cupon::existeCodigo($codigo, $id)) {
                // Combinamos los datos de la base de datos con los que el usuario intentó enviar
                // Así mantenemos sus cambios en pantalla sin perderlos
                $cuponEditado = array_merge($cupon, $_POST);
                
                $this->render('cupones/editar', [
                    'error'   => "El código «{$codigo}» ya está siendo utilizado por otro cupón.",
                    'cupon'   => $cuponEditado,
                    'usuario' => $this->usuarioActual()
                ]);
                return; // Detenemos la ejecución
            }

            // 2. Si todo está en orden, actualizamos
            $ok = Cupon::actualizar([
                'codigo'           => $codigo,
                'tipo'             => $this->post('tipo'),
                'descuento'        => $this->post('descuento'),
                'usoMaximo'        => $this->post('usoMaximo'),
                'fechaInicio'      => $this->post('fechaInicio'),
                'fechaVencimiento' => $this->post('fechaVencimiento'),
                'activo'           => $this->post('activo', 1),
                'idCupon'          => $id,
            ]);
 
            $this->setFlash($ok ? 'success' : 'error',
                $ok ? 'Cupón actualizado correctamente.' : 'Error al actualizar el cupón.');
            $this->redirect('cupones');
            return;
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
            $_SESSION['cupon'] = [
                'id' => $cupon['idCupon'],
                'codigo' => $cupon['codigo'],
                'tipo' => $cupon['tipo'],
                'descuento' => $cupon['descuento']
            ];
            
            $this->json(['valido' => true, 'cupon' => $cupon]);
        } else {
            unset($_SESSION['cupon']);
            $this->json(['valido' => false, 'mensaje' => 'Cupón inválido o expirado.'], 404);
        }

    }
}
<?php
namespace app\controllers;
 
use app\core\Controller;
use app\models\Pago;
use app\models\Pedido;
 
class PagoController extends Controller {
 
    // GET /pagos
    public function index(): void {
        $this->requireAuth();
 
        $this->render('pagos/index', [
            'pagos'   => Pago::obtenerTodos(),
            'flash'   => $this->getFlash(),
            'usuario' => $this->usuarioActual(),
        ]);
    }
 
    // POST /pagos/crear
    public function crear(): void {
        $this->requireAuth();
 
        $ok = Pago::crear([
            'monto'      => $this->post('monto'),
            'estado'     => 'Pendiente',
            'referencia' => $this->post('referencia'),
            'metodoPago' => $this->post('metodoPago'),
            'idPedido'   => $this->post('idPedido'),
        ]);
 
        if ($ok) {
            Pedido::actualizarEstado((int) $this->post('idPedido'), 'Confirmado');
            $this->setFlash('success', 'Pago registrado.');
        } else {
            $this->setFlash('error', 'Error al registrar el pago.');
        }
 
        $this->redirect('pedidos/ver?id=' . $this->post('idPedido'));
    }
 
    // POST /pagos/estado
    public function estado(): void {
        $this->requireAuth();
 
        $id     = (int)    $this->post('id');
        $estado = (string) $this->post('estado');
        $ok     = Pago::actualizarEstado($id, $estado);
 
        $this->setFlash($ok ? 'success' : 'error',
            $ok ? "Pago marcado como: $estado." : 'Error al actualizar pago.');
 
        $this->redirect('pagos');
    }
}
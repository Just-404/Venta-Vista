<?php
namespace app\controllers;
 
use app\core\Controller;
use app\models\Envio;
use app\models\Pedido;
use app\models\Direccion;
 
class EnvioController extends Controller {
 
    // GET /envios
    public function index(): void {
        $this->requireAuth();
 
        $this->render('envios/index', [
            'envios'  => Envio::obtenerTodos(),
            'flash'   => $this->getFlash(),
            'usuario' => $this->usuarioActual(),
        ]);
    }
 
    // POST /envios/crear
    public function crear(): void {
        $this->requireAuth();
 
        $ok = Envio::crear([
            'codigoRastreo' => $this->post('codigoRastreo'),
            'empresa'       => $this->post('empresa'),
            'fechaEstimada' => $this->post('fechaEstimada'),
            'fechaEntrega'  => null,
            'idPedido'      => $this->post('idPedido'),
            'idDireccion'   => $this->post('idDireccion'),
        ]);
 
        if ($ok) {
            Pedido::actualizarEstado((int) $this->post('idPedido'), 'Enviado');
            $this->setFlash('success', 'Envío creado.');
        } else {
            $this->setFlash('error', 'Error al crear el envío.');
        }
 
        $this->redirect('pedidos/ver?id=' . $this->post('idPedido'));
    }
 
    // POST /envios/entregar
    public function entregar(): void {
        $this->requireAuth();
 
        $id    = (int) $this->post('id');
        $fecha = date('Y-m-d');
        $ok    = Envio::registrarEntrega($id, $fecha);
 
        if ($ok) {
            $envio = Envio::obtenerPorId($id);
            if ($envio) Pedido::actualizarEstado((int) $envio['idPedido'], 'Entregado');
            $this->setFlash('success', 'Entrega registrada.');
        } else {
            $this->setFlash('error', 'Error al registrar entrega.');
        }
 
        $this->redirect('envios');
    }
 
    // POST /envios/rastreo
    public function rastreo(): void {
        $this->requireAuth();
 
        $ok = Envio::actualizarRastreo(
            (int) $this->post('id'),
            $this->post('codigoRastreo'),
            $this->post('empresa')
        );
 
        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Rastreo actualizado.' : 'Error al actualizar rastreo.');
 
        $this->redirect('envios');
    }
}
<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Envio;
use app\models\Pedido;
use app\models\Direccion;
use app\models\Cliente;
use app\models\Usuario;
use app\models\Notificacion;
use app\models\Configuracion;

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

        $idPedido = (int) $this->post('idPedido');

        $ok = Envio::crear([
            'codigoRastreo' => $this->post('codigoRastreo'),
            'empresa'       => $this->post('empresa'),
            'fechaEstimada' => $this->post('fechaEstimada'),
            'fechaEntrega'  => null,
            'idPedido'      => $idPedido,
            'idDireccion'   => $this->post('idDireccion'),
        ]);

        if ($ok) {
            Pedido::actualizarEstado($idPedido, 'Enviado');

            // Notificar al cliente
            $empresa  = $this->post('empresa') ?? 'transportista';
            $codigo   = $this->post('codigoRastreo') ?? '';
            $this->notificarClientePedido(
                $idPedido,
                'envio_actualizado',
                'Tu pedido está en camino 🚚',
                "Empresa: {$empresa}" . ($codigo ? " — Rastreo: {$codigo}" : ''),
                BASE_URL . 'pedidos/ver?id=' . $idPedido,
                'notif_estado_pedido'
            );

            $this->setFlash('success', 'Envío creado.');
        } else {
            $this->setFlash('error', 'Error al crear el envío.');
        }

        $this->redirect('pedidos/ver?id=' . $idPedido);
    }

    // POST /envios/entregar
    public function entregar(): void {
        $this->requireAuth();

        $id    = (int) $this->post('id');
        $fecha = date('Y-m-d');
        $ok    = Envio::registrarEntrega($id, $fecha);

        if ($ok) {
            $envio = Envio::obtenerPorId($id);
            if ($envio) {
                $idPedido = (int) $envio['idPedido'];
                Pedido::actualizarEstado($idPedido, 'Entregado');

                // Notificar al cliente
                $this->notificarClientePedido(
                    $idPedido,
                    'envio_actualizado',
                    '¡Tu pedido fue entregado! 📦',
                    'Tu pedido ha sido entregado exitosamente el ' . date('d/m/Y') . '.',
                    BASE_URL . 'pedidos/ver?id=' . $idPedido,
                    'notif_estado_pedido'
                );
            }
            $this->setFlash('success', 'Entrega registrada.');
        } else {
            $this->setFlash('error', 'Error al registrar entrega.');
        }

        $this->redirect('envios');
    }

    // POST /envios/rastreo
    public function rastreo(): void {
        $this->requireAuth();

        $id     = (int) $this->post('id');
        $codigo = $this->post('codigoRastreo');
        $ok     = Envio::actualizarRastreo($id, $codigo, $this->post('empresa'));

        if ($ok) {
            $envio = Envio::obtenerPorId($id);
            if ($envio) {
                $this->notificarClientePedido(
                    (int) $envio['idPedido'],
                    'envio_actualizado',
                    'Código de rastreo actualizado 🔍',
                    "Nuevo código: {$codigo}",
                    BASE_URL . 'pedidos/ver?id=' . $envio['idPedido'],
                    'notif_estado_pedido'
                );
            }
        }

        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Rastreo actualizado.' : 'Error al actualizar rastreo.');

        $this->redirect('envios');
    }

    // ── Helper privado ────────────────────────────────────────────────────────

    /**
     * Notifica al cliente dueño de un pedido si tiene la preferencia activa.
     */
    private function notificarClientePedido(
        int    $idPedido,
        string $tipo,
        string $titulo,
        string $mensaje,
        string $url,
        string $pref
    ): void {
        $pedido = Pedido::obtenerPorId($idPedido);
        if (!$pedido) return;

        $cliente = Cliente::obtenerPorId((int) $pedido['idCliente']);
        if (!$cliente) return;

        $idUsuario = (int) $cliente['idUsuario'];
        $prefs     = Configuracion::obtenerPreferencias($idUsuario);
        if (empty($prefs[$pref])) return;

        Notificacion::crear([
            'idUsuario' => $idUsuario,
            'tipo'      => $tipo,
            'titulo'    => $titulo,
            'mensaje'   => $mensaje,
            'url'       => $url,
        ]);
    }
}

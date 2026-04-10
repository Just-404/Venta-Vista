<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Pago;
use app\models\Pedido;
use app\models\Cliente;
use app\models\Usuario;
use app\models\Notificacion;
use app\models\Configuracion;
use app\helpers\MailHelper;

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

        $idPedido = (int) $this->post('idPedido');

        $ok = Pago::crear([
            'monto'      => $this->post('monto'),
            'estado'     => 'Pendiente',
            'referencia' => $this->post('referencia'),
            'metodoPago' => $this->post('metodoPago'),
            'idPedido'   => $idPedido,
        ]);

        if ($ok) {
            Pedido::actualizarEstado($idPedido, 'Confirmado');

            // Notificar al cliente
            $monto   = number_format((float) $this->post('monto'), 2);
            $metodo  = $this->post('metodoPago') ?? '';
            $this->notificarClientePedido(
                $idPedido,
                'pago',
                'Pago registrado correctamente 💳',
                "Monto: RD\$ {$monto} — Método: {$metodo}. Tu pedido fue confirmado.",
                BASE_URL . 'pedidos/ver?id=' . $idPedido,
                'confirmar_pedido'
            );

            $this->setFlash('success', 'Pago registrado.');
        } else {
            $this->setFlash('error', 'Error al registrar el pago.');
        }

        $this->redirect('pedidos/ver?id=' . $idPedido);
    }

    // POST /pagos/estado
    public function estado(): void {
        $this->requireAuth();

        $id     = (int)    $this->post('id');
        $estado = (string) $this->post('estado');
        $ok     = Pago::actualizarEstado($id, $estado);

        if ($ok) {
            // Obtener el pago para saber su pedido
            $pago = Pago::obtenerPorId($id);
            if ($pago) {
                $titulo  = match ($estado) {
                    'Aprobado'    => 'Tu pago fue aprobado ✅',
                    'Rechazado'   => 'Tu pago fue rechazado ❌',
                    'Reembolsado' => 'Reembolso procesado 💰',
                    default       => "Estado de pago: $estado",
                };
                $this->notificarClientePedido(
                    (int) $pago['idPedido'],
                    'pago',
                    $titulo,
                    "El estado de tu pago cambió a: {$estado}",
                    BASE_URL . 'pedidos/ver?id=' . $pago['idPedido'],
                    'confirmar_pedido'
                );
            }
        }

        $this->setFlash($ok ? 'success' : 'error',
            $ok ? "Pago marcado como: $estado." : 'Error al actualizar pago.');

        $this->redirect('pagos');
    }

    // ── Helper privado ────────────────────────────────────────────────────────

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

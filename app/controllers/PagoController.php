<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Pago;
use app\models\Pedido;
use app\models\DetallePedido;
use app\models\Cliente;
use app\models\Usuario;
use app\models\Notificacion;
use app\models\Configuracion;
use app\helpers\MailHelper;
use app\models\Producto;
use app\models\Carrito;
use app\models\Cupon;

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

    public function procesar(): void
    {
        $this->requireAuth();

        $idUsuario = $this->usuarioActual()['id'];
        $idCliente = Cliente::obtenerPorUsuario($idUsuario)['idCliente'];
        
        $carrito   = Carrito::obtenerPorCliente($idCliente);

        $items = Carrito::obtenerItems($carrito['idCarrito']);

        if (!$items) {
            $this->setFlash('error', 'Carrito vacío.');
            $this->redirect('carrito');
        }

        // Simular pago
        $pagoExitoso = true; 

        if (!$pagoExitoso) {
            $this->setFlash('error', 'Pago rechazado.');
            $this->redirect('carrito/checkout');
        }

        // Calcular totales
        $subtotal = Carrito::calcularTotal($carrito['idCarrito']);

        $cupon = $_SESSION['cupon'] ?? null;
        $descuento = 0;
        $idCupon = null;

        if ($cupon) {
            $idCupon = $cupon['id'];

            if ($cupon['tipo'] === 'Porcentaje') {
                $descuento = $subtotal * ($cupon['descuento'] / 100);
            } else {
                $descuento = $cupon['descuento'];
            }
        }

        $total = max(0, $subtotal - $descuento);

        // Crear Pedido
        $numeroPedido = Pedido::generarNumeroPedido();

        $idPedido = Pedido::crear([
            'numeroPedido' => $numeroPedido,
            'subtotal'     => $subtotal,
            'descuento'    => $descuento,
            'total'        => $total,
            'notas'        => $notas ?? '',
            'idCliente'    => $idCliente,
            'idCupon'      => $idCupon,
        ]);

        Pago::crear([
            'monto'      => $total,
            'estado'     => 'Aprobado',
            'referencia'   => $this->post('referencia'),
            'metodoPago' => $this->post('metodoPago'),
            'idPedido'   => $idPedido,
        ]);

        // Detalle
        $detalle = [];

        foreach ($items as $item) {

            $detalle[] = [
                'cantidad'       => $item['cantidad'],
                'precioUnitario' => $item['precioUnitario'],
                'subtotal'       => $item['cantidad'] * $item['precioUnitario'],
                'idPedido'       => $idPedido,
                'idProducto'     => $item['idProducto'],
            ];

            Producto::actualizarStock(
                $item['idProducto'],
                -$item['cantidad']
            );
        }

        DetallePedido::crearLote($detalle);

        // Registrar uso cupón
        if ($idCupon) {
            Cupon::registrarUso($idCupon);
        }
        // Vaciar carrito
        Carrito::vaciar($carrito['idCarrito']);

        // Limpiar cupón
        unset($_SESSION['cupon']);
        $this->setFlash('success', 'Pedido creado correctamente');
        $this->redirect("pedidos/ver?id=$idPedido");
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
            unset($_SESSION['cupon']);
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

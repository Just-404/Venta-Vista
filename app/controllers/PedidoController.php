<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Pedido;
use app\models\DetallePedido;
use app\models\Cliente;
use app\models\Producto;
use app\models\Cupon;
use app\models\Pago;
use app\models\Envio;
use app\models\Direccion;

class PedidoController extends Controller {

    // GET /pedidos
    public function index(): void {
        $this->requireAuth();

        $this->render('pedidos/index', [
            'pedidos' => Pedido::obtenerTodos(),
            'flash'   => $this->getFlash(),
            'usuario' => $this->usuarioActual(),
        ]);
    }

    // GET  /pedidos/crear
    // POST /pedidos/crear
    public function crear(): void {
        $this->requireAuth();

        if ($this->isPost()) {
            // 1. Validar cupón si se envió
            $idCupon  = null;
            $descuento = 0;
            $codigoCupon = $this->post('cupon');

            if ($codigoCupon) {
                $cupon = Cupon::validar($codigoCupon);
                if ($cupon) {
                    $idCupon  = $cupon['idCupon'];
                    $descuento = $cupon['tipo'] === 'Monto_fijo'
                        ? $cupon['descuento']
                        : 0; // El porcentaje se calcula en el subtotal
                }
            }

            $subtotal = (float) $this->post('subtotal');
            $total    = $subtotal - $descuento;

            // 2. Crear cabecera del pedido
            $idPedido = Pedido::crear([
                'numeroPedido' => Pedido::generarNumeroPedido(),
                'subtotal'     => $subtotal,
                'descuento'    => $descuento,
                'total'        => max(0, $total),
                'notas'        => $this->post('notas'),
                'idCliente'    => $this->post('idCliente'),
                'idCupon'      => $idCupon,
            ]);

            // 3. Insertar detalle (items enviados como arrays)
            $items = [];
            $productos  = $_POST['idProducto']      ?? [];
            $cantidades = $_POST['cantidad']         ?? [];
            $precios    = $_POST['precioUnitario']   ?? [];

            foreach ($productos as $i => $idProducto) {
                $cantidad = (int)   $cantidades[$i];
                $precio   = (float) $precios[$i];
                $items[]  = [
                    'cantidad'       => $cantidad,
                    'precioUnitario' => $precio,
                    'subtotal'       => $cantidad * $precio,
                    'idPedido'       => $idPedido,
                    'idProducto'     => (int) $idProducto,
                ];
                // Descontar stock
                Producto::actualizarStock((int) $idProducto, -$cantidad);
            }

            DetallePedido::crearLote($items);

            // 4. Registrar uso del cupón si aplica
            if ($idCupon) Cupon::registrarUso($idCupon);

            $this->setFlash('success', 'Pedido creado correctamente.');
            $this->redirect('pedidos');
        }

        $this->render('pedidos/crear', [
            'clientes' => Cliente::obtenerTodos(),
            'productos' => Producto::obtenerActivos(),
            'usuario'  => $this->usuarioActual(),
        ]);
    }

    // GET /pedidos/ver?id=X
    public function ver(): void {
        $this->requireAuth();

        $id     = (int) $this->get('id');
        $pedido = Pedido::obtenerPorId($id);

        if (!$pedido) {
            $this->setFlash('error', 'Pedido no encontrado.');
            $this->redirect('pedidos');
        }

        $this->render('pedidos/ver', [
            'pedido'  => $pedido,
            'detalle' => DetallePedido::obtenerPorPedido($id),
            'pago'    => Pago::obtenerPorPedido($id),
            'envio'   => Envio::obtenerPorPedido($id),
            'flash'   => $this->getFlash(),
            'usuario' => $this->usuarioActual(),
        ]);
    }

    // POST /pedidos/estado para cambiar el estado del pedido
    public function estado(): void {
        $this->requireAuth();

        $id     = (int)    $this->post('id');
        $estado = (string) $this->post('estado');

        $ok = Pedido::actualizarEstado($id, $estado);

        $this->setFlash($ok ? 'success' : 'error',
            $ok ? "Estado actualizado a: $estado." : 'Error al cambiar estado.');

        $this->redirect('pedidos/ver?id=' . $id);
    }

    // POST /pedidos/eliminar
    public function eliminar(): void {
        $this->requireAuth();

        $id = (int) $this->post('id');

        if (Pedido::eliminar($id)) {
            $this->setFlash('success', 'Pedido eliminado.');
        } else {
            $this->setFlash('error', 'Error al eliminar el pedido.');
        }

        $this->redirect('pedidos');
    }
}
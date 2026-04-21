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
use app\models\Notificacion;
use app\models\Usuario;
use app\models\Configuracion;
use app\helpers\MailHelper;
use app\models\Vendedor;

class PedidoController extends Controller {

    // GET /pedidos
    public function index(): void {
        $this->requireAuth();

        $usuario = $this->usuarioActual();
        if ($usuario['rol'] == 2) {
            $idVendedor = Vendedor::obtenerPorUsuario($usuario['id'])['idVendedor'];
            $pedidos = Pedido::obtenerPorVendedor($idVendedor);
        } elseif ($usuario['rol'] == 1) {
            $pedidos = Pedido::obtenerTodos();
        } else {
            $idCliente = Cliente::obtenerPorUsuario($usuario['id'])['idCliente'];
            $pedidos = Pedido::obtenerPorCliente($idCliente);
        }

        $this->render('pedidos/index', [
            'pedidos' => $pedidos,
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
            $numeroPedido = Pedido::generarNumeroPedido();
            $idPedido = Pedido::crear([
                'numeroPedido' => $numeroPedido,
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
             
            // ── Notificaciones: nuevo pedido ────────────────────────────
            $this->notificarNuevoPedido($idPedido, $numeroPedido, max(0, $total));

            // ── Alertas: stock bajo tras descontar ──────────────────────
            foreach ($items as $item) {
                $prod = Producto::obtenerPorId($item['idProducto']);
                if ($prod && (int) $prod['stock'] <= 5 && (int) $prod['stock'] >= 0) {
                    $this->notificarStockBajo($prod);
                }
            }
            
            // 4. Registrar uso del cupón si aplica
            if ($idCupon) Cupon::registrarUso($idCupon);

            $this->setFlash('success', 'Pedido creado correctamente.');
            $this->redirect('pagos/checkout?id=' . $idPedido);
        }

        $carrito = $_SESSION['carrito'] ?? [];
        $this->render('pedidos/crear', [
            'clientes' => Cliente::obtenerTodos(),
            'productos' => Producto::obtenerActivos(),
            'carrito'   => $carrito,
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

        if ($ok) {
            $this->notificarCambioEstado($id, $estado);
        }

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

    // ── Helpers de notificación ──────────────────────────────────────────────

    private function notificarNuevoPedido(int $idPedido, string $numeroPedido, float $total): void {
        $data = [
            'tipo'    => 'pedido_nuevo',
            'titulo'  => "Nuevo pedido: $numeroPedido",
            'mensaje' => 'Total: RD$ ' . number_format($total, 2),
            'url'     => BASE_URL . 'pedidos/ver?id=' . $idPedido,
        ];

        // Notificación in-app a admins (rol 1) y vendedores (rol 2) con confirmar_pedido activo
        Notificacion::crearParaRol(1, $data, 'confirmar_pedido');
        Notificacion::crearParaRol(2, $data, 'confirmar_pedido');

        // Correo electrónico a los mismos destinatarios
        $destinatarios = array_merge(
            Notificacion::obtenerDestinatariosEmail(1, 'confirmar_pedido'),
            Notificacion::obtenerDestinatariosEmail(2, 'confirmar_pedido')
        );
        foreach ($destinatarios as $dest) {
            MailHelper::pedidoNuevo($dest['email'], $dest['nombreUsuario'], $numeroPedido, $total);
        }
    }

    private function notificarCambioEstado(int $idPedido, string $estado): void {
        $pedido  = Pedido::obtenerPorId($idPedido);
        if (!$pedido) return;

        $cliente = Cliente::obtenerPorId((int) $pedido['idCliente']);
        if (!$cliente) return;

        $idUsuario = (int) $cliente['idUsuario'];
        $prefs     = Configuracion::obtenerPreferencias($idUsuario);

        if (empty($prefs['notif_estado_pedido'])) return;

        // Notificación in-app al cliente
        Notificacion::crear([
            'idUsuario' => $idUsuario,
            'tipo'      => 'estado_pedido',
            'titulo'    => "Pedido {$pedido['numeroPedido']} — $estado",
            'mensaje'   => "El estado de tu pedido cambió a: $estado",
            'url'       => BASE_URL . 'pedidos/ver?id=' . $idPedido,
        ]);

        // Correo electrónico al cliente
        $usuario = Usuario::obtenerPorId($idUsuario);
        if ($usuario) {
            MailHelper::estadoPedido(
                $usuario['email'],
                $cliente['nombre'],
                $pedido['numeroPedido'],
                $estado
            );
        }
    }

    private function notificarStockBajo(array $producto): void {
        $data = [
            'tipo'    => 'stock_bajo',
            'titulo'  => "Stock bajo: {$producto['nombre']}",
            'mensaje' => "Quedan {$producto['stock']} unidades en inventario.",
            'url'     => BASE_URL . 'inventario',
        ];

        // Notificación in-app a admins y vendedores con alerta_stock activo
        Notificacion::crearParaRol(1, $data, 'alerta_stock');
        Notificacion::crearParaRol(2, $data, 'alerta_stock');

        // Correo electrónico
        $destinatarios = array_merge(
            Notificacion::obtenerDestinatariosEmail(1, 'alerta_stock'),
            Notificacion::obtenerDestinatariosEmail(2, 'alerta_stock')
        );
        foreach ($destinatarios as $dest) {
            MailHelper::alertaStock($dest['email'], $dest['nombreUsuario'], $producto['nombre'], (int) $producto['stock']);
        }
    }
}

<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Carrito;
use app\models\Producto;
use app\models\Cliente;
use app\models\Pedido;
use app\models\DetallePedido;

class CarritoController extends Controller
{

    // GET /carrito
    public function index(): void
    {
        $this->requireAuth();

        $idCliente = $this->getIdCliente();
        $carrito = $this->obtenerOCrearCarrito($idCliente);

        // Verificar estado de los productos en el carrito
        $estadoProductos = Carrito::obtenerEstadoProductos($carrito['idCarrito']);
        $inactivos = [];

        foreach ($estadoProductos as $estado) {
            if ((int) $estado['activo'] === 0) {
                Carrito::eliminarItem($carrito['idCarrito'], (int) $estado['idProducto']);
                $inactivos[] = $estado;
            }
        }

        $this->render('carrito/index', [
            'items' => Carrito::obtenerItems($carrito['idCarrito']),
            'total' => Carrito::calcularTotal($carrito['idCarrito']),
            'carrito' => $carrito,
            'usuario' => $this->usuarioActual(),
            'estadoProductos' => $estadoProductos,
            'inactivos' => $inactivos,
        ]);
    }
    
    public function checkout(): void
    {
        $this->requireAuth();

        $idCliente = $this->getIdCliente();
        $carrito   = $this->obtenerOCrearCarrito($idCliente);

        $items = Carrito::obtenerItems($carrito['idCarrito']);

        if (!$items) {
            $this->setFlash('error', 'El carrito está vacío.');
            $this->redirect('carrito');
        }

        $subtotal = Carrito::calcularTotal($carrito['idCarrito']);

        $cupon = $_SESSION['cupon'] ?? null;
        $descuento = 0;

        if ($cupon) {
            if ($cupon['tipo'] === 'Porcentaje') {
                $descuento = $subtotal * ($cupon['descuento'] / 100);
            } else {
                $descuento = $cupon['descuento'];
            }
        }
        $total = max(0, $subtotal - $descuento);

        // Ir a checkout
        $this->render('pagos/checkout', [
        'items'     => $items,
        'subtotal'  => $subtotal,
        'descuento' => $descuento,
        'total'     => $total,
        'cupon'     => $cupon,
        'usuario'   => $this->usuarioActual()
        ]);
    }

    // POST /carrito/agregar
    public function agregar(): void
    {
        $this->requireAuth();

        $idCliente = $this->getIdCliente();
        $carrito = $this->obtenerOCrearCarrito($idCliente);
        $idProducto = (int) $this->post('idProducto');
        $cantidad = max(1, (int) $this->post('cantidad', 1));
        $producto = Producto::obtenerPorId($idProducto);

        if (!$producto) {
            $this->setFlash('error', 'Producto no encontrado.');
            $this->redirect('carrito');
        }

        Carrito::agregarItem([
            'idCarrito' => $carrito['idCarrito'],
            'idProducto' => $idProducto,
            'cantidad' => $cantidad,
            'precioUnitario' => $producto['precio'],
        ]);

        $this->setFlash('success', 'Producto agregado al carrito.');
        $this->redirect('productos');
    }

    // POST /carrito/actualizar
    public function actualizar(): void
    {
        $this->requireAuth();

        $idCliente = $this->getIdCliente();
        $carrito = $this->obtenerOCrearCarrito($idCliente);

        Carrito::actualizarCantidad([
            'cantidad' => max(1, (int) $this->post('cantidad')),
            'idCarrito' => $carrito['idCarrito'],
            'idProducto' => (int) $this->post('idProducto'),
        ]);

        $this->redirect('carrito');
    }

    // POST /carrito/eliminar-item
    public function eliminarItem(): void
    {
        $this->requireAuth();

        $idCliente = $this->getIdCliente();
        $carrito = $this->obtenerOCrearCarrito($idCliente);

        Carrito::eliminarItem($carrito['idCarrito'], (int) $this->post('idProducto'));
        $this->redirect('carrito');
    }

    // POST /carrito/vaciar
    public function vaciar(): void
    {
        $this->requireAuth();

        $idCliente = $this->getIdCliente();
        $carrito = $this->obtenerOCrearCarrito($idCliente);

        Carrito::vaciar($carrito['idCarrito']);
        $this->redirect('carrito');
    }

    // Helpers privados

    private function getIdCliente(): int
    {
        $usuario = $this->usuarioActual();

        if ((int) $usuario['rol'] !== 3) {
            $this->setFlash('error', 'Solo los clientes pueden acceder al carrito.');
            $this->redirect('dashboard/index');
         }

        $cliente = Cliente::obtenerPorUsuario((int) $usuario['id']);

        if (!$cliente) {
            $this->setFlash('error', 'Perfil de cliente no encontrado.');
            $this->redirect('dashboard/index');
        }
        return (int) $cliente['idCliente'];
    }

    private function obtenerOCrearCarrito(int $idCliente): array
    {
        $carrito = Carrito::obtenerPorCliente($idCliente);
        if (!$carrito) {
            $id = Carrito::crear($idCliente);
            $carrito = ['idCarrito' => $id, 'idCliente' => $idCliente];
        }
        return $carrito;
    }

}
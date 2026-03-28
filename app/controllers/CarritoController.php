<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Carrito;
use app\models\Producto;
use app\models\Cliente;

class CarritoController extends Controller {
 
    // GET /carrito
    public function index(): void {
        $this->requireAuth();
 
        $idCliente = $this->getIdCliente();
        $carrito   = $this->obtenerOCrearCarrito($idCliente);
 
        $this->render('carrito/index', [
            'items'   => Carrito::obtenerItems($carrito['idCarrito']),
            'total'   => Carrito::calcularTotal($carrito['idCarrito']),
            'carrito' => $carrito,
            'usuario' => $this->usuarioActual(),
        ]);
    }
 
    // POST /carrito/agregar
    public function agregar(): void {
        $this->requireAuth();
 
        $idCliente  = $this->getIdCliente();
        $carrito    = $this->obtenerOCrearCarrito($idCliente);
        $idProducto = (int) $this->post('idProducto');
        $cantidad   = max(1, (int) $this->post('cantidad', 1));
        $producto   = Producto::obtenerPorId($idProducto);
 
        if (!$producto) {
            $this->setFlash('error', 'Producto no encontrado.');
            $this->redirect('carrito');
        }
 
        Carrito::agregarItem([
            'idCarrito'      => $carrito['idCarrito'],
            'idProducto'     => $idProducto,
            'cantidad'       => $cantidad,
            'precioUnitario' => $producto['precio'],
        ]);
 
        $this->setFlash('success', 'Producto agregado al carrito.');
        $this->redirect('carrito');
    }
 
    // POST /carrito/actualizar
    public function actualizar(): void {
        $this->requireAuth();
 
        $idCliente  = $this->getIdCliente();
        $carrito    = $this->obtenerOCrearCarrito($idCliente);
 
        Carrito::actualizarCantidad([
            'cantidad'   => max(1, (int) $this->post('cantidad')),
            'idCarrito'  => $carrito['idCarrito'],
            'idProducto' => (int) $this->post('idProducto'),
        ]);
 
        $this->redirect('carrito');
    }
 
    // POST /carrito/eliminar-item
    public function eliminarItem(): void {
        $this->requireAuth();
 
        $idCliente  = $this->getIdCliente();
        $carrito    = $this->obtenerOCrearCarrito($idCliente);
 
        Carrito::eliminarItem($carrito['idCarrito'], (int) $this->post('idProducto'));
        $this->redirect('carrito');
    }
 
    // POST /carrito/vaciar
    public function vaciar(): void {
        $this->requireAuth();
 
        $idCliente = $this->getIdCliente();
        $carrito   = $this->obtenerOCrearCarrito($idCliente);
 
        Carrito::vaciar($carrito['idCarrito']);
        $this->redirect('carrito');
    }
 
    // Helpers privados
 
    private function getIdCliente(): int {
        $usuario = $this->usuarioActual();
        $cliente = Cliente::obtenerPorUsuario((int) $usuario['id']);
        return (int) $cliente['idCliente'];
    }
 
    private function obtenerOCrearCarrito(int $idCliente): array {
        $carrito = Carrito::obtenerPorCliente($idCliente);
        if (!$carrito) {
            $id      = Carrito::crear($idCliente);
            $carrito = ['idCarrito' => $id, 'idCliente' => $idCliente];
        }
        return $carrito;
    }
}
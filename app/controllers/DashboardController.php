<?php

namespace app\controllers;
use app\core\Controller;
use app\models\Pedido;
use app\models\Producto;
use app\models\Cliente;
use app\models\Usuario;

class DashboardController extends Controller{

    public function index(): void{
        $this->requireAuth();

        $datos = [
            'totalPedidos'   => count(Pedido::obtenerTodos()),
            'totalProductos' => count(Producto::obtenerActivos()),
            'totalClientes'  => count(Cliente::obtenerTodos()),
            'totalUsuarios'  => count(Usuario::obtenerTodos()),
            'pedidosRecientes' => array_slice(Pedido::obtenerTodos(), 0, 5),
            'productosDestacados' => Producto::obtenerDestacados(5),
            'flash'          => $this->getFlash(),
            'usuario'        => $this->usuarioActual(),
        ];
 
        $this->render('dashboard/index', $datos);
    }
}
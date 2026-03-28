<?php

namespace app\controllers;
 
use app\core\Controller;
use app\models\Pedido;
use app\models\Producto;
use app\models\Vendedor;
use app\models\Cliente;
 
class ReporteController extends Controller {
 
    // GET /reportes
    public function index(): void {
        $this->requireAuth();
 
        $this->render('reportes/index', [
            'usuario' => $this->usuarioActual(),
        ]);
    }
 
    // GET /reportes/ventas
    public function ventas(): void {
        $this->requireAuth();
 
        $this->render('reportes/ventas', [
            'pedidos'        => Pedido::obtenerTodos(),
            'porVendedor'    => Vendedor::obtenerResumenVentas(),
            'usuario'        => $this->usuarioActual(),
        ]);
    }
 
    // GET /reportes/productos
    public function productos(): void {
        $this->requireAuth();
 
        $this->render('reportes/productos', [
            'destacados' => Producto::obtenerDestacados(10),
            'todos'      => Producto::obtenerConRating(),
            'usuario'    => $this->usuarioActual(),
        ]);
    }
 
    // GET /reportes/clientes
    public function clientes(): void {
        $this->requireAuth();
 
        $this->render('reportes/clientes', [
            'clientes' => Cliente::obtenerTodos(),
            'usuario'  => $this->usuarioActual(),
        ]);
    }
}
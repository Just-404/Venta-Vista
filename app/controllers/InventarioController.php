<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Producto;
use app\models\Vendedor;

class InventarioController extends Controller {

    public function index(): void {
        $this->requireAuth();

        $usuario = $this->usuarioActual();

        if ($usuario['rol'] == 2) {
            $idVendedor = Vendedor::obtenerPorUsuario($usuario['id'])['idVendedor'];
            $productos = Producto::obtenerPorVendedor($idVendedor);
        } elseif ($usuario['rol'] == 3) {
            $productos = Producto::obtenerActivos();
        } else {
            $productos = Producto::obtenerTodos();
        }

        $this->render('inventario/index', [
            'productos' => $productos,
            'flash'     => $this->getFlash(),
            'usuario'   => $this->usuarioActual(),
        ]);
    }
}
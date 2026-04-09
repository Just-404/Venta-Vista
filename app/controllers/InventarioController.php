<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Producto;

class InventarioController extends Controller {

    public function index(): void {
        $this->requireAuth();

        $this->render('inventario/index', [
            'productos' => Producto::obtenerTodos(),
            'flash'     => $this->getFlash(),
            'usuario'   => $this->usuarioActual(),
        ]);
    }
}
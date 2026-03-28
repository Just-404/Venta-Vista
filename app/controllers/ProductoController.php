<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Producto;
use app\models\Categoria;

class ProductoController extends Controller {

    // GET /productos
    public function index(): void {
        $this->requireAuth();

        $this->render('productos/index', [
            'productos' => Producto::obtenerTodos(),
            'flash'     => $this->getFlash(),
            'usuario'   => $this->usuarioActual(),
        ]);
    }

    // GET  /productos/crear para mostrar formulario
    // POST /productos/crear para procesar creación

    public function crear(): void {
        $this->requireAuth();

        if ($this->isPost()) {
            $data = [
                'nombre'      => $this->post('nombre'),
                'descripcion' => $this->post('descripcion'),
                'precio'      => $this->post('precio'),
                'descuento'   => $this->post('descuento', 0),
                'stock'       => $this->post('stock', 0),
                'imagenes'    => $this->post('imagenes'),
                'idCategoria' => $this->post('idCategoria'),
            ];

            if (Producto::crear($data)) {
                $this->setFlash('success', 'Producto creado correctamente.');
            } else {
                $this->setFlash('error', 'Error al crear el producto.');
            }

            $this->redirect('productos');
        }

        $this->render('productos/crear', [
            'categorias' => Categoria::obtenerTodas(),
            'usuario'    => $this->usuarioActual(),
        ]);
    }

    // GET  /productos/editar?id=X para mostrar formulario
    // POST /productos/editar para procesar actualización
    public function editar(): void {
        $this->requireAuth();

        $id = (int) $this->get('id');
        $producto = Producto::obtenerPorId($id);

        if (!$producto) {
            $this->setFlash('error', 'Producto no encontrado.');
            $this->redirect('productos');
        }

        if ($this->isPost()) {
            $data = [
                'nombre'      => $this->post('nombre'),
                'descripcion' => $this->post('descripcion'),
                'precio'      => $this->post('precio'),
                'descuento'   => $this->post('descuento', 0),
                'stock'       => $this->post('stock'),
                'imagenes'    => $this->post('imagenes'),
                'idCategoria' => $this->post('idCategoria'),
                'idProducto'  => $id,
            ];

            if (Producto::actualizar($data)) {
                $this->setFlash('success', 'Producto actualizado.');
            } else {
                $this->setFlash('error', 'Error al actualizar el producto.');
            }

            $this->redirect('productos');
        }

        $this->render('productos/editar', [
            'producto'   => $producto,
            'categorias' => Categoria::obtenerTodas(),
            'usuario'    => $this->usuarioActual(),
        ]);
    }

    // POST /productos/eliminar  (llamado por form con id oculto)
    public function eliminar(): void {
        $this->requireAuth();

        $id = (int) $this->post('id');

        if (Producto::eliminar($id)) {
            $this->setFlash('success', 'Producto desactivado.');
        } else {
            $this->setFlash('error', 'Error al eliminar el producto.');
        }

        $this->redirect('productos');
    }

    // GET /productos/ver?id=X
    public function ver(): void {
        $this->requireAuth();

        $id      = (int) $this->get('id');
        $producto = Producto::obtenerPorId($id);

        if (!$producto) {
            $this->setFlash('error', 'Producto no encontrado.');
            $this->redirect('productos');
        }

        $this->render('productos/ver', [
            'producto' => $producto,
            'usuario'  => $this->usuarioActual(),
        ]);
    }
}
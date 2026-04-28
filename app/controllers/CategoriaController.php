<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Categoria;

class CategoriaController extends Controller {

    // GET /categorias
    public function index(): void {
        $this->requireAuth();

        $this->render('categorias/index', [
            'categorias' => Categoria::obtenerTodosConConteo(),
            'flash'      => $this->getFlash(),
            'usuario'    => $this->usuarioActual(),
        ]);
    }

    // GET /categorias/crear  →  muestra formulario
    // POST /categorias/crear →  procesa
    public function crear(): void {
        $this->requireAuth();

        if ($this->isPost()) {
            $nombre      = $this->post('nombre');
            $descripcion = $this->post('descripcion');

            if (empty($nombre)) {
                $this->setFlash('error', 'El nombre de la categoría es obligatorio.');
                $this->redirect('categorias/crear');
            }

            if (Categoria::existeNombre($nombre)) {
                $this->setFlash('error', 'Ya existe una categoría con ese nombre.');
                $this->redirect('categorias/crear');
            }

            $ok = Categoria::crear([
                'nombre'      => $nombre,
                'descripcion' => $descripcion ?: null,
            ]);

            $ok
                ? $this->setFlash('exito', "Categoría «{$nombre}» creada correctamente.")
                : $this->setFlash('error', 'No se pudo crear la categoría. Inténtalo de nuevo.');

            $this->redirect('categorias');
        }

        $this->render('categorias/form', [
            'categoria' => null,
            'flash'     => $this->getFlash(),
            'usuario'   => $this->usuarioActual(),
        ]);
    }

    // GET /categorias/editar?id=N  →  muestra formulario con datos
    // POST /categorias/editar      →  procesa
    public function editar(): void {
        $this->requireAuth();

        $id        = (int) $this->get('id');
        $categoria = Categoria::obtenerPorId($id);

        if (!$categoria) {
            $this->setFlash('error', 'Categoría no encontrada.');
            $this->redirect('categorias');
        }

        if ($this->isPost()) {
            $nombre      = $this->post('nombre');
            $descripcion = $this->post('descripcion');

            if (empty($nombre)) {
                $this->setFlash('error', 'El nombre de la categoría es obligatorio.');
                $this->redirect("categorias/editar?id={$id}");
            }

            if (Categoria::existeNombre($nombre, $id)) {
                $this->setFlash('error', 'Ya existe otra categoría con ese nombre.');
                $this->redirect("categorias/editar?id={$id}");
            }

            $ok = Categoria::actualizar([
                'idCategoria' => $id,
                'nombre'      => $nombre,
                'descripcion' => $descripcion ?: null,
            ]);

            $ok
                ? $this->setFlash('exito', "Categoría «{$nombre}» actualizada correctamente.")
                : $this->setFlash('error', 'No se pudo actualizar la categoría.');

            $this->redirect('categorias');
        }

        $this->render('categorias/form', [
            'categoria' => $categoria,
            'flash'     => $this->getFlash(),
            'usuario'   => $this->usuarioActual(),
        ]);
    }

    // POST /categorias/eliminar
    public function eliminar(): void {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('categorias');
        }

        $id        = (int) $this->post('id');
        $categoria = Categoria::obtenerPorId($id);

        if (!$categoria) {
            $this->setFlash('error', 'Categoría no encontrada.');
            $this->redirect('categorias');
        }

        try {
            $ok = Categoria::eliminar($id);

            $ok
                ? $this->setFlash('exito', "Categoría «{$categoria['nombre']}» eliminada.")
                : $this->setFlash('error', 'No se pudo eliminar la categoría.');

        } catch (\Exception $e) {
            $this->setFlash('error',
                "No se puede eliminar «{$categoria['nombre']}» porque tiene productos asociados."
            );
        }

        $this->redirect('categorias');
    }
}
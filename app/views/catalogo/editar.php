<?php
$producto   = $producto   ?? [];
$categorias = $categorias ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Editar Producto</h1>
        <p class="page-sub"><?= htmlspecialchars($producto['nombre'] ?? '') ?></p>
    </div>
    <?php $origen = $_GET['origen'] ?? 'catalogo'; ?>
    <a href="<?= BASE_URL ?><?= $origen === 'inventario' ? 'inventario' : 'productos/ver?id='.$producto['idProducto'] ?>" class="btn btn-contorno">← Volver</a>
</div>

<div class="panel" style="max-width: 720px; padding: 20px;">
    <form method="POST" action="<?= BASE_URL ?>productos/editar?id=<?= $producto['idProducto'] ?>&origen=<?= $origen ?>">

        <div class="grid-form">
            <div class="grupo-form completo">
                <label class="etiqueta-form">Nombre del producto</label>
                <input class="input-form" type="text" name="nombre"
                       value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>" required>
            </div>

            <div class="grupo-form completo">
                <label class="etiqueta-form">Descripción</label>
                <textarea class="input-form" name="descripcion" rows="3"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="grupo-form">
                <label class="etiqueta-form">Precio (RD$)</label>
                <input class="input-form" type="number" name="precio" step="0.01" min="0"
                       value="<?= $producto['precio'] ?? 0 ?>" required>
            </div>

            <div class="grupo-form">
                <label class="etiqueta-form">Descuento (%)</label>
                <input class="input-form" type="number" name="descuento" step="0.01" min="0" max="100"
                       value="<?= $producto['descuento'] ?? 0 ?>">
            </div>

            <div class="grupo-form">
                <label class="etiqueta-form">Stock</label>
                <input class="input-form" type="number" name="stock" min="0"
                       value="<?= $producto['stock'] ?? 0 ?>" required>
            </div>

            <div class="grupo-form">
                <label class="etiqueta-form">Categoría</label>
                <select class="select-form" name="idCategoria" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['idCategoria'] ?>"
                            <?= $cat['idCategoria'] == ($producto['idCategoria'] ?? '') ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo-form completo">
                <label class="etiqueta-form">Imágenes (rutas separadas por coma)</label>
                <input class="input-form" type="text" name="imagenes"
                       value="<?= htmlspecialchars($producto['imagenes'] ?? '') ?>">
            </div>
            <div class="grupo-form completo">
                <label class="etiqueta-form">Activo</label>
                <select class="select-form" name="activo" required>
                    <option value="1" selected>Sí</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>


        <div class="form-acciones">
            <a href="<?= BASE_URL ?><?= $origen === 'inventario' ? 'inventario' : 'productos' ?>" class="btn btn-secundario">Cancelar</a>
            <button class="btn btn-primario" type="submit">Actualizar producto</button>
        </div>

    </form>
</div>
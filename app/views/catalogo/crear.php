<?php $categorias = $categorias ?? []; ?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Nuevo Producto</h1>
        <p class="page-sub">Completa los datos para agregar al catálogo</p>
    </div>
    <a href="<?= BASE_URL ?><?= $origen === 'inventario' ? 'inventario' : 'productos'?>" class="btn btn-contorno">←
        Volver</a>
</div>

<div class="panel" style="max-width: 720px; padding: 20px;">
    <form method="POST" action="<?= BASE_URL ?>productos/crear?origen=<?= $origen ?>">

        <div class="grid-form">
            <div class="grupo-form completo">
                <label class="etiqueta-form">Nombre del producto</label>
                <input class="input-form" type="text" name="nombre" placeholder="Ej: Camiseta Polo Classic" required>
            </div>

            <div class="grupo-form completo">
                <label class="etiqueta-form">Descripción</label>
                <textarea class="input-form" name="descripcion" rows="3"
                    placeholder="Descripción del producto..."></textarea>
            </div>

            <div class="grupo-form">
                <label class="etiqueta-form">Precio (RD$)</label>
                <input class="input-form" type="number" name="precio" step="0.01" min="0" placeholder="0.00" required>
            </div>

            <div class="grupo-form">
                <label class="etiqueta-form">Descuento (%)</label>
                <input class="input-form" type="number" name="descuento" step="0.01" min="0" max="100" value="0"
                    placeholder="0">
            </div>

            <div class="grupo-form">
                <label class="etiqueta-form">Stock</label>
                <input class="input-form" type="number" name="stock" min="0" value="0" required>
            </div>

            <div class="grupo-form">
                <label class="etiqueta-form">Categoría</label>
                <select class="select-form" name="idCategoria" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['idCategoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo-form completo">
                <label class="etiqueta-form">Imágenes (rutas separadas por coma)</label>
                <input class="input-form" type="text" name="imagenes" placeholder="img1.jpg, img2.jpg">
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
            <a href="<?= BASE_URL ?><?= $origen === 'inventario' ? 'inventario' : 'productos' ?>"
                class="btn btn-secundario">Cancelar</a>
            <button class="btn btn-primario" type="submit">Guardar producto</button>
        </div>

    </form>
</div>
<?php
// $categoria = null  →  modo crear
// $categoria = [...]  →  modo editar
$editando = !empty($categoria);
$titulo   = $editando ? 'Editar categoría' : 'Nueva categoría';
$accion   = $editando
    ? BASE_URL . 'categorias/editar?id=' . $categoria['idCategoria']
    : BASE_URL . 'categorias/crear';
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo"><?= $titulo ?></h1>
        <p class="page-sub">
            <?= $editando
                ? 'Modifica los datos de la categoría'
                : 'Completa los campos para registrar una nueva categoría' ?>
        </p>
    </div>
    <a href="<?= BASE_URL ?>categorias" class="btn btn-contorno">← Volver</a>
</div>

<div style="max-width:600px">
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-titulo"><?= $titulo ?></h2>
        </div>
        <div style="padding:24px">
            <form method="POST" action="<?= $accion ?>">

                <div class="grupo-form">
                    <label class="etiqueta-form">
                        Nombre <span style="color:var(--peligro)">*</span>
                    </label>
                    <input class="input-form"
                           type="text"
                           name="nombre"
                           maxlength="60"
                           placeholder="Ej: Ropa, Tecnología, Calzado..."
                           value="<?= htmlspecialchars($categoria['nombre'] ?? '') ?>"
                           required>
                </div>

                <div class="grupo-form">
                    <label class="etiqueta-form">Descripción <span class="texto-muted">(opcional)</span></label>
                    <textarea class="input-form"
                              name="descripcion"
                              rows="3"
                              maxlength="200"
                              placeholder="Breve descripción de la categoría..."><?= htmlspecialchars($categoria['descripcion'] ?? '') ?></textarea>
                    <small class="texto-muted">Máximo 200 caracteres</small>
                </div>

                <div style="display:flex;gap:12px;margin-top:8px">
                    <button class="btn btn-primario" type="submit">
                        <?= $editando ? '💾 Guardar cambios' : '✚ Crear categoría' ?>
                    </button>
                    <a href="<?= BASE_URL ?>categorias" class="btn btn-contorno">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>
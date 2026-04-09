<?php $cupon = $cupon ?? []; ?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Editar Cupón</h1>
        <p class="page-sub"><span class="codigo"><?= htmlspecialchars($cupon['codigo'] ?? '') ?></span></p>
    </div>
    <a href="<?= BASE_URL ?>cupones" class="btn btn-contorno">← Volver</a>
</div>

<div class="panel" style="max-width:680px">
    <form method="POST" action="<?= BASE_URL ?>cupones/editar?id=<?= $cupon['idCupon'] ?>">
        <div style="padding:20px">
            <div class="grid-form">
                <div class="grupo-form">
                    <label class="etiqueta-form">Código</label>
                    <input class="input-form" type="text" name="codigo"
                           value="<?= htmlspecialchars($cupon['codigo'] ?? '') ?>" required style="text-transform:uppercase">
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Tipo</label>
                    <select class="select-form" name="tipo">
                        <?php foreach (['Porcentaje','Monto_fijo','envio_gratis'] as $t): ?>
                            <option value="<?= $t ?>" <?= ($cupon['tipo'] ?? '') === $t ? 'selected' : '' ?>>
                                <?= str_replace('_', ' ', $t) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Descuento</label>
                    <input class="input-form" type="number" name="descuento" step="0.01" min="0"
                           value="<?= $cupon['descuento'] ?? 0 ?>">
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Uso máximo</label>
                    <input class="input-form" type="number" name="usoMaximo" min="1"
                           value="<?= $cupon['usoMaximo'] ?? 1 ?>" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Fecha inicio</label>
                    <input class="input-form" type="date" name="fechaInicio"
                           value="<?= $cupon['fechaInicio'] ?? '' ?>" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Fecha vencimiento</label>
                    <input class="input-form" type="date" name="fechaVencimiento"
                           value="<?= $cupon['fechaVencimiento'] ?? '' ?>" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Estado</label>
                    <select class="select-form" name="activo">
                        <option value="1" <?= ($cupon['activo'] ?? 1) ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= !($cupon['activo'] ?? 1) ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-acciones" style="padding:0 20px 20px">
            <a href="<?= BASE_URL ?>cupones" class="btn btn-secundario">Cancelar</a>
            <button class="btn btn-primario" type="submit">Guardar cambios</button>
        </div>
    </form>
</div>
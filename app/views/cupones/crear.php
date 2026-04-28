<div class="page-header">
    <div>
        <h1 class="page-titulo">Nuevo Cupón</h1>
        <p class="page-sub">Crea un cupón de descuento para tus clientes</p>
    </div>
    <a href="<?= BASE_URL ?>cupones" class="btn btn-contorno">← Volver</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alerta alerta--error">
        ❌ <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="panel" style="max-width:680px">
    <form method="POST" action="<?= BASE_URL ?>cupones/crear">
        <div style="padding:20px">
            <div class="grid-form">
                <div class="grupo-form">
                    <label class="etiqueta-form">Código</label>
                    <input class="input-form" type="text" name="codigo" placeholder="VERANO20" required
                           style="text-transform:uppercase">
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Tipo</label>
                    <select class="select-form" name="tipo" id="tipo-cupon" onchange="actualizarLabel()">
                        <option value="Porcentaje">Porcentaje</option>
                        <option value="Monto_fijo">Monto fijo</option>
                        <option value="envio_gratis">Envío gratis</option>
                    </select>
                </div>
                <div class="grupo-form" id="grupo-descuento">
                    <label class="etiqueta-form" id="label-descuento">Descuento (%)</label>
                    <input class="input-form" type="number" name="descuento" step="0.01" min="0" value="0">
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Uso máximo</label>
                    <input class="input-form" type="number" name="usoMaximo" min="1" value="1" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Fecha inicio</label>
                    <input class="input-form" type="date" name="fechaInicio" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Fecha vencimiento</label>
                    <input class="input-form" type="date" name="fechaVencimiento" required>
                </div>
            </div>
        </div>
        <div class="form-acciones" style="padding:0 20px 20px">
            <a href="<?= BASE_URL ?>cupones" class="btn btn-secundario">Cancelar</a>
            <button class="btn btn-primario" type="submit">Crear cupón</button>
        </div>
    </form>
</div>

<script>
function actualizarLabel() {
    const tipo = document.getElementById('tipo-cupon').value;
    const grupo = document.getElementById('grupo-descuento');
    const label = document.getElementById('label-descuento');
    if (tipo === 'envio_gratis') {
        grupo.style.display = 'none';
    } else {
        grupo.style.display = '';
        label.textContent = tipo === 'Monto_fijo' ? 'Descuento (RD$)' : 'Descuento (%)';
    }
}
</script>
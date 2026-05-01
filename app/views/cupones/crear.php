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
    <form method="POST" action="<?= BASE_URL ?>cupones/crear" id="form-cupon">
        <div style="padding:20px">
            <div class="grid-form">
                <div class="grupo-form">
                    <label class="etiqueta-form">Código</label>
                    <input class="input-form" type="text" name="codigo" placeholder="VERANO20" required
                           style="text-transform:uppercase" maxlength="20"
                           pattern="^[A-Za-z0-9_\-]{3,20}$"
                           title="Solo letras, números, guiones. Entre 3 y 20 caracteres.">
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
                    <input class="input-form" type="number" name="descuento" step="0.01" min="0" max="100" value="0">
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Uso máximo</label>
                    <input class="input-form" type="number" name="usoMaximo" min="1" value="1" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Fecha inicio</label>
                    <input class="input-form" type="date" name="fechaInicio" id="fechaInicio" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Fecha vencimiento</label>
                    <input class="input-form" type="date" name="fechaVencimiento" id="fechaVencimiento" required>
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
    const tipo      = document.getElementById('tipo-cupon').value;
    const grupo     = document.getElementById('grupo-descuento');
    const label     = document.getElementById('label-descuento');
    const inputDesc = document.querySelector('input[name="descuento"]');
    if (tipo === 'envio_gratis') {
        grupo.style.display = 'none';
    } else {
        grupo.style.display = '';
        if (tipo === 'Monto_fijo') {
            label.textContent = 'Descuento (RD$)';
            inputDesc.removeAttribute('max');
        } else {
            label.textContent = 'Descuento (%)';
            inputDesc.setAttribute('max', '100');
        }
    }
}

// Mínimo = hoy en ambas fechas
const hoy = new Date().toISOString().split('T')[0];
document.getElementById('fechaInicio').setAttribute('min', hoy);
document.getElementById('fechaVencimiento').setAttribute('min', hoy);

// Validar que vencimiento > inicio
document.getElementById('form-cupon').addEventListener('submit', function(e) {
    const ini = document.getElementById('fechaInicio').value;
    const fin = document.getElementById('fechaVencimiento').value;
    if (ini && fin && fin <= ini) {
        e.preventDefault();
        alert('La fecha de vencimiento debe ser posterior a la fecha de inicio.');
        document.getElementById('fechaVencimiento').focus();
    }
});

actualizarLabel();
</script>

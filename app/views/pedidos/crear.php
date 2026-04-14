<?php
$clientes  = $clientes  ?? [];
$productos = $productos ?? [];
$carrito = $carrito ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Nuevo Pedido</h1>
        <p class="page-sub">Completa los datos para registrar un pedido</p>
    </div>
    <a href="<?= BASE_URL ?>pedidos" class="btn btn-contorno">← Volver</a>
</div>

<form method="POST" action="<?= BASE_URL ?>pedidos/crear" id="form-pedido">
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

    <!-- Columna izquierda -->
    <div>

        <!-- Cliente -->
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Cliente</h2></div>
            <div style="padding:20px">
                <div class="grupo-form">
                    <label class="etiqueta-form">Seleccionar cliente</label>
                    <select class="select-form" name="idCliente" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= $c['idCliente'] ?>">
                                <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']) ?> — <?= htmlspecialchars($c['cedula']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Notas (opcional)</label>
                    <textarea class="input-form" name="notas" rows="2"
                              placeholder="Instrucciones especiales, horario de entrega..."></textarea>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-titulo">Productos</h2>
                <button type="button" class="btn btn-contorno btn-sm" onclick="agregarFila()">+ Agregar producto</button>
            </div>
            <div class="tabla-wrapper">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio unit.</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
              <tbody id="items-body">

<?php if(!empty($carrito)): ?>
<?php foreach($carrito as $item): ?>
<tr class="item-fila">
    <td>
        <select class="select-form" name="idProducto[]" 
                onchange="actualizarPrecio(this)" required>

            <?php foreach ($productos as $p): ?>
            <option value="<?= $p['idProducto'] ?>"
                    data-precio="<?= $p['precio'] ?>"
                    <?= $p['idProducto'] == $item['idProducto'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['nombre']) ?>
            </option>
            <?php endforeach; ?>

        </select>
    </td>

    <td>
        <input class="input-form precio-unit" type="number"
               name="precioUnitario[]"
               value="<?= $item['precio'] ?>"
               readonly>
    </td>

    <td>
        <input class="input-form cantidad"
               type="number"
               name="cantidad[]"
               value="<?= $item['cantidad'] ?>"
               onchange="recalcular()">
    </td>

    <td class="subtotal-celda">
        RD$ <?= number_format($item['precio'] * $item['cantidad'],2) ?>
    </td>

    <td>
        <button type="button"
                class="btn-tabla btn-tabla--eliminar"
                onclick="eliminarFila(this)">✕</button>
    </td>

</tr>
<?php endforeach; ?>

<?php else: ?>

<!-- Fila vacía original -->
<tr class="item-fila">
...
</tr>

<?php endif; ?>

</tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Columna derecha -->
    <div>
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Resumen</h2></div>
            <div style="padding:20px">

                <!-- Cupón -->
                <div class="grupo-form">
                    <label class="etiqueta-form">Cupón de descuento</label>
                    <div style="display:flex;gap:8px">
                        <input class="input-form" type="text" id="input-cupon"
                               placeholder="CODIGO" style="text-transform:uppercase">
                        <button type="button" class="btn btn-contorno btn-sm"
                                onclick="validarCupon()">Aplicar</button>
                    </div>
                    <div id="cupon-resultado" style="margin-top:6px;font-size:.82rem"></div>
                    <input type="hidden" name="cupon" id="cupon-aplicado">
                </div>

                <div class="separador-seccion"></div>

                <div class="detalle-fila">
                    <span class="detalle-label">Subtotal</span>
                    <span id="resumen-subtotal">RD$ 0.00</span>
                </div>
                <div class="detalle-fila">
                    <span class="detalle-label">Descuento</span>
                    <span id="resumen-descuento" style="color:var(--exito)">RD$ 0.00</span>
                </div>
                <div class="detalle-fila" style="font-size:1.1rem;font-weight:700;margin-top:8px">
                    <span>Total</span>
                    <span id="resumen-total" style="color:var(--acento)">RD$ 0.00</span>
                </div>

                <!-- Campos ocultos que el controller necesita -->
                <input type="hidden" name="subtotal" id="campo-subtotal" value="0">
                <input type="hidden" name="descuento" id="campo-descuento" value="0">
                <input type="hidden" name="total" id="campo-total" value="0">

                <button class="btn btn-primario btn-completo" type="submit" style="margin-top:20px">
                    Crear pedido
                </button>

            </div>
        </div>
    </div>

</div>
</form>

<script>
// Template de fila para clonar
const templateFila = `
<tr class="item-fila">
    <td>
        <select class="select-form" name="idProducto[]" onchange="actualizarPrecio(this)" required>
            <option value="">Seleccionar...</option>
            <?php foreach ($productos as $p): ?>
            <option value="<?= $p['idProducto'] ?>" data-precio="<?= $p['precio'] ?>">
                <?= htmlspecialchars($p['nombre']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </td>
    <td><input class="input-form precio-unit" type="number" name="precioUnitario[]"
               step="0.01" min="0" placeholder="0.00" readonly style="width:100px"></td>
    <td><input class="input-form cantidad" type="number" name="cantidad[]"
               min="1" value="1" onchange="recalcular()" style="width:70px"></td>
    <td class="subtotal-celda">RD$ 0.00</td>
    <td><button type="button" class="btn-tabla btn-tabla--eliminar"
                onclick="eliminarFila(this)">✕</button></td>
</tr>`;
window.addEventListener('load', () => {
    recalcular();
});
function agregarFila() {
    document.getElementById('items-body').insertAdjacentHTML('beforeend', templateFila);
}

function eliminarFila(btn) {
    const filas = document.querySelectorAll('.item-fila');
    if (filas.length === 1) return; // mínimo 1 fila
    btn.closest('tr').remove();
    recalcular();
}

function actualizarPrecio(select) {
    const opt    = select.options[select.selectedIndex];
    const precio = parseFloat(opt.dataset.precio || 0);
    const fila   = select.closest('tr');
    fila.querySelector('.precio-unit').value = precio.toFixed(2);
    recalcular();
}

let descuentoAplicado = 0;
let tipoCupon = '';

function recalcular() {
    let subtotal = 0;
    document.querySelectorAll('.item-fila').forEach(fila => {
        const precio   = parseFloat(fila.querySelector('.precio-unit').value || 0);
        const cantidad = parseInt(fila.querySelector('.cantidad').value || 1);
        const sub      = precio * cantidad;
        fila.querySelector('.subtotal-celda').textContent = 'RD$ ' + sub.toFixed(2);
        subtotal += sub;
    });

    let descuento = 0;
    if (tipoCupon === 'Porcentaje') {
        descuento = subtotal * (descuentoAplicado / 100);
    } else if (tipoCupon === 'Monto_fijo') {
        descuento = descuentoAplicado;
    }

    const total = Math.max(0, subtotal - descuento);

    document.getElementById('resumen-subtotal').textContent  = 'RD$ ' + subtotal.toFixed(2);
    document.getElementById('resumen-descuento').textContent = 'RD$ ' + descuento.toFixed(2);
    document.getElementById('resumen-total').textContent     = 'RD$ ' + total.toFixed(2);

    document.getElementById('campo-subtotal').value  = subtotal.toFixed(2);
    document.getElementById('campo-descuento').value = descuento.toFixed(2);
    document.getElementById('campo-total').value     = total.toFixed(2);
}

async function validarCupon() {
    const codigo = document.getElementById('input-cupon').value.trim();
    const res    = document.getElementById('cupon-resultado');
    if (!codigo) return;

    try {
        const r    = await fetch('<?= BASE_URL ?>cupones/validar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'codigo=' + encodeURIComponent(codigo)
        });
        const data = await r.json();

        if (data.valido) {
            descuentoAplicado = parseFloat(data.cupon.descuento);
            tipoCupon         = data.cupon.tipo;
            document.getElementById('cupon-aplicado').value = codigo;
            res.style.color   = 'var(--exito)';
            res.textContent   = '✔ Cupón aplicado: ' + (tipoCupon === 'Monto_fijo'
                ? 'RD$ ' + descuentoAplicado + ' de descuento'
                : descuentoAplicado + '% de descuento');
            recalcular();
        } else {
            descuentoAplicado = 0;
            tipoCupon = '';
            document.getElementById('cupon-aplicado').value = '';
            res.style.color = 'var(--peligro)';
            res.textContent = '✖ ' + data.mensaje;
            recalcular();
        }
    } catch(e) {
        res.style.color = 'var(--peligro)';
        res.textContent = 'Error al validar el cupón.';
    }
}
</script>
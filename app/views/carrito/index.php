<?php
$items = $items ?? [];
$total = $total ?? 0;
$carrito = $carrito ?? [];
$estadoProductos = $estadoProductos ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Mi Carrito</h1>
        <p class="page-sub"><?= count($items) ?> producto(s) en el carrito</p>
    </div>
    <a href="<?= BASE_URL ?>productos" class="btn btn-contorno">← Seguir comprando</a>
</div>

<?php if (!empty($inactivos)): ?>
    <div class="alert-stock">
        <span>⚠️</span>
        <span>
            <strong>
                <?= count($inactivos) ?> producto(s)
            </strong> fueron removidos porque están inactivos:
        </span>
        <ul style="list-style: none;">
            <?php foreach ($inactivos as $prod): ?>
                <li>
                    <?= htmlspecialchars($prod['nombre']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (empty($items)): ?>
    <div class="panel" style="text-align:center;padding:60px 20px">
        <div style="font-size:3rem;margin-bottom:16px">🛒</div>
        <h2 style="color:var(--primario);margin-bottom:8px">Tu carrito está vacío</h2>
        <p class="texto-muted" style="margin-bottom:24px">Agrega productos del catálogo para comenzar.</p>
        <a href="<?= BASE_URL ?>productos" class="btn btn-primario">Ver catálogo</a>
    </div>
<?php else: ?>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

        <!-- Items -->
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-titulo">Productos</h2>
                <form method="POST" action="<?= BASE_URL ?>carrito/vaciar">
                    <button class="btn-tabla btn-tabla--eliminar" type="submit"
                        onclick="return confirm('¿Vaciar el carrito?')">Vaciar carrito</button>
                </form>
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
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($item['nombre']) ?></strong></td>
                                <td>RD$ <?= number_format($item['precioUnitario'], 2) ?></td>
                                <td>
                                    <form method="POST" action="<?= BASE_URL ?>carrito/actualizar"
                                        style="display:flex;align-items:center;gap:6px">
                                        <input type="hidden" name="idProducto" value="<?= $item['idProducto'] ?>">
                                        <input class="input-form" type="number" name="cantidad" value="<?= $item['cantidad'] ?>"
                                            min="1" style="width:60px;padding:4px 8px;text-align:center"
                                            onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td><strong>RD$ <?= number_format($item['subtotal'], 2) ?></strong></td>
                                <td>
                                    <form method="POST" action="<?= BASE_URL ?>carrito/eliminar-item">
                                        <input type="hidden" name="idProducto" value="<?= $item['idProducto'] ?>">
                                        <button class="btn-tabla btn-tabla--eliminar" type="submit">✕</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Resumen -->
        <div>
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-titulo">Resumen</h2>
                </div>
                <div style="padding:20px">

                    <!-- Cupón -->
                    <div class="grupo-form">
                        <label class="etiqueta-form">¿Tienes un cupón?</label>
                        <div style="display:flex;gap:8px">
                            <input class="input-form" type="text" id="input-cupon" placeholder="CODIGO"
                                style="text-transform:uppercase">
                            <button class="btn btn-contorno btn-sm" type="button" onclick="validarCupon()">Aplicar</button>
                        </div>
                        <div id="cupon-resultado" style="margin-top:6px;font-size:.82rem"></div>
                    </div>

                    <div class="separador-seccion"></div>

                    <div class="detalle-fila">
                        <span class="detalle-label">Subtotal</span>
                        <span id="subtotal">RD$ <?= number_format($total, 2) ?></span>
                    </div>
                    <div class="detalle-fila" style="font-size:1.1rem;font-weight:700;margin-top:12px">
                        <span>Total</span>
                        <span style="color:var(--acento)" id="total">RD$ <?= number_format($total, 2) ?></span>
                    </div>

                    <a href="<?= BASE_URL ?>pedidos/crear" class="btn btn-primario btn-completo" style="margin-top:20px">
                        Proceder al pago
                    </a>
                </div>
            </div>
        </div>

    </div>

<?php endif; ?>

<script>
    async function validarCupon() {
        const codigo = document.getElementById('input-cupon').value.trim();
        const res = document.getElementById('cupon-resultado');

        if (!codigo) return;

        try {
            const r = await fetch('<?= BASE_URL ?>cupones/validar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'codigo=' + encodeURIComponent(codigo)
            });
            const data = await r.json();

            if (data.valido) {
                res.style.color = 'var(--exito)';
                res.textContent = '✔ Cupón válido: ' + (data.cupon.tipo === 'Monto_fijo'
                    ? 'RD$ ' + data.cupon.descuento + ' de descuento'
                    : data.cupon.descuento + '% de descuento');
                data.cupon.tipo = data.cupon.tipo === 'Monto_fijo' ? 'Monto_fijo' : 'Porcentaje';
                aplicarDescuento(<?= $total ?>, data.cupon);
            } else {
                res.style.color = 'var(--peligro)';
                res.textContent = '✖ ' + data.mensaje;
            }
        } catch (e) {
            res.style.color = 'var(--peligro)';
            res.textContent = 'Error al validar el cupón.';
        }
    }

    function aplicarDescuento(total, cupon) {
        let nuevoTotal = total;

        if (cupon.tipo === 'Monto_fijo') {
            nuevoTotal = Math.max(0, total - cupon.descuento);
        } else if (cupon.tipo === 'Porcentaje') {
            nuevoTotal = Math.max(0, total * (1 - cupon.descuento / 100));
        }

        document.getElementById('subtotal').textContent = 'RD$ ' + nuevoTotal.toFixed(2);
        document.getElementById('total').textContent = 'RD$ ' + nuevoTotal.toFixed(2);

        return nuevoTotal;
    }



</script>
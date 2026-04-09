<?php $productos = $productos ?? []; ?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Inventario</h1>
        <p class="page-sub">Control de stock de productos</p>
    </div>
</div>

<!-- Alertas de stock bajo -->
<?php $bajoStock = array_filter($productos, fn($p) => $p['stock'] <= 5 && $p['activo']); ?>
<?php if (!empty($bajoStock)): ?>
<div class="alert-stock">
    <span>⚠️</span>
    <span><strong><?= count($bajoStock) ?> producto(s)</strong> con stock crítico (≤ 5 unidades)</span>
</div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <input class="input-buscar" type="text" id="buscador" placeholder="🔍 Buscar producto...">
        <select class="select-form" id="filtro-stock" style="width:auto">
            <option value="">Todo el inventario</option>
            <option value="critico">Stock crítico (≤ 5)</option>
            <option value="bajo">Stock bajo (≤ 20)</option>
            <option value="ok">Stock OK (> 20)</option>
        </select>
    </div>
    <div class="tabla-wrapper">
        <table class="tabla" id="tabla-inventario">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr><td colspan="6" class="tabla-vacia">No hay productos registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($productos as $p): ?>
                    <?php
                        $stockClase = match(true) {
                            $p['stock'] <= 5  => 'stock--critico',
                            $p['stock'] <= 20 => 'stock--bajo',
                            default           => 'stock--ok'
                        };
                        $stockLabel = match(true) {
                            $p['stock'] <= 5  => 'Crítico',
                            $p['stock'] <= 20 => 'Bajo',
                            default           => 'OK'
                        };
                        $stockData = $p['stock'] <= 5 ? 'critico' : ($p['stock'] <= 20 ? 'bajo' : 'ok');
                    ?>
                    <tr data-stock="<?= $stockData ?>">
                        <td><strong><?= htmlspecialchars($p['nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                        <td>RD$ <?= number_format($p['precio'], 2) ?></td>
                        <td>
                            <span class="stock-num <?= $stockClase ?>"><?= $p['stock'] ?> uds.</span>
                        </td>
                        <td>
                            <span class="badge <?= match($stockData) {
                                'critico' => 'badge--rojo',
                                'bajo'    => 'badge--amarillo',
                                default   => 'badge--verde'
                            } ?>"><?= $stockLabel ?></span>
                        </td>
                        <td class="acciones">
                            <a href="<?= BASE_URL ?>productos/editar?id=<?= $p['idProducto'] ?>"
                               class="btn-tabla btn-tabla--editar">Ajustar stock</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('buscador').addEventListener('input', filtrar);
document.getElementById('filtro-stock').addEventListener('change', filtrar);

function filtrar() {
    const q     = document.getElementById('buscador').value.toLowerCase();
    const stock = document.getElementById('filtro-stock').value;
    document.querySelectorAll('#tabla-inventario tbody tr').forEach(tr => {
        const textoOk = tr.textContent.toLowerCase().includes(q);
        const stockOk = !stock || tr.dataset.stock === stock;
        tr.style.display = textoOk && stockOk ? '' : 'none';
    });
}
</script>
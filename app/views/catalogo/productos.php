<?php $productos = $productos ?? []; ?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Catálogo de Productos</h1>
        <p class="page-sub"><?= count($productos) ?> productos registrados</p>
    </div>
    <a href="<?= BASE_URL ?>productos/crear" class="btn btn-primario">+ Nuevo producto</a>
</div>

<div class="panel">
    <div class="panel-header">
        <input class="input-buscar" type="text" id="buscador" placeholder="🔍 Buscar producto...">
    </div>
    <div class="tabla-wrapper">
        <table class="tabla" id="tabla-productos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Descuento</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr><td colspan="8" class="tabla-vacia">No hay productos registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td class="texto-muted"><?= $p['idProducto'] ?></td>
                        <td><strong><?= htmlspecialchars($p['nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                        <td>RD$ <?= number_format($p['precio'], 2) ?></td>
                        <td><?= $p['descuento'] > 0 ? $p['descuento'] . '%' : '—' ?></td>
                        <td>
                            <span class="<?= $p['stock'] <= 5 ? 'texto-peligro' : '' ?>">
                                <?= $p['stock'] ?> uds.
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $p['activo'] ? 'badge--verde' : 'badge--rojo' ?>">
                                <?= $p['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="acciones">
                            <a href="<?= BASE_URL ?>productos/ver?id=<?= $p['idProducto'] ?>" class="btn-tabla">Ver</a>
                            <a href="<?= BASE_URL ?>productos/editar?id=<?= $p['idProducto'] ?>" class="btn-tabla btn-tabla--editar">Editar</a>
                            <form method="POST" action="<?= BASE_URL ?>productos/eliminar" style="display:inline"
                                  onsubmit="return confirm('¿Desactivar este producto?')">
                                <input type="hidden" name="id" value="<?= $p['idProducto'] ?>">
                                <button class="btn-tabla btn-tabla--eliminar" type="submit">Desactivar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('buscador').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tabla-productos tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
<?php
$categorias = $categorias ?? [];
$total      = count($categorias);
$conProds   = count(array_filter($categorias, fn($c) => $c['totalProductos'] > 0));
$sinProds   = $total - $conProds;
?>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">
    <div class="stat-card">
        <div class="stat-icono stat-icono--azul">
            <img src="<?= BASE_URL ?>images/icons/catalogo-icon.png" class="icon" alt="categorías">
        </div>
        <div>
            <div class="stat-valor"><?= $total ?></div>
            <div class="stat-label">Total categorías</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--verde">
            <img src="<?= BASE_URL ?>images/icons/checkmark-icon.png" class="icon" alt="con productos">
        </div>
        <div>
            <div class="stat-valor"><?= $conProds ?></div>
            <div class="stat-label">Con productos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--naranja">
            <img src="<?= BASE_URL ?>images/icons/inventario-icon.png" class="icon" alt="vacías">
        </div>
        <div>
            <div class="stat-valor"><?= $sinProds ?></div>
            <div class="stat-label">Sin productos</div>
        </div>
    </div>
</div>

<!-- Encabezado -->
<div class="page-header">
    <div>
        <h1 class="page-titulo">Categorías</h1>
        <p class="page-sub"><?= $total ?> categorías registradas</p>
    </div>
    <a href="<?= BASE_URL ?>categorias/crear" class="btn btn-primario">+ Nueva categoría</a>
</div>

<!-- Tabla -->
<div class="panel">
    <div class="panel-header">
        <img src="<?= BASE_URL ?>images/icons/search-icon.png" class="icon" alt="buscar">
        <input class="input-buscar" type="text" id="buscador" placeholder="Buscar categoría...">
    </div>

    <div class="tabla-wrapper">
        <table class="tabla" id="tabla-categorias">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Productos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categorias)): ?>
                    <tr>
                        <td colspan="4" class="tabla-vacia">No hay categorías registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categorias as $c): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($c['nombre']) ?></strong>
                        </td>
                        <td class="texto-muted">
                            <?= $c['descripcion']
                                ? htmlspecialchars($c['descripcion'])
                                : '<em style="color:var(--text-muted)">Sin descripción</em>' ?>
                        </td>
                        <td>
                            <?php if ($c['totalProductos'] > 0): ?>
                                <span class="codigo"><?= $c['totalProductos'] ?> producto<?= $c['totalProductos'] != 1 ? 's' : '' ?></span>
                            <?php else: ?>
                                <span class="texto-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="acciones">
                            <a href="<?= BASE_URL ?>categorias/editar?id=<?= $c['idCategoria'] ?>"
                               class="btn-tabla btn-tabla--editar">Editar</a>

                            <?php if ($c['totalProductos'] == 0): ?>
                            <form method="POST" action="<?= BASE_URL ?>categorias/eliminar"
                                  style="display:inline"
                                  onsubmit="return confirm('¿Eliminar la categoría «<?= htmlspecialchars($c['nombre']) ?>»?')">
                                <input type="hidden" name="id" value="<?= $c['idCategoria'] ?>">
                                <button class="btn-tabla btn-tabla--eliminar" type="submit">Eliminar</button>
                            </form>
                            <?php else: ?>
                                <span class="texto-muted" style="font-size:.78rem"
                                      title="Tiene productos asociados">No eliminable</span>
                            <?php endif; ?>
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
    document.querySelectorAll('#tabla-categorias tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
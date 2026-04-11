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
    <div class="catalogo-grid">
        <!-- Tarjeta de producto -->
        <?php foreach ($productos as $producto): ?>
            <div class="producto-card">
                <span class="imagen"><img src="<?= htmlspecialchars($producto['imagenes']) ?>"
                        alt="<?= htmlspecialchars($producto['nombre']) ?>"></span>
                <span class="categoria"><?= htmlspecialchars($producto['categoria']) ?></span>
                <h3 class="nombre"><?= htmlspecialchars($producto['nombre']) ?></h3>
                <?php if ($producto['descuento'] > 0): ?>
                    <span class="descuento">-<?= $producto['descuento'] ?>%</span>
                <?php endif; ?>
                <div class="calificacion">
                </div>
                <div class="precio">
                    <span class="precio-actual">RD$
                        <?= number_format($producto['precio'] * (1 - $producto['descuento'] / 100), 2) ?></span>
                    <?php if ($producto['descuento'] > 0): ?>
                        <span class="precio-anterior">RD$ <?= number_format($producto['precio'], 2) ?></span>
                    <?php endif; ?>
                </div>
                <span class="stock">Stock: <?= $producto['stock'] ?></span>
                <form action="<?= BASE_URL ?>productos/agregarAlCarrito" method="post">
                    <input type="hidden" name="idProducto" value="<?= $producto['idProducto'] ?>">
                    <input type="number" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>" required>
                    <button type="submit" class="btn btn-primario">Agregar 🛒</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    document.getElementById('buscador').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.producto-card').forEach(card => {
            const nombre = card.querySelector('.nombre').textContent.toLowerCase();
            const categoria = card.querySelector('.categoria').textContent.toLowerCase();
            if (nombre.includes(q) || categoria.includes(q)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
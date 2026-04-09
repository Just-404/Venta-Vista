<?php
$producto     = $producto     ?? [];
$calificaciones = $calificaciones ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo"><?= htmlspecialchars($producto['nombre'] ?? '') ?></h1>
        <p class="page-sub">Detalle del producto</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?= BASE_URL ?>productos/editar?id=<?= $producto['idProducto'] ?>" class="btn btn-primario">Editar</a>
        <a href="<?= BASE_URL ?>productos" class="btn btn-contorno">← Volver</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <!-- Info general -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Información general</h2></div>
        <div style="padding:20px">
            <div class="detalle-fila">
                <span class="detalle-label">Nombre</span>
                <span><?= htmlspecialchars($producto['nombre']) ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Categoría</span>
                <span><?= htmlspecialchars($producto['categoria'] ?? '—') ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Precio</span>
                <span><strong>RD$ <?= number_format($producto['precio'], 2) ?></strong></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Descuento</span>
                <span><?= $producto['descuento'] > 0 ? $producto['descuento'] . '%' : '—' ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Stock</span>
                <span class="<?= $producto['stock'] <= 5 ? 'texto-peligro' : '' ?>">
                    <?= $producto['stock'] ?> unidades
                </span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Estado</span>
                <span class="badge <?= $producto['activo'] ? 'badge--verde' : 'badge--rojo' ?>">
                    <?= $producto['activo'] ? 'Activo' : 'Inactivo' ?>
                </span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Fecha creación</span>
                <span class="texto-muted"><?= date('d/m/Y', strtotime($producto['fechaCreacion'])) ?></span>
            </div>
            <?php if (!empty($producto['descripcion'])): ?>
            <div style="margin-top:16px">
                <div class="detalle-label" style="margin-bottom:6px">Descripción</div>
                <p style="color:var(--texto);font-size:.9rem;line-height:1.6">
                    <?= htmlspecialchars($producto['descripcion']) ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Calificaciones -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Calificaciones</h2></div>
        <div style="padding:20px">
            <?php if (empty($calificaciones)): ?>
                <p class="texto-muted">Aún no hay calificaciones.</p>
            <?php else: ?>
                <?php foreach ($calificaciones as $cal): ?>
                <div class="calificacion-item">
                    <div class="calificacion-header">
                        <strong><?= htmlspecialchars($cal['cliente']) ?></strong>
                        <span class="texto-muted" style="font-size:.78rem"><?= date('d/m/Y', strtotime($cal['fecha'])) ?></span>
                    </div>
                    <div class="estrellas">
                        <?= str_repeat('⭐', $cal['nota']) . str_repeat('☆', 5 - $cal['nota']) ?>
                    </div>
                    <?php if (!empty($cal['comentario'])): ?>
                        <p style="font-size:.85rem;color:var(--texto);margin-top:4px">
                            <?= htmlspecialchars($cal['comentario']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
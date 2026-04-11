<?php
$producto = $producto ?? [];
$calificaciones = $calificaciones ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo"><?= htmlspecialchars($producto['nombre'] ?? '') ?></h1>
        <p class="page-sub">Detalle del producto</p>
    </div>
    <div style="display:flex;gap:8px">
        <?php if ($usuario['rol'] != 3): ?>
            <a href="<?= BASE_URL ?>productos/editar?id=<?= $producto['idProducto'] ?>" class="btn btn-primario">Editar</a>
        <?php endif; ?>
        <?php if ($usuario['rol'] == 3): ?>
            <div class="acciones-carrito">
                <form action="<?= BASE_URL ?>/carrito/agregar" method="post" class="form-carrito">
                    <input type="hidden" name="idProducto" value="<?= $producto['idProducto'] ?>">
                    <input class="input-form" type="number" name="cantidad" value="1" min="1"
                        max="<?= $producto['stock'] ?>" required>
                    <button type="submit" class="btn btn-primario"> Agregar <span class="carrito-blanco">🛒</span></button>
                </form>
            </div>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>productos" class="btn btn-contorno">← Volver</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <!-- Info general -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-titulo">Información general</h2>
        </div>
        <div style="padding:20px">
            <div class="detalle-fila">
                <span class="detalle-label">Imagen</span>
                <img src="<?= htmlspecialchars($producto['imagenes']) ?>"
                    alt="<?= htmlspecialchars($producto['nombre']) ?>"
                    style="width:30%;height:auto;object-fit:cover;border-radius:6px;margin-bottom:20px"
                    onerror="this.onerror=null; this.src='https://community.softr.io/uploads/db9110/original/2X/7/74e6e7e382d0ff5d7773ca9a87e6f6f8817a68a6.jpeg';">
            </div>
        </div>
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
        <div class="panel-header">
            <h2 class="panel-titulo">Calificaciones</h2>
        </div>
        <div style="padding:20px">
            <?php if (empty($calificaciones)): ?>
                <p class="texto-muted">Aún no hay calificaciones.</p>
            <?php else: ?>
                <?php foreach ($calificaciones as $cal): ?>
                    <div class="calificacion-item">
                        <div class="calificacion-header">
                            <strong><?= htmlspecialchars($cal['cliente']) ?></strong>
                            <span class="texto-muted"
                                style="font-size:.78rem"><?= date('d/m/Y', strtotime($cal['fecha'])) ?></span>
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
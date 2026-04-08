<?php
$totalPedidos   = $totalPedidos   ?? 0;
$totalProductos = $totalProductos ?? 0;
$totalClientes  = $totalClientes  ?? 0;
$totalUsuarios  = $totalUsuarios  ?? 0;
$pedidosRecientes    = $pedidosRecientes    ?? [];
$productosDestacados = $productosDestacados ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Dashboard</h1>
        <p class="page-sub">Resumen general del sistema</p>
    </div>
</div>

<!-- Tarjetas de estadísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icono stat-icono--azul">📦</div>
        <div class="stat-info">
            <div class="stat-valor"><?= $totalPedidos ?></div>
            <div class="stat-label">Pedidos totales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--naranja">🏷️</div>
        <div class="stat-info">
            <div class="stat-valor"><?= $totalProductos ?></div>
            <div class="stat-label">Productos activos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--verde">👥</div>
        <div class="stat-info">
            <div class="stat-valor"><?= $totalClientes ?></div>
            <div class="stat-label">Clientes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--morado">👤</div>
        <div class="stat-info">
            <div class="stat-valor"><?= $totalUsuarios ?></div>
            <div class="stat-label">Usuarios</div>
        </div>
    </div>
</div>

<!-- Pedidos recientes -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-titulo">Pedidos recientes</h2>
        <a href="<?= BASE_URL ?>pedidos" class="btn btn-contorno btn-sm">Ver todos</a>
    </div>
    <div class="tabla-wrapper">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pedidosRecientes)): ?>
                    <tr><td colspan="6" class="tabla-vacia">No hay pedidos registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($pedidosRecientes as $p): ?>
                    <tr>
                        <td><span class="codigo"><?= htmlspecialchars($p['numeroPedido']) ?></span></td>
                        <td><?= htmlspecialchars($p['cliente']) ?></td>
                        <td><strong>RD$ <?= number_format($p['total'], 2) ?></strong></td>
                        <td><?php include __DIR__ . '/../partials/badge-estado.php'; ?></td>
                        <td class="texto-muted"><?= date('d/m/Y', strtotime($p['fechaPedido'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>pedidos/ver?id=<?= $p['idPedido'] ?>" class="btn-tabla">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Productos destacados -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-titulo">Productos destacados</h2>
        <a href="<?= BASE_URL ?>productos" class="btn btn-contorno btn-sm">Ver catálogo</a>
    </div>
    <div class="productos-grid">
        <?php if (empty($productosDestacados)): ?>
            <p class="texto-muted">No hay productos disponibles.</p>
        <?php else: ?>
            <?php foreach ($productosDestacados as $prod): ?>
            <div class="producto-card">
                <div class="producto-img">🏷️</div>
                <div class="producto-info">
                    <div class="producto-nombre"><?= htmlspecialchars($prod['nombre']) ?></div>
                    <div class="producto-categoria"><?= htmlspecialchars($prod['categoria']) ?></div>
                    <div class="producto-precio">RD$ <?= number_format($prod['precio'], 2) ?></div>
                    <?php if (!empty($prod['promedio'])): ?>
                        <div class="producto-rating">⭐ <?= $prod['promedio'] ?> (<?= $prod['totalResenas'] ?>)</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
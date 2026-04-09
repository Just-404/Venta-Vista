<?php
$pedidos     = $pedidos     ?? [];
$porVendedor = $porVendedor ?? [];
$todos        = $todos       ?? [];

// Calcular totales
$totalVentas    = array_sum(array_column($pedidos, 'total'));
$pedidosEntregados = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Entregado'));
$pedidosPendientes = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Pendiente'));
$pedidosCancelados = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Cancelado'));

// Ventas por estado
$porEstado = [];
foreach ($pedidos as $p) {
    $porEstado[$p['estado']] = ($porEstado[$p['estado']] ?? 0) + 1;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Reportes</h1>
        <p class="page-sub">Resumen general de ventas y rendimiento</p>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icono stat-icono--naranja">💰</div>
        <div class="stat-info">
            <div class="stat-valor">RD$ <?= number_format($totalVentas, 0) ?></div>
            <div class="stat-label">Ingresos totales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--verde">✅</div>
        <div class="stat-info">
            <div class="stat-valor"><?= $pedidosEntregados ?></div>
            <div class="stat-label">Pedidos entregados</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--azul">⏳</div>
        <div class="stat-info">
            <div class="stat-valor"><?= $pedidosPendientes ?></div>
            <div class="stat-label">Pedidos pendientes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--morado">❌</div>
        <div class="stat-info">
            <div class="stat-valor"><?= $pedidosCancelados ?></div>
            <div class="stat-label">Pedidos cancelados</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <!-- Pedidos por estado -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Pedidos por estado</h2></div>
        <div style="padding:20px">
            <?php foreach ($porEstado as $estado => $cantidad): ?>
            <?php $pct = count($pedidos) > 0 ? round($cantidad / count($pedidos) * 100) : 0; ?>
            <div style="margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:.85rem">
                    <span><?= str_replace('_', ' ', $estado) ?></span>
                    <span class="texto-muted"><?= $cantidad ?> (<?= $pct ?>%)</span>
                </div>
                <div class="barra-fondo">
                    <div class="barra-progreso <?= match($estado) {
                        'Entregado'  => 'barra--verde',
                        'Cancelado','Devuelto' => 'barra--roja',
                        'Enviado','En_proceso' => 'barra--azul',
                        default => 'barra--amarilla'
                    } ?>" style="width:<?= $pct ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Ventas por vendedor -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Ventas por vendedor</h2></div>
        <div class="tabla-wrapper">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Vendedor</th>
                        <th>Pedidos</th>
                        <th>Monto total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($porVendedor)): ?>
                        <tr><td colspan="3" class="tabla-vacia">Sin datos.</td></tr>
                    <?php else: ?>
                        <?php foreach ($porVendedor as $v): ?>
                        <tr>
                            <td>
                                <div class="avatar-fila">
                                    <div class="avatar-mini"><?= strtoupper(substr($v['vendedor'], 0, 1)) ?></div>
                                    <?= htmlspecialchars($v['vendedor']) ?>
                                </div>
                            </td>
                            <td><?= $v['totalPedidos'] ?></td>
                            <td><strong>RD$ <?= number_format($v['montoTotal'], 2) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Todos los pedidos -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-titulo">Historial de pedidos</h2>
        <div style="display:flex;gap:8px">
            <a href="<?= BASE_URL ?>reportes/ventas" class="btn btn-contorno btn-sm">Detalle ventas</a>
            <a href="<?= BASE_URL ?>reportes/productos" class="btn btn-contorno btn-sm">Productos</a>
            <a href="<?= BASE_URL ?>reportes/clientes" class="btn btn-contorno btn-sm">Clientes</a>
        </div>
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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td><span class="codigo"><?= htmlspecialchars($p['numeroPedido']) ?></span></td>
                    <td><?= htmlspecialchars($p['cliente']) ?></td>
                    <td><strong>RD$ <?= number_format($p['total'], 2) ?></strong></td>
                    <td><?php include __DIR__ . '/../partials/badge-estado.php'; ?></td>
                    <td class="texto-muted"><?= date('d/m/Y', strtotime($p['fechaPedido'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
/* Variables con valores por defecto */
$ventasMes           = $ventasMes           ?? 0;
$crecimientoMes      = $crecimientoMes      ?? null;
$pedidosActivos      = $pedidosActivos      ?? 0;
$pedidosHoy          = $pedidosHoy          ?? 0;
$clientesRegistrados = $clientesRegistrados ?? 0;
$clientesEstaSemana  = $clientesEstaSemana  ?? 0;
$sinStock            = $sinStock            ?? 0;
$ventasSemanales     = $ventasSemanales     ?? [];
$ventasCategorias    = $ventasCategorias    ?? [];
$pedidosRecientes    = $pedidosRecientes    ?? [];
$totalUsuarios       = $totalUsuarios       ?? null;
$rol                 = $usuario['rol']      ?? 0;

/* Preparar datos para Chart.js */
$semanasLabels = json_encode(array_column($ventasSemanales, 'label'));
$semanasTotals = json_encode(array_column($ventasSemanales, 'total'));

$catLabels = json_encode(array_column($ventasCategorias, 'categoria'));
$catTotals = json_encode(array_column($ventasCategorias, 'total'));
?>

<!-- ══════════════════════ ESTILOS DEL DASHBOARD ══════════════════════ -->
<style>
/* ── Cabecera ── */
.db-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}
.db-header h1 { font-size: 1.6rem; font-weight: 700; margin: 0; color: var(--texto-principal, #1a2340); }
.db-header p  { font-size: 0.82rem; color: var(--texto-muted, #7a8699); margin: 0.15rem 0 0; }

/* ── Tarjetas de métricas ── */
.db-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 1100px) { .db-stats { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 600px)  { .db-stats { grid-template-columns: 1fr; } }

.db-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.15rem 1.3rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 6px rgba(0,0,0,.07);
    border: 1px solid #f0f2f5;
    position: relative;
    overflow: hidden;
}
.db-card--warn { border-left: 4px solid #f59e0b; }

.db-card-icon {
    width: 46px; height: 46px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}
.db-card-icon--orange { background: #fff3e0; }
.db-card-icon--blue   { background: #e8f4fd; }
.db-card-icon--green  { background: #e6f9f0; }
.db-card-icon--yellow { background: #fffbeb; }

.db-card-body { flex: 1; }
.db-card-value {
    font-size: 1.55rem; font-weight: 700;
    color: var(--texto-principal, #1a2340);
    line-height: 1.1;
}
.db-card-label {
    font-size: 0.78rem; color: var(--texto-muted, #7a8699);
    margin-top: 2px;
}
.db-card-badge {
    font-size: 0.72rem; font-weight: 600;
    margin-top: 4px; display: inline-flex; align-items: center; gap: 3px;
}
.db-card-badge--up   { color: #16a34a; }
.db-card-badge--down { color: #dc2626; }
.db-card-badge--warn { color: #d97706; }

/* ── Fila de gráficos ── */
.db-charts {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 1000px) { .db-charts { grid-template-columns: 1fr; } }

.db-panel {
    background: #fff;
    border-radius: 12px;
    padding: 1.2rem 1.4rem;
    box-shadow: 0 1px 6px rgba(0,0,0,.07);
    border: 1px solid #f0f2f5;
}
.db-panel-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 1rem;
}
.db-panel-title { font-size: 1rem; font-weight: 700; color: var(--texto-principal,#1a2340); }

.db-select {
    font-size: 0.78rem; border: 1px solid #e2e6ed;
    border-radius: 7px; padding: 4px 10px;
    color: var(--texto-principal,#1a2340);
    background: #fff; cursor: pointer;
}

.db-chart-wrap { position: relative; height: 220px; }

/* ── Leyenda categorías ── */
.db-legend {
    display: flex; flex-wrap: wrap; gap: 6px 12px;
    margin-top: 0.75rem; justify-content: center;
}
.db-legend-item { display: flex; align-items: center; gap: 5px; font-size: 0.73rem; color: #555; }
.db-legend-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

/* ── Tabla pedidos recientes ── */
.db-table-panel { background:#fff; border-radius:12px; padding:1.2rem 1.4rem;
    box-shadow:0 1px 6px rgba(0,0,0,.07); border:1px solid #f0f2f5; }

.db-table { width:100%; border-collapse:collapse; font-size:0.85rem; }
.db-table th {
    text-align:left; font-size:0.73rem; font-weight:600; letter-spacing:.04em;
    color:#7a8699; text-transform:uppercase; padding:8px 12px;
    border-bottom:1px solid #f0f2f5;
}
.db-table td { padding:10px 12px; border-bottom:1px solid #f8f9fb; vertical-align:middle; }
.db-table tr:last-child td { border-bottom:none; }
.db-table tr:hover td { background:#fafbfc; }

.db-num  { font-weight:700; color:var(--texto-principal,#1a2340); font-size:0.82rem; }
.db-muted { color:#7a8699; }
.db-total { font-weight:700; }

/* badges de estado */
.badge {
    display:inline-block; padding:3px 10px; border-radius:20px;
    font-size:0.72rem; font-weight:600;
}
.badge--entregado  { background:#d1fae5; color:#065f46; }
.badge--enviado    { background:#dbeafe; color:#1e40af; }
.badge--en_proceso { background:#ede9fe; color:#6d28d9; }
.badge--confirmado { background:#dbeafe; color:#1d4ed8; }
.badge--pendiente  { background:#fef3c7; color:#92400e; }
.badge--cancelado  { background:#fee2e2; color:#991b1b; }
.badge--devuelto   { background:#f3f4f6; color:#374151; }

.btn-ver {
    font-size:0.75rem; padding:4px 12px; border-radius:6px;
    border:1px solid #e2e6ed; background:#fff; color:#374151;
    cursor:pointer; text-decoration:none;
    transition: background .15s;
}
.btn-ver:hover { background:#f3f4f6; }

.db-ver-todos {
    font-size:0.8rem; color: var(--color-primario, #e8622a);
    text-decoration:none; font-weight:600;
}
.db-ver-todos:hover { text-decoration:underline; }

/* Admin extra row */
.db-admin-row {
    display:grid; grid-template-columns:repeat(2,1fr); gap:1rem;
    margin-bottom:1.5rem;
}
</style>

<!-- ══════════════════════ CABECERA ══════════════════════ -->
<div class="db-header">
    <div>
        <h1>Dashboard</h1>
        <p>Inicio / Dashboard</p>
    </div>
</div>

<!-- ══════════════════════ TARJETAS MÉTRICAS ══════════════════════ -->
<div class="db-stats">

    <!-- Ventas del Mes -->
    <div class="db-card">
        <div class="db-card-icon db-card-icon--orange">💰</div>
        <div class="db-card-body">
            <div class="db-card-value">RD$<?= number_format($ventasMes, 0, '.', ',') ?></div>
            <div class="db-card-label">Ventas del Mes</div>
            <?php if ($crecimientoMes !== null): ?>
                <div class="db-card-badge <?= $crecimientoMes >= 0 ? 'db-card-badge--up' : 'db-card-badge--down' ?>">
                    <?= $crecimientoMes >= 0 ? '▲' : '▼' ?> <?= abs($crecimientoMes) ?>% vs mes anterior
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pedidos Activos -->
    <div class="db-card">
        <div class="db-card-icon db-card-icon--blue">📦</div>
        <div class="db-card-body">
            <div class="db-card-value"><?= $pedidosActivos ?></div>
            <div class="db-card-label">Pedidos Activos</div>
            <?php if ($pedidosHoy > 0): ?>
                <div class="db-card-badge db-card-badge--up">▲ <?= $pedidosHoy ?> nuevos hoy</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Clientes Registrados -->
    <div class="db-card">
        <div class="db-card-icon db-card-icon--green">👥</div>
        <div class="db-card-body">
            <div class="db-card-value"><?= $clientesRegistrados ?></div>
            <div class="db-card-label">Clientes Registrados</div>
            <?php if ($clientesEstaSemana > 0): ?>
                <div class="db-card-badge db-card-badge--up">▲ <?= $clientesEstaSemana ?> esta semana</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Productos Sin Stock -->
    <div class="db-card <?= $sinStock > 0 ? 'db-card--warn' : '' ?>">
        <div class="db-card-icon db-card-icon--yellow">⚠️</div>
        <div class="db-card-body">
            <div class="db-card-value"><?= $sinStock ?></div>
            <div class="db-card-label">Productos Sin Stock</div>
            <?php if ($sinStock > 0): ?>
                <div class="db-card-badge db-card-badge--warn">▼ Requiere atención</div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ══════════════════════ GRÁFICOS ══════════════════════ -->
<div class="db-charts">

    <!-- Ventas Semanales -->
    <div class="db-panel">
        <div class="db-panel-header">
            <span class="db-panel-title">Ventas Semanales</span>
            <select class="db-select" id="filtroSemanas">
                <option value="4">Últimas 4 semanas</option>
                <option value="8">Últimas 8 semanas</option>
            </select>
        </div>
        <div class="db-chart-wrap">
            <canvas id="chartBarras"></canvas>
        </div>
    </div>

    <!-- Ventas por Categoría -->
    <div class="db-panel">
        <div class="db-panel-header">
            <span class="db-panel-title">Ventas por Categoría</span>
        </div>
        <div class="db-chart-wrap" style="height:180px;">
            <canvas id="chartDonut"></canvas>
        </div>
        <div class="db-legend" id="legendCat"></div>
    </div>

</div>

<!-- ══════════════════════ PEDIDOS RECIENTES ══════════════════════ -->
<div class="db-table-panel">
    <div class="db-panel-header">
        <span class="db-panel-title">Pedidos Recientes</span>
        <a href="<?= BASE_URL ?>pedidos" class="db-ver-todos">Ver todos →</a>
    </div>

    <div style="overflow-x:auto;">
        <table class="db-table">
            <thead>
                <tr>
                    <th>N° Pedido</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($pedidosRecientes)): ?>
                <tr><td colspan="6" style="text-align:center;padding:1.5rem;color:#7a8699;">No hay pedidos registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($pedidosRecientes as $p):
                    $estadoKey = strtolower(str_replace('_','-',$p['estado']));
                ?>
                <tr>
                    <td><span class="db-num"><?= htmlspecialchars($p['numeroPedido']) ?></span></td>
                    <td><?= htmlspecialchars($p['cliente']) ?></td>
                    <td class="db-muted"><?= date('d M \d\e Y', strtotime($p['fechaPedido'])) ?></td>
                    <td class="db-total">RD$<?= number_format($p['total'], 0, '.', ',') ?></td>
                    <td>
                        <span class="badge badge--<?= strtolower(str_replace(' ','_',$p['estado'])) ?>">
                            <?= htmlspecialchars($p['estado']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>pedidos/ver?id=<?= $p['idPedido'] ?>" class="btn-ver">Ver</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($rol === 1 && $totalUsuarios !== null): ?>
<!-- ══════ FILA EXTRA ADMIN ══════ -->
<div class="db-admin-row" style="margin-top:1rem;">
    <div class="db-panel" style="display:flex;align-items:center;gap:1rem;">
        <div class="db-card-icon db-card-icon--blue" style="font-size:1.5rem;">👤</div>
        <div>
            <div class="db-card-value"><?= $totalUsuarios ?></div>
            <div class="db-card-label">Usuarios del sistema</div>
        </div>
        <a href="<?= BASE_URL ?>usuarios" class="btn-ver" style="margin-left:auto;">Gestionar</a>
    </div>
    <div class="db-panel" style="display:flex;align-items:center;gap:1rem;">
        <div class="db-card-icon db-card-icon--orange" style="font-size:1.5rem;">🏷️</div>
        <div>
            <div class="db-card-value"><?= $totalProductos ?></div>
            <div class="db-card-label">Productos activos</div>
        </div>
        <a href="<?= BASE_URL ?>productos" class="btn-ver" style="margin-left:auto;">Ver catálogo</a>
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════ CHART.JS ══════════════════════ -->
<script>
(function () {
    const naranja  = '#e8622a';
    const naranjaTrans = 'rgba(232,98,42,.85)';
    const coloresCat = ['#e8622a','#3b82f6','#22c55e','#f59e0b','#a78bfa','#ec4899','#14b8a6'];

    /* ── Barras semanales ── */
    const labelsBar = <?= $semanasLabels ?>;
    const datosBar  = <?= $semanasTotals ?>;

    const ctxBar = document.getElementById('chartBarras')?.getContext('2d');
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labelsBar,
                datasets: [{
                    label: 'Ventas (RD$)',
                    data: datosBar,
                    backgroundColor: naranjaTrans,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false },
                           tooltip: { callbacks: { label: ctx => ' RD$' + ctx.parsed.y.toLocaleString() } } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 12 } } },
                    y: {
                        grid: { color: '#f0f2f5' },
                        ticks: { callback: v => 'RD$' + v.toLocaleString(), font: { size: 11 } }
                    }
                }
            }
        });
    }

    /* ── Donut categorías ── */
    const labelsCat = <?= $catLabels ?>;
    const datosCat  = <?= $catTotals ?>;

    const ctxDonut = document.getElementById('chartDonut')?.getContext('2d');
    if (ctxDonut) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: labelsCat,
                datasets: [{
                    data: datosCat,
                    backgroundColor: coloresCat.slice(0, labelsCat.length),
                    borderWidth: 2, borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ' RD$' + ctx.parsed.toLocaleString() } }
                }
            }
        });

        /* Leyenda personalizada */
        const legend = document.getElementById('legendCat');
        if (legend && labelsCat.length) {
            labelsCat.forEach((l, i) => {
                const item = document.createElement('div');
                item.className = 'db-legend-item';
                item.innerHTML = `<span class="db-legend-dot" style="background:${coloresCat[i]}"></span>${l}`;
                legend.appendChild(item);
            });
        }
    }
})();
</script>

<?php
$pedidos       = $pedidos       ?? [];
$porVendedor   = $porVendedor   ?? [];
$serieVendedor = $serieVendedor ?? [];
$todos         = $todos         ?? [];

// ── Totales ──────────────────────────────────────────────────────────────────
$totalVentas       = array_sum(array_column($pedidos, 'total'));
$pedidosEntregados = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Entregado'));
$pedidosPendientes = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Pendiente'));
$pedidosCancelados = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Cancelado'));

// ── Agrupación por estado ────────────────────────────────────────────────────
$porEstado = [];
foreach ($pedidos as $p) {
    $porEstado[$p['estado']] = ($porEstado[$p['estado']] ?? 0) + 1;
}

// ── Preparar datos para el gráfico de líneas de vendedores ──────────────────
$mesesSet     = [];
$vendedoresSet = [];
foreach ($serieVendedor as $row) {
    $mesesSet[$row['mes']]         = true;
    $vendedoresSet[$row['vendedor']] = true;
}
ksort($mesesSet);
$mesesLabels  = array_keys($mesesSet);
$vendedores   = array_keys($vendedoresSet);

// Formatea etiquetas de mes: "2026-01" → "Ene 26"
$mesesNombres = ['01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun',
                 '07'=>'Jul','08'=>'Ago','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'];
$labelsMeses  = array_map(function($m) use ($mesesNombres) {
    [$y, $mo] = explode('-', $m);
    return ($mesesNombres[$mo] ?? $mo) . ' ' . substr($y, 2);
}, $mesesLabels);

// Construir datasets indexados por vendedor → mes
$indexSerie = [];
foreach ($serieVendedor as $row) {
    $indexSerie[$row['vendedor']][$row['mes']] = (float)$row['total'];
}

// Paleta de colores para cada línea
$colores = ['#e85d26','#3b82f6','#22c55e','#f59e0b','#a855f7','#06b6d4'];
$datasets = [];
foreach ($vendedores as $i => $vend) {
    $color  = $colores[$i % count($colores)];
    $puntos = array_map(fn($m) => $indexSerie[$vend][$m] ?? 0, $mesesLabels);
    $datasets[] = [
        'label'           => $vend,
        'data'            => $puntos,
        'borderColor'     => $color,
        'backgroundColor' => $color . '22',
        'fill'            => true,
        'tension'         => 0.4,
        'pointRadius'     => 5,
        'pointHoverRadius'=> 7,
        'borderWidth'     => 2.5,
    ];
}

$chartJson    = json_encode(['labels' => $labelsMeses, 'datasets' => $datasets]);
$vendedorJson = json_encode($porVendedor);
?>

<!-- ══ ESTILOS PROPIOS DEL MÓDULO ══════════════════════════════════════════ -->
<style>
/* ── Tabs de navegación de reportes ────────────────────────────────────────── */
.rep-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.rep-tab {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: var(--radio-sm);
    font-size: .83rem;
    font-weight: 600;
    border: 1.5px solid var(--borde);
    background: var(--superficie);
    color: var(--texto-muted);
    cursor: pointer;
    text-decoration: none;
    transition: .18s;
}
.rep-tab:hover,
.rep-tab--activo {
    border-color: var(--acento);
    color: var(--acento);
    background: rgba(232,93,38,.06);
}
.rep-tab .rep-tab-icon { font-size: 1rem; }

/* ── Panel del gráfico de vendedores ───────────────────────────────────────── */
.vv-chart-wrap {
    position: relative;
    height: 260px;
    padding: 4px 8px 0;
}

/* ── Leyenda de vendedores ─────────────────────────────────────────────────── */
.vv-leyenda {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 18px;
    padding: 12px 20px 16px;
}
.vv-leyenda-item {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: .8rem;
    font-weight: 600;
    color: var(--texto);
}
.vv-leyenda-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* ── Tabla resumen vendedores ──────────────────────────────────────────────── */
.vv-resumen {
    border-top: 1px solid var(--borde);
    margin-top: 4px;
}

/* ── Botones exportar ──────────────────────────────────────────────────────── */
.export-group {
    display: flex;
    gap: 6px;
    align-items: center;
}
.btn-export {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 13px;
    border-radius: var(--radio-sm);
    font-size: .78rem;
    font-weight: 700;
    border: 1.5px solid;
    cursor: pointer;
    text-decoration: none;
    transition: .17s;
    background: var(--superficie);
    font-family: var(--fuente-cuerpo);
}
.btn-export--excel {
    border-color: #16a34a;
    color: #16a34a;
}
.btn-export--excel:hover { background: rgba(22,163,74,.08); }
.btn-export--pdf {
    border-color: var(--peligro);
    color: var(--peligro);
}
.btn-export--pdf:hover { background: rgba(239,68,68,.08); }

/* ── Estado vacío del gráfico ──────────────────────────────────────────────── */
.vv-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 220px;
    gap: 10px;
    color: var(--texto-muted);
}
.vv-empty svg { opacity: .35; }
</style>

<!-- ── Cabecera de página ──────────────────────────────────────────────────── -->
<div class="page-header">
    <div>
        <h1 class="page-titulo">Reportes</h1>
        <p class="page-sub">Resumen general de ventas y rendimiento</p>
    </div>
</div>

<!-- ── Estadísticas rápidas ───────────────────────────────────────────────── -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icono stat-icono--naranja">
            <img src="<?= BASE_URL ?>images/icons/bolsa-dinero-icon.png" class="icon" alt="logo sistema">
        </div>
        <div class="stat-info">
            <div class="stat-valor">RD$ <?= number_format($totalVentas, 0) ?></div>
            <div class="stat-label">Ingresos totales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--verde">
            <img src="<?= BASE_URL ?>images/icons/checkmark-icon.png" class="icon" alt="logo sistema">
        </div>
        <div class="stat-info">
            <div class="stat-valor"><?= $pedidosEntregados ?></div>
            <div class="stat-label">Pedidos entregados</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--azul">
            <img src="<?= BASE_URL ?>images/icons/pedido-pendiente-icon.png" class="icon" alt="logo sistema">
        </div>
        <div class="stat-info">
            <div class="stat-valor"><?= $pedidosPendientes ?></div>
            <div class="stat-label">Pedidos pendientes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--morado">
            <img src="<?= BASE_URL ?>images/icons/cancelado-icon.png" class="icon" alt="logo sistema">
        </div>
        <div class="stat-info">
            <div class="stat-valor"><?= $pedidosCancelados ?></div>
            <div class="stat-label">Pedidos cancelados</div>
        </div>
    </div>
</div>

<!-- ── Fila: Pedidos por estado + Ventas por vendedor ─────────────────────── -->
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
                        'Entregado'            => 'barra--verde',
                        'Cancelado','Devuelto' => 'barra--roja',
                        'Enviado','En_proceso'  => 'barra--azul',
                        default                => 'barra--amarilla'
                    } ?>" style="width:<?= $pct ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ── Ventas por vendedor ── gráfico de líneas ─────────────────────── -->
    <div class="panel">
        <div class="panel-header" style="justify-content:space-between;align-items:center">
            <h2 class="panel-titulo">Ventas por vendedor</h2>
            <?php if (!empty($porVendedor)): ?>
            <a href="<?= BASE_URL ?>reportes/exportar?tipo=ventas&formato=excel"
               class="btn-export btn-export--excel" title="Exportar tabla de vendedores a Excel">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11zM8 15h2v2H8zm0-3h2v2H8zm4 3h2v2h-2zm0-3h2v2h-2zm4 3h-2v2h2zm0-3h-2v2h2z"/></svg>
                Excel
            </a>
            <?php endif; ?>
        </div>

        <?php if (empty($serieVendedor) && empty($porVendedor)): ?>
            <!-- Estado vacío elegante -->
            <div class="vv-empty">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                    <path d="M3 3h18v18H3z" rx="2"/><path d="M7 16l3-4 3 2 3-6"/>
                    <circle cx="7" cy="16" r="1.5" fill="currentColor" stroke="none"/>
                    <circle cx="10" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                    <circle cx="13" cy="14" r="1.5" fill="currentColor" stroke="none"/>
                    <circle cx="16" cy="8" r="1.5" fill="currentColor" stroke="none"/>
                </svg>
                <span style="font-size:.88rem;font-weight:600">Sin datos de ventas por vendedor</span>
                <span style="font-size:.8rem">Los datos aparecerán aquí una vez haya pedidos registrados</span>
            </div>
        <?php else: ?>
            <!-- Gráfico de líneas -->
            <div class="vv-chart-wrap">
                <canvas id="chartVendedor"></canvas>
            </div>

            <!-- Leyenda manual con colores del gráfico -->
            <?php if (!empty($vendedores)): ?>
            <div class="vv-leyenda">
                <?php
                $coloresPHP = ['#e85d26','#3b82f6','#22c55e','#f59e0b','#a855f7','#06b6d4'];
                foreach ($vendedores as $idx => $vnom):
                    $col = $coloresPHP[$idx % count($coloresPHP)];
                ?>
                <div class="vv-leyenda-item">
                    <div class="vv-leyenda-dot" style="background:<?= $col ?>"></div>
                    <?= htmlspecialchars($vnom) ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Tabla resumen compacta -->
            <?php if (!empty($porVendedor)): ?>
            <div class="vv-resumen">
                <table class="tabla" style="font-size:.82rem">
                    <thead>
                        <tr>
                            <th>Vendedor</th>
                            <th style="text-align:center">Pedidos</th>
                            <th style="text-align:right">Monto total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($porVendedor as $v): ?>
                        <tr>
                            <td>
                                <div class="avatar-fila">
                                    <div class="avatar-mini"><?= strtoupper(substr($v['vendedor'], 0, 1)) ?></div>
                                    <?= htmlspecialchars($v['vendedor']) ?>
                                </div>
                            </td>
                            <td style="text-align:center"><?= $v['totalPedidos'] ?></td>
                            <td style="text-align:right"><strong>RD$ <?= number_format($v['montoTotal'], 2) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>

<!-- ── Historial de pedidos ──────────────────────────────────────────────── -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-titulo">Historial de pedidos</h2>
        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
            <!-- Tabs de reportes -->
            <div class="rep-tabs">
                <a href="<?= BASE_URL ?>reportes/ventas"
                   class="rep-tab">
                    <span class="rep-tab-icon">
                        <img src="<?= BASE_URL ?>images/icons/reportes-icon.png" class="icon" alt="logo sistema">
                    </span> Detalle ventas
                </a>
                <a href="<?= BASE_URL ?>reportes/productos"
                   class="rep-tab">
                    <span class="rep-tab-icon">
                        <img src="<?= BASE_URL ?>images/icons/pedido-icon.png" class="icon" alt="logo sistema">
                    </span> Productos
                </a>
                <a href="<?= BASE_URL ?>reportes/clientes"
                   class="rep-tab">
                    <span class="rep-tab-icon">
                        <img src="<?= BASE_URL ?>images/icons/people-icon.png" class="icon" alt="logo sistema">
                    </span> Clientes
                </a>
            </div>
            <!-- Exportar historial -->
            <div class="export-group">
                <a href="<?= BASE_URL ?>reportes/exportar?tipo=ventas&formato=excel"
                   class="btn-export btn-export--excel" title="Exportar a Excel">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
                    Excel
                </a>
                <a href="<?= BASE_URL ?>reportes/exportar?tipo=ventas&formato=pdf"
                   class="btn-export btn-export--pdf" title="Exportar a PDF" target="_blank">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg>
                    PDF
                </a>
            </div>
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

<!-- ── Script Chart.js — gráfico de líneas vendedores ───────────────────── -->
<?php if (!empty($serieVendedor)): ?>
<script>
(function () {
    const ctx = document.getElementById('chartVendedor');
    if (!ctx) return;

    const data = <?= $chartJson ?>;

    new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },   // leyenda propia en HTML
                tooltip: {
                    backgroundColor: '#1e2a4a',
                    titleColor: '#fff',
                    bodyColor: 'rgba(255,255,255,.85)',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ': RD$ ' +
                            Number(ctx.parsed.y).toLocaleString('es-DO', {minimumFractionDigits: 2})
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(221,226,239,.5)', drawBorder: false },
                    ticks: { font: { size: 11, family: "'Plus Jakarta Sans',sans-serif" }, color: '#6b7494' }
                },
                y: {
                    grid: { color: 'rgba(221,226,239,.5)', drawBorder: false },
                    ticks: {
                        font: { size: 11, family: "'Plus Jakarta Sans',sans-serif" },
                        color: '#6b7494',
                        callback: v => 'RD$ ' + Number(v).toLocaleString('es-DO')
                    }
                }
            }
        }
    });
})();
</script>
<?php endif; ?>

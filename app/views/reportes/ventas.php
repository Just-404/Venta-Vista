<?php


$pedidos     = $pedidos     ?? [];
$porVendedor = $porVendedor ?? [];

$totalVentas       = array_sum(array_column($pedidos, 'total'));
$pedidosEntregados = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Entregado'));
$pedidosCancelados = count(array_filter($pedidos, fn($p) => $p['estado'] === 'Cancelado'));
$ticketPromedio    = count($pedidos) > 0 ? $totalVentas / count($pedidos) : 0;

// Ventas por mes para el gráfico de barras
$porMes = [];
foreach ($pedidos as $p) {
    if (in_array($p['estado'], ['Cancelado','Devuelto'])) continue;
    $mes = date('Y-m', strtotime($p['fechaPedido']));
    $porMes[$mes] = ($porMes[$mes] ?? 0) + $p['total'];
}
ksort($porMes);

$mesesNombres = ['01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun',
                 '07'=>'Jul','08'=>'Ago','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'];
$labMeses = array_map(function($m) use ($mesesNombres) {
    [$y, $mo] = explode('-', $m);
    return ($mesesNombres[$mo] ?? $mo) . ' ' . substr($y, 2);
}, array_keys($porMes));
$valMeses = array_values($porMes);

$chartBarJson = json_encode(['labels' => $labMeses, 'data' => $valMeses]);
?>

<style>
.rep-back { display:inline-flex;align-items:center;gap:6px;font-size:.85rem;font-weight:600;color:var(--texto-muted);text-decoration:none;margin-bottom:18px;transition:.15s; }
.rep-back:hover { color:var(--acento); }
.rep-kpi-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px; }
.rep-kpi {
    background:var(--superficie);
    border:1.5px solid var(--borde);
    border-radius:var(--radio);
    padding:20px 22px;
    display:flex;flex-direction:column;gap:6px;
    transition:.2s;
}
.rep-kpi:hover { box-shadow:var(--sombra-md);transform:translateY(-2px); }
.rep-kpi-label { font-size:.78rem;font-weight:600;color:var(--texto-muted);text-transform:uppercase;letter-spacing:.04em; }
.rep-kpi-valor { font-size:1.55rem;font-weight:800;color:var(--texto); }
.rep-kpi-sub   { font-size:.78rem;color:var(--texto-muted); }
.rep-kpi--acento  { border-top:3px solid var(--acento); }
.rep-kpi--verde   { border-top:3px solid var(--exito); }
.rep-kpi--azul    { border-top:3px solid var(--info); }
.rep-kpi--rojo    { border-top:3px solid var(--peligro); }

.dos-cols { display:grid;grid-template-columns:1.6fr 1fr;gap:20px; }
.chart-box { height:230px;position:relative;padding:8px; }

.export-group { display:flex;gap:6px;align-items:center; }
.btn-export { display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:var(--radio-sm);font-size:.78rem;font-weight:700;border:1.5px solid;cursor:pointer;text-decoration:none;transition:.17s;background:var(--superficie);font-family:var(--fuente-cuerpo); }
.btn-export--excel { border-color:#16a34a;color:#16a34a; }
.btn-export--excel:hover { background:rgba(22,163,74,.08); }
.btn-export--pdf   { border-color:var(--peligro);color:var(--peligro); }
.btn-export--pdf:hover { background:rgba(239,68,68,.08); }
</style>

<!-- Volver -->
<a href="<?= BASE_URL ?>reportes" class="rep-back">
    ← Volver a Reportes
</a>

<!-- Título -->
<div class="page-header" style="margin-bottom:20px">
    <div>
        <h1 class="page-titulo">
            <img src="<?= BASE_URL ?>images/icons/reportes-icon.png" class="icon" alt="logo sistema"> Detalle de Ventas
        </h1>
        <p class="page-sub">Análisis completo del rendimiento de ventas</p>
    </div>
    <div class="export-group">
        <a href="<?= BASE_URL ?>reportes/exportar?tipo=ventas&formato=excel"
           class="btn-export btn-export--excel">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            Exportar Excel
        </a>
        <a href="<?= BASE_URL ?>reportes/exportar?tipo=ventas&formato=pdf"
           class="btn-export btn-export--pdf" target="_blank">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg>
            Exportar PDF
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="rep-kpi-grid">
    <div class="rep-kpi rep-kpi--acento">
        <span class="rep-kpi-label">Ingresos totales</span>
        <span class="rep-kpi-valor">RD$ <?= number_format($totalVentas, 0) ?></span>
        <span class="rep-kpi-sub"><?= count($pedidos) ?> pedidos en total</span>
    </div>
    <div class="rep-kpi rep-kpi--verde">
        <span class="rep-kpi-label">Pedidos entregados</span>
        <span class="rep-kpi-valor"><?= $pedidosEntregados ?></span>
        <span class="rep-kpi-sub">
            <?= count($pedidos) > 0 ? round($pedidosEntregados / count($pedidos) * 100) : 0 ?>% del total
        </span>
    </div>
    <div class="rep-kpi rep-kpi--azul">
        <span class="rep-kpi-label">Ticket promedio</span>
        <span class="rep-kpi-valor">RD$ <?= number_format($ticketPromedio, 0) ?></span>
        <span class="rep-kpi-sub">Por pedido</span>
    </div>
    <div class="rep-kpi rep-kpi--rojo">
        <span class="rep-kpi-label">Pedidos cancelados</span>
        <span class="rep-kpi-valor"><?= $pedidosCancelados ?></span>
        <span class="rep-kpi-sub">
            <?= count($pedidos) > 0 ? round($pedidosCancelados / count($pedidos) * 100) : 0 ?>% del total
        </span>
    </div>
</div>

<!-- Gráfico + tabla vendedores -->
<div class="dos-cols">
    <!-- Gráfico de barras mensual -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Ingresos por mes</h2></div>
        <div class="chart-box"><canvas id="chartBarVentas"></canvas></div>
    </div>

    <!-- Top vendedores -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Top vendedores</h2></div>
        <?php if (empty($porVendedor)): ?>
            <div style="padding:30px;text-align:center;color:var(--texto-muted);font-size:.88rem">Sin datos disponibles</div>
        <?php else: ?>
        <div class="tabla-wrapper">
            <table class="tabla" style="font-size:.85rem">
                <thead>
                    <tr><th>#</th><th>Vendedor</th><th style="text-align:right">Monto</th><th style="text-align:center">Pedidos</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($porVendedor as $i => $v): ?>
                    <tr>
                        <td><span style="font-weight:800;color:var(--acento)"><?= $i+1 ?></span></td>
                        <td>
                            <div class="avatar-fila">
                                <div class="avatar-mini"><?= strtoupper(substr($v['vendedor'],0,1)) ?></div>
                                <?= htmlspecialchars($v['vendedor']) ?>
                            </div>
                        </td>
                        <td style="text-align:right"><strong>RD$ <?= number_format($v['montoTotal'],0) ?></strong></td>
                        <td style="text-align:center"><?= $v['totalPedidos'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tabla detallada -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-titulo">Todos los pedidos</h2>
        <span style="font-size:.82rem;color:var(--texto-muted)"><?= count($pedidos) ?> registros</span>
    </div>
    <div class="tabla-wrapper">
        <table class="tabla">
            <thead>
                <tr><th>Número</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Fecha</th></tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td><span class="codigo"><?= htmlspecialchars($p['numeroPedido']) ?></span></td>
                    <td><?= htmlspecialchars($p['cliente']) ?></td>
                    <td><strong>RD$ <?= number_format($p['total'],2) ?></strong></td>
                    <td><?php include __DIR__ . '/../partials/badge-estado.php'; ?></td>
                    <td class="texto-muted"><?= date('d/m/Y', strtotime($p['fechaPedido'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function(){
    const ctx = document.getElementById('chartBarVentas');
    if(!ctx) return;
    const d = <?= $chartBarJson ?>;
    new Chart(ctx,{
        type:'bar',
        data:{
            labels: d.labels,
            datasets:[{
                label:'Ingresos',
                data: d.data,
                backgroundColor:'rgba(232,93,38,.75)',
                borderColor:'#e85d26',
                borderWidth:0,
                borderRadius:6,
                hoverBackgroundColor:'#e85d26'
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{
                legend:{display:false},
                tooltip:{
                    backgroundColor:'#1e2a4a', titleColor:'#fff', bodyColor:'rgba(255,255,255,.85)',
                    padding:12, cornerRadius:10,
                    callbacks:{ label: c => ' RD$ '+Number(c.parsed.y).toLocaleString('es-DO',{minimumFractionDigits:2}) }
                }
            },
            scales:{
                x:{ grid:{display:false}, ticks:{font:{size:11},color:'#6b7494'} },
                y:{ grid:{color:'rgba(221,226,239,.5)',drawBorder:false},
                    ticks:{ font:{size:11},color:'#6b7494',
                        callback: v=>'RD$ '+Number(v).toLocaleString('es-DO') } }
            }
        }
    });
})();
</script>

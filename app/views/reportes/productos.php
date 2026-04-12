<?php

$destacados = $destacados ?? [];
$todos      = $todos      ?? [];

$totalProductos  = count($todos);
$stockTotal      = array_sum(array_column($todos, 'stock'));
$promedioRating  = count($todos) > 0
    ? round(array_sum(array_map(fn($p) => (float)($p['promedio'] ?? 0), $todos)) / count($todos), 1)
    : 0;
$sinStock = count(array_filter($todos, fn($p) => (int)$p['stock'] === 0));

// Categorías agrupadas
$porCategoria = [];
foreach ($todos as $p) {
    $cat = $p['categoria'] ?? 'Sin categoría';
    $porCategoria[$cat] = ($porCategoria[$cat] ?? 0) + 1;
}
arsort($porCategoria);
$topCats    = array_slice($porCategoria, 0, 6, true);
$chartDona  = json_encode([
    'labels' => array_keys($topCats),
    'data'   => array_values($topCats),
]);
?>

<style>
.rep-back { display:inline-flex;align-items:center;gap:6px;font-size:.85rem;font-weight:600;color:var(--texto-muted);text-decoration:none;margin-bottom:18px;transition:.15s; }
.rep-back:hover { color:var(--acento); }
.rep-kpi-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px; }
.rep-kpi { background:var(--superficie);border:1.5px solid var(--borde);border-radius:var(--radio);padding:20px 22px;display:flex;flex-direction:column;gap:6px;transition:.2s; }
.rep-kpi:hover { box-shadow:var(--sombra-md);transform:translateY(-2px); }
.rep-kpi-label { font-size:.78rem;font-weight:600;color:var(--texto-muted);text-transform:uppercase;letter-spacing:.04em; }
.rep-kpi-valor { font-size:1.55rem;font-weight:800;color:var(--texto); }
.rep-kpi-sub   { font-size:.78rem;color:var(--texto-muted); }
.rep-kpi--acento { border-top:3px solid var(--acento); }
.rep-kpi--verde  { border-top:3px solid var(--exito); }
.rep-kpi--azul   { border-top:3px solid var(--info); }
.rep-kpi--rojo   { border-top:3px solid var(--peligro); }

.dos-cols { display:grid;grid-template-columns:1fr 1.3fr;gap:20px; }
.chart-dona-wrap { height:230px;position:relative;display:flex;align-items:center;justify-content:center;padding:8px; }

.estrellas { color:#f59e0b;letter-spacing:-2px; }
.estrellas-vacias { color:#e2e8f0; }

.export-group { display:flex;gap:6px;align-items:center; }
.btn-export { display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:var(--radio-sm);font-size:.78rem;font-weight:700;border:1.5px solid;cursor:pointer;text-decoration:none;transition:.17s;background:var(--superficie);font-family:var(--fuente-cuerpo); }
.btn-export--excel { border-color:#16a34a;color:#16a34a; }
.btn-export--excel:hover { background:rgba(22,163,74,.08); }
.btn-export--pdf { border-color:var(--peligro);color:var(--peligro); }
.btn-export--pdf:hover { background:rgba(239,68,68,.08); }
</style>

<a href="<?= BASE_URL ?>reportes" class="rep-back">← Volver a Reportes</a>

<div class="page-header" style="margin-bottom:20px">
    <div>
        <h1 class="page-titulo">
             <img src="<?= BASE_URL ?>images/icons/pedido-icon.png" class="icon" alt="logo sistema"> Reporte de Productos
            </h1>
        <p class="page-sub">Desempeño, stock y calificaciones del catálogo</p>
    </div>
    <div class="export-group">
        <a href="<?= BASE_URL ?>reportes/exportar?tipo=productos&formato=excel"
           class="btn-export btn-export--excel">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            Exportar Excel
        </a>
        <a href="<?= BASE_URL ?>reportes/exportar?tipo=productos&formato=pdf"
           class="btn-export btn-export--pdf" target="_blank">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1z"/></svg>
            Exportar PDF
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="rep-kpi-grid">
    <div class="rep-kpi rep-kpi--acento">
        <span class="rep-kpi-label">Total productos</span>
        <span class="rep-kpi-valor"><?= $totalProductos ?></span>
        <span class="rep-kpi-sub">En catálogo</span>
    </div>
    <div class="rep-kpi rep-kpi--verde">
        <span class="rep-kpi-label">Stock total</span>
        <span class="rep-kpi-valor"><?= number_format($stockTotal) ?></span>
        <span class="rep-kpi-sub">Unidades disponibles</span>
    </div>
    <div class="rep-kpi rep-kpi--azul">
        <span class="rep-kpi-label">Rating promedio</span>
        <span class="rep-kpi-valor"><?= $promedioRating ?> ⭐</span>
        <span class="rep-kpi-sub">Sobre 5.0</span>
    </div>
    <div class="rep-kpi rep-kpi--rojo">
        <span class="rep-kpi-label">Sin stock</span>
        <span class="rep-kpi-valor"><?= $sinStock ?></span>
        <span class="rep-kpi-sub">Productos agotados</span>
    </div>
</div>

<div class="dos-cols">
    <!-- Gráfico de dona por categoría -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Productos por categoría</h2></div>
        <div class="chart-dona-wrap"><canvas id="chartDona"></canvas></div>
    </div>

    <!-- Top productos mejor valorados -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Mejor valorados</h2></div>
        <div class="tabla-wrapper">
            <table class="tabla" style="font-size:.85rem">
                <thead>
                    <tr><th>#</th><th>Producto</th><th>Categoría</th><th style="text-align:center">Rating</th><th style="text-align:center">Stock</th></tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($destacados, 0, 8) as $i => $p): ?>
                    <tr>
                        <td><strong style="color:var(--acento)"><?= $i+1 ?></strong></td>
                        <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><span class="badge badge--azul" style="font-size:.72rem"><?= htmlspecialchars($p['categoria']) ?></span></td>
                        <td style="text-align:center">
                            <span style="font-weight:700;color:#f59e0b"><?= number_format((float)($p['promedio'] ?? 0),1) ?></span>
                            <span style="color:var(--texto-muted);font-size:.75rem">/5</span>
                        </td>
                        <td style="text-align:center">
                            <?php if ((int)$p['stock'] === 0): ?>
                                <span class="badge badge--rojo" style="font-size:.72rem">Agotado</span>
                            <?php elseif ((int)$p['stock'] < 5): ?>
                                <span class="badge badge--amarillo" style="font-size:.72rem"><?= $p['stock'] ?></span>
                            <?php else: ?>
                                <span class="badge badge--verde" style="font-size:.72rem"><?= $p['stock'] ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tabla completa -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-titulo">Todos los productos</h2>
        <span style="font-size:.82rem;color:var(--texto-muted)"><?= $totalProductos ?> registros</span>
    </div>
    <div class="tabla-wrapper">
        <table class="tabla">
            <thead>
                <tr><th>Producto</th><th>Categoría</th><th style="text-align:right">Precio</th><th style="text-align:center">Stock</th><th style="text-align:center">Rating</th><th style="text-align:center">Reseñas</th></tr>
            </thead>
            <tbody>
                <?php foreach ($todos as $p): ?>
                <tr>
                    <td style="font-weight:600"><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><span class="badge badge--azul" style="font-size:.75rem"><?= htmlspecialchars($p['categoria']) ?></span></td>
                    <td style="text-align:right">RD$ <?= number_format($p['precio'],2) ?></td>
                    <td style="text-align:center">
                        <?php $s = (int)$p['stock']; ?>
                        <?php if ($s === 0): ?>
                            <span class="badge badge--rojo">Agotado</span>
                        <?php elseif ($s < 5): ?>
                            <span class="badge badge--amarillo"><?= $s ?></span>
                        <?php else: ?>
                            <?= $s ?>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center">
                        <?php $r = (float)($p['promedio'] ?? 0); ?>
                        <span style="font-weight:700;color:<?= $r >= 4 ? '#16a34a' : ($r >= 2.5 ? '#f59e0b' : '#ef4444') ?>"><?= number_format($r,1) ?></span>
                    </td>
                    <td style="text-align:center;color:var(--texto-muted)"><?= $p['totalResenas'] ?? 0 ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function(){
    const ctx = document.getElementById('chartDona');
    if(!ctx) return;
    const d = <?= $chartDona ?>;
    const cols = ['#e85d26','#3b82f6','#22c55e','#f59e0b','#a855f7','#06b6d4'];
    new Chart(ctx,{
        type:'doughnut',
        data:{ labels:d.labels, datasets:[{ data:d.data, backgroundColor:cols, borderWidth:0, hoverOffset:8 }] },
        options:{
            responsive:true, maintainAspectRatio:false, cutout:'62%',
            plugins:{
                legend:{ position:'right', labels:{ font:{size:12,family:"'Plus Jakarta Sans',sans-serif"}, color:'#1a1f2e', padding:14, boxWidth:12 } },
                tooltip:{ backgroundColor:'#1e2a4a', titleColor:'#fff', bodyColor:'rgba(255,255,255,.85)', padding:12, cornerRadius:10 }
            }
        }
    });
})();
</script>

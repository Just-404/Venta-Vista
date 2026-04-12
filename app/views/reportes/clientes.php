<?php


$clientes = $clientes ?? [];

$totalClientes  = count($clientes);
$activos        = count(array_filter($clientes, fn($c) => $c['activo']));
$inactivos      = $totalClientes - $activos;

// Agrupa por primer carácter del nombre para distribución alfabética visual
$porLetra = [];
foreach ($clientes as $c) {
    $letra = strtoupper(substr($c['nombre'], 0, 1));
    $porLetra[$letra] = ($porLetra[$letra] ?? 0) + 1;
}
ksort($porLetra);
$chartLetraJson = json_encode(['labels' => array_keys($porLetra), 'data' => array_values($porLetra)]);
?>

<style>
.rep-back { display:inline-flex;align-items:center;gap:6px;font-size:.85rem;font-weight:600;color:var(--texto-muted);text-decoration:none;margin-bottom:18px;transition:.15s; }
.rep-back:hover { color:var(--acento); }
.rep-kpi-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px; }
.rep-kpi { background:var(--superficie);border:1.5px solid var(--borde);border-radius:var(--radio);padding:20px 22px;display:flex;flex-direction:column;gap:6px;transition:.2s; }
.rep-kpi:hover { box-shadow:var(--sombra-md);transform:translateY(-2px); }
.rep-kpi-label { font-size:.78rem;font-weight:600;color:var(--texto-muted);text-transform:uppercase;letter-spacing:.04em; }
.rep-kpi-valor { font-size:1.55rem;font-weight:800;color:var(--texto); }
.rep-kpi-sub   { font-size:.78rem;color:var(--texto-muted); }
.rep-kpi--acento { border-top:3px solid var(--acento); }
.rep-kpi--verde  { border-top:3px solid var(--exito); }
.rep-kpi--rojo   { border-top:3px solid var(--peligro); }

/* Tarjetas de cliente */
.cli-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;padding:18px; }
.cli-card {
    background:var(--superficie2);
    border:1.5px solid var(--borde);
    border-radius:var(--radio);
    padding:16px 18px;
    display:flex;align-items:center;gap:14px;
    transition:.18s;
}
.cli-card:hover { box-shadow:var(--sombra);transform:translateY(-2px);border-color:var(--acento); }
.cli-avatar {
    width:44px;height:44px;border-radius:50%;
    background:linear-gradient(135deg,var(--primario),var(--acento));
    color:#fff;font-weight:800;font-size:1rem;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;
}
.cli-info { min-width:0; }
.cli-nombre { font-weight:700;font-size:.88rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.cli-email  { font-size:.75rem;color:var(--texto-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.cli-badge  { margin-top:4px; }

.dos-cols { display:grid;grid-template-columns:1fr 1fr;gap:20px; }
.chart-bar-wrap { height:200px;position:relative;padding:8px; }

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
            <img src="<?= BASE_URL ?>images/icons/people-icon.png" class="icon" alt="logo sistema"> 
            Reporte de Clientes</h1>
        <p class="page-sub">Análisis y estado de la base de clientes</p>
    </div>
    <div class="export-group">
        <a href="<?= BASE_URL ?>reportes/exportar?tipo=clientes&formato=excel"
           class="btn-export btn-export--excel">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            Exportar Excel
        </a>
        <a href="<?= BASE_URL ?>reportes/exportar?tipo=clientes&formato=pdf"
           class="btn-export btn-export--pdf" target="_blank">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5z"/></svg>
            Exportar PDF
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="rep-kpi-grid">
    <div class="rep-kpi rep-kpi--acento">
        <span class="rep-kpi-label">Total clientes</span>
        <span class="rep-kpi-valor"><?= $totalClientes ?></span>
        <span class="rep-kpi-sub">Registrados en el sistema</span>
    </div>
    <div class="rep-kpi rep-kpi--verde">
        <span class="rep-kpi-label">Clientes activos</span>
        <span class="rep-kpi-valor"><?= $activos ?></span>
        <span class="rep-kpi-sub">
            <?= $totalClientes > 0 ? round($activos / $totalClientes * 100) : 0 ?>% del total
        </span>
    </div>
    <div class="rep-kpi rep-kpi--rojo">
        <span class="rep-kpi-label">Clientes inactivos</span>
        <span class="rep-kpi-valor"><?= $inactivos ?></span>
        <span class="rep-kpi-sub">
            <?= $totalClientes > 0 ? round($inactivos / $totalClientes * 100) : 0 ?>% del total
        </span>
    </div>
</div>

<!-- Gráfica + tabla resumen -->
<div class="dos-cols" style="margin-bottom:20px">
    <!-- Distribución por letra inicial -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Distribución por inicial</h2></div>
        <div class="chart-bar-wrap"><canvas id="chartLetras"></canvas></div>
    </div>

    <!-- Estado activo/inactivo visual -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Estado de cuentas</h2></div>
        <div style="display:flex;flex-direction:column;gap:14px;padding:24px">
            <?php
            $items = [
                ['Activos',   $activos,   'barra--verde',  '#22c55e'],
                ['Inactivos', $inactivos, 'barra--roja',   '#ef4444'],
            ];
            foreach ($items as [$lbl, $val, $cls, $col]):
                $pct = $totalClientes > 0 ? round($val / $totalClientes * 100) : 0;
            ?>
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:.85rem">
                    <span style="font-weight:600"><?= $lbl ?></span>
                    <span style="color:<?= $col ?>;font-weight:700"><?= $val ?> (<?= $pct ?>%)</span>
                </div>
                <div class="barra-fondo" style="height:10px">
                    <div class="barra-progreso <?= $cls ?>" style="width:<?= $pct ?>%;height:10px;border-radius:99px"></div>
                </div>
            </div>
            <?php endforeach; ?>

            <div style="margin-top:8px;padding:14px;background:var(--superficie2);border-radius:var(--radio-sm);text-align:center">
                <div style="font-size:2rem;font-weight:800;color:var(--primario)"><?= $totalClientes ?></div>
                <div style="font-size:.82rem;color:var(--texto-muted);margin-top:2px">Total de clientes registrados</div>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de clientes -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-titulo">Directorio de clientes</h2>
        <span style="font-size:.82rem;color:var(--texto-muted)"><?= $totalClientes ?> clientes</span>
    </div>
    <?php if (empty($clientes)): ?>
        <div style="padding:40px;text-align:center;color:var(--texto-muted)">No hay clientes registrados</div>
    <?php else: ?>
    <div class="cli-grid">
        <?php foreach ($clientes as $c): ?>
        <div class="cli-card">
            <div class="cli-avatar"><?= strtoupper(substr($c['nombre'], 0, 1)) ?></div>
            <div class="cli-info">
                <div class="cli-nombre"><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']) ?></div>
                <div class="cli-email"><?= htmlspecialchars($c['emailUsuario'] ?? $c['email'] ?? '—') ?></div>
                <div class="cli-badge">
                    <?php if ($c['activo']): ?>
                        <span class="badge badge--verde" style="font-size:.7rem">Activo</span>
                    <?php else: ?>
                        <span class="badge badge--rojo" style="font-size:.7rem">Inactivo</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
(function(){
    const ctx = document.getElementById('chartLetras');
    if(!ctx) return;
    const d = <?= $chartLetraJson ?>;
    new Chart(ctx,{
        type:'bar',
        data:{ labels:d.labels, datasets:[{
            label:'Clientes', data:d.data,
            backgroundColor:'rgba(59,130,246,.7)', borderColor:'#3b82f6',
            borderWidth:0, borderRadius:5, hoverBackgroundColor:'#3b82f6'
        }]},
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{display:false}, tooltip:{ backgroundColor:'#1e2a4a', titleColor:'#fff', bodyColor:'rgba(255,255,255,.85)', padding:10, cornerRadius:8 } },
            scales:{
                x:{ grid:{display:false}, ticks:{font:{size:10},color:'#6b7494'} },
                y:{ grid:{color:'rgba(221,226,239,.5)',drawBorder:false}, ticks:{font:{size:10},color:'#6b7494',stepSize:1} }
            }
        }
    });
})();
</script>

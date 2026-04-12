<?php $clientes = $clientes ?? []; ?>

<!-- ── Estado ──────────────────────────────────────────────────── -->
<?php
$total    = count($clientes);
$activos  = count(array_filter($clientes, fn($c) => $c['activo']));
$inactivos = $total - $activos;
?>
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
    <div class="stat-card">
        <div class="stat-icono stat-icono--azul">👥</div>
        <div>
            <div class="stat-valor"><?= $total ?></div>
            <div class="stat-label">Total clientes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--verde">✅</div>
        <div>
            <div class="stat-valor"><?= $activos ?></div>
            <div class="stat-label">Activos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--naranja">⛔</div>
        <div>
            <div class="stat-valor"><?= $inactivos ?></div>
            <div class="stat-label">Inactivos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--morado">📦</div>
        <div>
            <div class="stat-valor"><?= $totalPedidos ?? '—' ?></div>
            <div class="stat-label">Con pedidos</div>
        </div>
    </div>
</div>

<!-- ── header ────────────────────────────────────────────── -->
<div class="page-header">
    <div>
        <h1 class="page-titulo">Clientes</h1>
        <p class="page-sub"><?= $total ?> clientes registrados</p>
    </div>
    <a href="<?= BASE_URL ?>clientes/crear" class="btn btn-primario">+ Nuevo cliente</a>
</div>

<!-- ── Panel de tabla ─────────────────────────────────────────── -->
<div class="panel">
    <div class="panel-header">
        <img src="<?= BASE_URL ?>images/icons/search-icon.png" class="icon" alt="busqueda">
        <input class="input-buscar" type="text" id="buscador" placeholder="Buscar cliente...">
        <select class="select-form" id="filtro-activo" style="width:auto" onchange="filtrar()">
            <option value="">Todos</option>
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
        </select>
    </div>

    <div class="tabla-wrapper">
        <table class="tabla" id="tabla-clientes">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                    <tr><td colspan="7" class="tabla-vacia">No hay clientes registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($clientes as $c): ?>
                    <tr data-activo="<?= $c['activo'] ? '1' : '0' ?>">
                        <td class="texto-muted"><?= $c['idCliente'] ?></td>
                        <td>
                            <div class="avatar-fila">
                                <div class="avatar-mini"><?= strtoupper(substr($c['nombre'], 0, 1)) ?></div>
                                <strong><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']) ?></strong>
                            </div>
                        </td>
                        <td class="texto-muted"><?= htmlspecialchars($c['cedula']) ?></td>
                        <td class="texto-muted"><?= htmlspecialchars($c['telefono'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td>
                            <span class="badge <?= $c['activo'] ? 'badge--verde' : 'badge--rojo' ?>">
                                <?= $c['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="acciones">
                            <a href="<?= BASE_URL ?>clientes/ver?id=<?= $c['idCliente'] ?>" class="btn-tabla">Ver</a>
                            <a href="<?= BASE_URL ?>clientes/editar?id=<?= $c['idCliente'] ?>" class="btn-tabla btn-tabla--editar">Editar</a>
                            <form method="POST" action="<?= BASE_URL ?>clientes/eliminar" style="display:inline"
                                  onsubmit="return confirm('¿Eliminar a <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']) ?>?')">
                                <input type="hidden" name="id" value="<?= $c['idCliente'] ?>">
                                <button class="btn-tabla btn-tabla--eliminar" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('buscador').addEventListener('input', filtrar);

function filtrar() {
    const q      = document.getElementById('buscador').value.toLowerCase();
    const activo = document.getElementById('filtro-activo').value;
    document.querySelectorAll('#tabla-clientes tbody tr').forEach(tr => {
        const textoOk  = tr.textContent.toLowerCase().includes(q);
        const activoOk = activo === '' || tr.dataset.activo === activo;
        tr.style.display = textoOk && activoOk ? '' : 'none';
    });
}
</script>

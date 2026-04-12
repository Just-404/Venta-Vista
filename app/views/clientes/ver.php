<?php
$cliente     = $cliente     ?? [];
$direcciones = $direcciones ?? [];
$pedidos     = $pedidos     ?? [];   // últimos pedidos del cliente
?>

<!-- ── Header ────────────────────────────────────────────── -->
<div class="page-header">
    <div>
        <h1 class="page-titulo">
            <?= htmlspecialchars(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellidos'] ?? '')) ?>
        </h1>
        <p class="page-sub">Perfil del cliente</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?= BASE_URL ?>clientes/editar?id=<?= $cliente['idCliente'] ?? '' ?>" class="btn btn-primario">Editar</a>
        <a href="<?= BASE_URL ?>clientes" class="btn btn-contorno">← Volver</a>
    </div>
</div>

<!-- ── Fila superior: datos + direcciones ────────────────────── -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">

    <!-- Datos personales -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-titulo">Datos personales</h2>
        </div>
        <div style="padding:20px">

            <!-- Avatar + nombre grande -->
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;
                        border-bottom:1px solid var(--borde)">
                <div style="width:56px;height:56px;border-radius:50%;background:var(--acento);
                            display:flex;align-items:center;justify-content:center;
                            color:white;font-size:1.3rem;font-weight:700;flex-shrink:0">
                    <?= strtoupper(substr($cliente['nombre'] ?? 'C', 0, 1)) ?>
                </div>
                <div>
                    <div style="font-weight:700;font-size:1.05rem">
                        <?= htmlspecialchars(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellidos'] ?? '')) ?>
                    </div>
                    <span class="badge <?= ($cliente['activo'] ?? 0) ? 'badge--verde' : 'badge--rojo' ?>"
                          style="margin-top:4px">
                        <?= ($cliente['activo'] ?? 0) ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Cédula</span>
                <span class="codigo" style="font-family:var(--fuente-cuerpo);font-size:.85rem">
                    <?= htmlspecialchars($cliente['cedula'] ?? '—') ?>
                </span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Teléfono</span>
                <span><?= htmlspecialchars($cliente['telefono'] ?? '—') ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Email</span>
                <span><?= htmlspecialchars($cliente['email'] ?? '—') ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Usuario</span>
                <span class="codigo"><?= htmlspecialchars($cliente['nombreUsuario'] ?? '—') ?></span>
            </div>
        </div>
    </div>

    <!-- Direcciones -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-titulo">Direcciones</h2>
        </div>
        <div style="padding:20px">
            <?php if (empty($direcciones)): ?>
                <p class="texto-muted">No hay direcciones registradas.</p>
            <?php else: ?>
                <?php foreach ($direcciones as $d): ?>
                <div class="direccion-item">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                        <strong style="font-size:.88rem"><?= htmlspecialchars($d['calle']) ?></strong>
                        <?php if ($d['esPrincipal']): ?>
                            <span class="badge badge--azul">Principal</span>
                        <?php endif; ?>
                    </div>
                    <div class="texto-muted">
                        <?= htmlspecialchars($d['ciudad']) ?>, <?= htmlspecialchars($d['provincia']) ?>
                        <?= !empty($d['codigoPostal']) ? '· CP ' . htmlspecialchars($d['codigoPostal']) : '' ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ── Últimos pedidos ────────────────────────────────────────── -->
<?php if (!empty($pedidos)): ?>
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-titulo">Últimos pedidos</h2>
        <a href="<?= BASE_URL ?>pedidos?cliente=<?= $cliente['idCliente'] ?? '' ?>"
           class="btn-tabla">Ver todos</a>
    </div>
    <div class="tabla-wrapper">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td><span class="codigo"><?= htmlspecialchars($p['numeroPedido']) ?></span></td>
                    <td class="texto-muted"><?= date('d/m/Y', strtotime($p['fechaPedido'])) ?></td>
                    <td><strong>RD$ <?= number_format($p['total'], 2) ?></strong></td>
                    <td><?php include __DIR__ . '/../partials/badge-estado.php'; ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>pedidos/ver?id=<?= $p['idPedido'] ?>" class="btn-tabla">Ver</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

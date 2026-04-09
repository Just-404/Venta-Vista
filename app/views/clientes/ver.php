<?php
// clientes/ver.php
$cliente     = $cliente     ?? [];
$direcciones = $direcciones ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo"><?= htmlspecialchars(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellidos'] ?? '')) ?></h1>
        <p class="page-sub">Perfil del cliente</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?= BASE_URL ?>clientes/editar?id=<?= $cliente['idCliente'] ?>" class="btn btn-primario">Editar</a>
        <a href="<?= BASE_URL ?>clientes" class="btn btn-contorno">← Volver</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Datos personales</h2></div>
        <div style="padding:20px">
            <div class="detalle-fila">
                <span class="detalle-label">Nombre completo</span>
                <span><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']) ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Cédula</span>
                <span><?= htmlspecialchars($cliente['cedula']) ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Teléfono</span>
                <span><?= htmlspecialchars($cliente['telefono'] ?? '—') ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Email</span>
                <span><?= htmlspecialchars($cliente['email']) ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Usuario</span>
                <span class="codigo"><?= htmlspecialchars($cliente['nombreUsuario'] ?? '—') ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Estado</span>
                <span class="badge <?= $cliente['activo'] ? 'badge--verde' : 'badge--rojo' ?>">
                    <?= $cliente['activo'] ? 'Activo' : 'Inactivo' ?>
                </span>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Direcciones</h2></div>
        <div style="padding:20px">
            <?php if (empty($direcciones)): ?>
                <p class="texto-muted">No hay direcciones registradas.</p>
            <?php else: ?>
                <?php foreach ($direcciones as $d): ?>
                <div class="direccion-item">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                        <strong><?= htmlspecialchars($d['calle']) ?></strong>
                        <?php if ($d['esPrincipal']): ?>
                            <span class="badge badge--azul">Principal</span>
                        <?php endif; ?>
                    </div>
                    <div class="texto-muted">
                        <?= htmlspecialchars($d['ciudad']) ?>, <?= htmlspecialchars($d['provincia']) ?>
                        <?= !empty($d['codigoPostal']) ? '· CP ' . $d['codigoPostal'] : '' ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
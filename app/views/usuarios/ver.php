<?php
$perfil = $perfil ?? [];
$p      = $perfil['perfil'] ?? null;
$idRol  = (int) ($perfil['idRol'] ?? 0);

$nombreRol = match($idRol) {
    1 => 'Administrador',
    2 => 'Vendedor',
    3 => 'Cliente',
    default => 'Desconocido'
};
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo"><?= htmlspecialchars($perfil['nombreUsuario'] ?? '') ?></h1>
        <p class="page-sub">Perfil de <?= $nombreRol ?></p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?= BASE_URL ?>usuarios" class="btn btn-contorno">← Volver</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <!-- Datos de acceso -->
    <div class="panel">
        <div class="panel-header"><h2 class="panel-titulo">Cuenta</h2></div>
        <div style="padding:20px">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
                <div class="avatar-mini" style="width:52px;height:52px;font-size:1.2rem">
                    <?= strtoupper(substr($perfil['nombreUsuario'] ?? '?', 0, 1)) ?>
                </div>
                <div>
                    <div style="font-weight:700;font-size:1rem"><?= htmlspecialchars($perfil['nombreUsuario']) ?></div>
                    <div class="texto-muted"><?= htmlspecialchars($perfil['email']) ?></div>
                </div>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Rol</span>
                <span class="badge <?= match($idRol) {
                    1 => 'badge--morado',
                    2 => 'badge--azul',
                    default => 'badge--gris'
                } ?>"><?= $nombreRol ?></span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Estado</span>
                <span class="badge <?= $perfil['activo'] ? 'badge--verde' : 'badge--rojo' ?>">
                    <?= $perfil['activo'] ? 'Activo' : 'Inactivo' ?>
                </span>
            </div>
            <div class="detalle-fila">
                <span class="detalle-label">Registro</span>
                <span class="texto-muted"><?= date('d/m/Y', strtotime($perfil['fechaRegistro'])) ?></span>
            </div>

            <!-- Cambiar contraseña -->
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--borde)">
                <p style="font-size:.82rem;font-weight:600;color:var(--texto-muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px">
                    Cambiar contraseña
                </p>
                <form method="POST" action="<?= BASE_URL ?>usuarios/password" style="display:flex;gap:8px">
                    <input type="hidden" name="id" value="<?= $perfil['idUsuario'] ?>">
                    <input class="input-form" type="password" name="password"
                           placeholder="Nueva contraseña" required style="flex:1">
                    <button class="btn btn-contorno btn-sm" type="submit">Cambiar</button>
                </form>
            </div>

            <!-- Activar / desactivar -->
            <form method="POST" action="<?= BASE_URL ?>usuarios/estado" style="margin-top:12px">
                <input type="hidden" name="id" value="<?= $perfil['idUsuario'] ?>">
                <input type="hidden" name="activo" value="<?= $perfil['activo'] ? 0 : 1 ?>">
                <button class="btn <?= $perfil['activo'] ? 'btn-peligro' : 'btn-exito' ?> btn-sm btn-completo"
                        type="submit">
                    <?= $perfil['activo'] ? 'Desactivar cuenta' : 'Activar cuenta' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Perfil según rol -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-titulo">
                <?= match($idRol) { 1 => 'Datos del administrador', 2 => 'Datos del vendedor', 3 => 'Datos del cliente', default => 'Perfil' } ?>
            </h2>
        </div>
        <div style="padding:20px">
            <?php if (!$p): ?>
                <p class="texto-muted">No se encontró perfil asociado a este usuario.</p>
            <?php else: ?>
                <?php if ($idRol === 1 || $idRol === 2): ?>
                    <div class="detalle-fila">
                        <span class="detalle-label">Nombre completo</span>
                        <span><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']) ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Cédula</span>
                        <span><?= htmlspecialchars($p['cedula']) ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Teléfono</span>
                        <span><?= htmlspecialchars($p['telefono'] ?? '—') ?></span>
                    </div>

                <?php elseif ($idRol === 3): ?>
                    <div class="detalle-fila">
                        <span class="detalle-label">Nombre completo</span>
                        <span><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']) ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Cédula</span>
                        <span><?= htmlspecialchars($p['cedula']) ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Teléfono</span>
                        <span><?= htmlspecialchars($p['telefono'] ?? '—') ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Email</span>
                        <span><?= htmlspecialchars($p['email']) ?></span>
                    </div>

                    <!-- Direcciones del cliente -->
                    <?php if (!empty($p['direcciones'])): ?>
                    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--borde)">
                        <p style="font-size:.82rem;font-weight:600;color:var(--texto-muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:12px">
                            Direcciones
                        </p>
                        <?php foreach ($p['direcciones'] as $d): ?>
                        <div class="direccion-item">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px">
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
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
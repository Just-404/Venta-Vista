<?php $usuarios = $usuarios ?? []; ?>

<!-- ── Estado ──────────────────────────────────────────────────── -->
<?php
$total    = count($usuarios);
$admins   = count(array_filter($usuarios, fn($u) => $u['idRol'] == 1));
$vendedores = count(array_filter($usuarios, fn($u) => $u['idRol'] == 2));
$clientes = count(array_filter($usuarios, fn($u) => $u['idRol'] == 3));
$activos  = count(array_filter($usuarios, fn($u) => $u['activo']));
?>

<div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
    <div class="stat-card">
        <div class="stat-icono stat-icono--naranja">👤</div>
        <div>
            <div class="stat-valor"><?= $total ?></div>
            <div class="stat-label">Total usuarios</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--morado">👤</div>
        <div>
            <div class="stat-valor"><?= $admins ?></div>
            <div class="stat-label">Administradores</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--azul">👤</div>
        <div>
            <div class="stat-valor"><?= $vendedores ?></div>
            <div class="stat-label">Vendedores</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icono stat-icono--verde">👤</div>
        <div>
            <div class="stat-valor"><?= $clientes ?></div>
            <div class="stat-label">Clientes</div>
        </div>
    </div>
</div>

<!-- ── Page header ────────────────────────────────────────────── -->
<div class="page-header">
    <div>
        <h1 class="page-titulo">Usuarios</h1>
        <p class="page-sub"><?= $total ?> usuarios registrados</p>
    </div>
    <a href="<?= BASE_URL ?>usuarios/crear" class="btn btn-primario">+ Nuevo usuario</a>
</div>

<!-- ── Panel de tabla ─────────────────────────────────────────── -->
<div class="panel">
    <div class="panel-header" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <img src="<?= BASE_URL ?>images/icons/search-icon.png" class="icon" alt="busqueda">
        <input class="input-buscar" type="text" id="buscador" placeholder="Buscar usuario..." onkeyup="filtrar()">

        <select class="select-form" id="filtro-rol" onchange="filtrar()" style="width:200px">
            <option value="">Todos los roles</option>
            <option value="Administrador">Administrador</option>
            <option value="Vendedor">Vendedor</option>
            <option value="Cliente">Cliente</option>
        </select>

        <select class="select-form" id="filtro-estado" onchange="filtrar()" style="width:200px">
            <option value="">Todos los estados</option>
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
        </select>
    </div>
    
    <div class="tabla-wrapper">
        <table class="tabla" id="tabla-usuarios">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Registro</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr><td colspan="7" class="tabla-vacia">No hay usuarios registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                    <?php
                        $badgeRol = match((int)$u['idRol']) {
                            1 => 'badge--morado',
                            2 => 'badge--azul',
                            default => 'badge--gris'
                        };
                        $colorAvatar = match((int)$u['idRol']) {
                            1 => 'background:var(--morado,#8b5cf6)',
                            2 => 'background:var(--info,#3b82f6)',
                            default => 'background:var(--exito,#22c55e)'
                        };
                    ?>
                    <tr data-rol="<?= htmlspecialchars($u['rol'] ?? '') ?>" data-estado="<?= $u['activo'] ? '1' : '0' ?>">
                        <td class="texto-muted"><?= $u['idUsuario'] ?></td>
                        <td>
                            <div class="avatar-fila">
                                <div class="avatar-mini" style="<?= $colorAvatar ?>">
                                    <?= strtoupper(substr($u['nombreUsuario'], 0, 1)) ?>
                                </div>
                                <strong><?= htmlspecialchars($u['nombreUsuario']) ?></strong>
                            </div>
                        </td>
                        <td class="texto-muted"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge <?= $badgeRol ?>">
                                <?= htmlspecialchars($u['rol'] ?? '') ?>
                            </span>
                        </td>
                        <td class="texto-muted"><?= date('d/m/Y', strtotime($u['fechaRegistro'])) ?></td>
                        <td>
                            <span class="badge <?= $u['activo'] ? 'badge--verde' : 'badge--rojo' ?>">
                                <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="acciones">
                            <a href="<?= BASE_URL ?>usuarios/ver?id=<?= $u['idUsuario'] ?>" class="btn-tabla">Ver</a>
                            <?php if ($u['idUsuario'] != 1): ?>
                                <form method="POST" action="<?= BASE_URL ?>usuarios/estado" style="display:inline">
                                    <input type="hidden" name="id"     value="<?= $u['idUsuario'] ?>">
                                    <input type="hidden" name="activo" value="<?= $u['activo'] ? 0 : 1 ?>">
                                    <button class="btn-tabla" type="submit">
                                        <?= $u['activo'] ? 'Desactivar' : 'Activar' ?>
                                    </button>
                                </form>
                                <form method="POST" action="<?= BASE_URL ?>usuarios/eliminar" style="display:inline"
                                      onsubmit="return confirm('¿Eliminar permanentemente a <?= htmlspecialchars($u['nombreUsuario']) ?>?')">
                                    <input type="hidden" name="id" value="<?= $u['idUsuario'] ?>">
                                    <button class="btn-tabla btn-tabla--eliminar" type="submit">Eliminar</button>
                                </form>
                            <?php endif; ?>
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
    const q = document.getElementById('buscador').value.toLowerCase();
    const rol = document.getElementById('filtro-rol').value;
    const estado = document.getElementById('filtro-estado').value;

    document.querySelectorAll('#tabla-usuarios tbody tr').forEach(tr => {
        const textoOk = tr.textContent.toLowerCase().includes(q);
        const rolOk = !rol || tr.dataset.rol === rol;
        const estadoOk = !estado || tr.dataset.estado === estado;
        tr.style.display = (textoOk && rolOk && estadoOk) ? '' : 'none';
    });
}
</script>
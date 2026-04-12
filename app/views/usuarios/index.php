<?php $usuarios = $usuarios ?? []; ?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Usuarios</h1>
        <p class="page-sub"><?= count($usuarios) ?> usuarios registrados</p>
    </div>
    <a href="<?= BASE_URL ?>usuarios/crear" class="btn btn-primario">+ Nuevo usuario</a>
</div>

<div class="panel">
    <div class="panel-header">
        <img src="<?= BASE_URL ?>images/icons/search-icon.png" class="icon" alt="logo sistema">
        <input class="input-buscar" type="text" id="buscador" placeholder="Buscar usuario...">
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
                    <tr>
                        <td class="texto-muted"><?= $u['idUsuario'] ?></td>
                        <td>
                            <div class="avatar-fila">
                                <div class="avatar-mini"><?= strtoupper(substr($u['nombreUsuario'], 0, 1)) ?></div>
                                <strong><?= htmlspecialchars($u['nombreUsuario']) ?></strong>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge <?= match($u['idRol']) { 1 => 'badge--morado', 2 => 'badge--azul', default => 'badge--gris' } ?>">
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
                            <form method="POST" action="<?= BASE_URL ?>usuarios/estado" style="display:inline">
                                <input type="hidden" name="id" value="<?= $u['idUsuario'] ?>">
                                <input type="hidden" name="activo" value="<?= $u['activo'] ? 0 : 1 ?>">
                                <button class="btn-tabla" type="submit">
                                    <?= $u['activo'] ? 'Desactivar' : 'Activar' ?>
                                </button>
                            </form>
                            <form method="POST" action="<?= BASE_URL ?>usuarios/eliminar" style="display:inline"
                                  onsubmit="return confirm('¿Eliminar este usuario permanentemente?')">
                                <input type="hidden" name="id" value="<?= $u['idUsuario'] ?>">
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
document.getElementById('buscador').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tabla-usuarios tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
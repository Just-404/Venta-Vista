<?php $clientes = $clientes ?? []; ?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Clientes</h1>
        <p class="page-sub"><?= count($clientes) ?> clientes registrados</p>
    </div>
    <a href="<?= BASE_URL ?>clientes/crear" class="btn btn-primario">+ Nuevo cliente</a>
</div>

<div class="panel">
    <div class="panel-header">
        <input class="input-buscar" type="text" id="buscador" placeholder="🔍 Buscar cliente...">
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
                    <tr>
                        <td class="texto-muted"><?= $c['idCliente'] ?></td>
                        <td>
                            <div class="avatar-fila">
                                <div class="avatar-mini"><?= strtoupper(substr($c['nombre'], 0, 1)) ?></div>
                                <span><strong><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']) ?></strong></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($c['cedula']) ?></td>
                        <td><?= htmlspecialchars($c['telefono'] ?? '—') ?></td>
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
                                  onsubmit="return confirm('¿Eliminar este cliente?')">
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
document.getElementById('buscador').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tabla-clientes tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
<?php $pedidos = $pedidos ?? []; ?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Pedidos</h1>
        <p class="page-sub"><?= count($pedidos) ?> pedidos en total</p>
    </div>
    <a href="<?= BASE_URL ?>pedidos/crear" class="btn btn-primario">+ Nuevo pedido</a>
</div>

<div class="panel">
    <div class="panel-header">
        <img src="<?= BASE_URL ?>images/icons/search-icon.png" class="icon" alt="logo sistema">
        <input class="input-buscar" type="text" id="buscador" placeholder="Buscar pedido...">
        <select class="select-form" id="filtro-estado" style="width:auto">
            <option value="">Todos los estados</option>
            <option>Pendiente</option>
            <option>Confirmado</option>
            <option>En_proceso</option>
            <option>Enviado</option>
            <option>Entregado</option>
            <option>Cancelado</option>
            <option>Devuelto</option>
        </select>
    </div>
    <div class="tabla-wrapper">
        <table class="tabla" id="tabla-pedidos">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Subtotal</th>
                    <th>Descuento</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pedidos)): ?>
                    <tr><td colspan="8" class="tabla-vacia">No hay pedidos registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($pedidos as $p): ?>
                    <tr data-estado="<?= $p['estado'] ?>">
                        <td><span class="codigo"><?= htmlspecialchars($p['numeroPedido']) ?></span></td>
                        <td><?= htmlspecialchars($p['cliente']) ?></td>
                        <td>RD$ <?= number_format($p['subtotal'], 2) ?></td>
                        <td><?= $p['descuento'] > 0 ? 'RD$ ' . number_format($p['descuento'], 2) : '—' ?></td>
                        <td><strong>RD$ <?= number_format($p['total'], 2) ?></strong></td>
                        <td><?php include __DIR__ . '/../partials/badge-estado.php'; ?></td>
                        <td class="texto-muted"><?= date('d/m/Y', strtotime($p['fechaPedido'])) ?></td>
                        <td class="acciones">
                            <a href="<?= BASE_URL ?>pedidos/ver?id=<?= $p['idPedido'] ?>" class="btn-tabla">Ver</a>
                            <form method="POST" action="<?= BASE_URL ?>pedidos/eliminar" style="display:inline"
                                  onsubmit="return confirm('¿Eliminar este pedido?')">
                                <input type="hidden" name="id" value="<?= $p['idPedido'] ?>">
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
document.getElementById('filtro-estado').addEventListener('change', filtrar);

function filtrar() {
    const q      = document.getElementById('buscador').value.toLowerCase();
    const estado = document.getElementById('filtro-estado').value;
    document.querySelectorAll('#tabla-pedidos tbody tr').forEach(tr => {
        const textoOk  = tr.textContent.toLowerCase().includes(q);
        const estadoOk = !estado || tr.dataset.estado === estado;
        tr.style.display = textoOk && estadoOk ? '' : 'none';
    });
}
</script>
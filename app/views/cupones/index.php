<?php $cupones = $cupones ?? []; ?>

<!-- ── Mensajes de sesión ── -->
<?php if (!empty($_SESSION['advertencia'])): ?>
    <div class="alerta alerta--advertencia">
        ⚠️ <?= $_SESSION['advertencia'] ?>
    </div>
    <?php unset($_SESSION['advertencia']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['exito'])): ?>
    <div class="alerta alerta--exito">
        ✅ <?= htmlspecialchars($_SESSION['exito']) ?>
    </div>
    <?php unset($_SESSION['exito']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alerta alerta--error">
        ❌ <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Cupones</h1>
        <p class="page-sub"><?= count($cupones) ?> cupones registrados</p>
    </div>
    <a href="<?= BASE_URL ?>cupones/crear" class="btn btn-primario">+ Nuevo cupón</a>
</div>

<div class="panel">
    <div class="tabla-wrapper">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Descuento</th>
                    <th>Usos</th>
                    <th>Vigencia</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cupones)): ?>
                    <tr><td colspan="7" class="tabla-vacia">No hay cupones registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($cupones as $c):
                        // ── Determinar si el cupón ya venció ──
                        $vencido = strtotime($c['fechaVencimiento']) < strtotime('today');
                    ?>
                    <tr>
                        <td><span class="codigo"><?= htmlspecialchars($c['codigo']) ?></span></td>
                        <td><?= htmlspecialchars(str_replace('_', ' ', $c['tipo'])) ?></td>
                        <td>
                            <?php if ($c['tipo'] === 'Monto_fijo'): ?>
                                RD$ <?= number_format($c['descuento'], 2) ?>
                            <?php elseif ($c['tipo'] === 'envio_gratis'): ?>
                                Envío gratis
                            <?php else: ?>
                                <?= $c['descuento'] ?>%
                            <?php endif; ?>
                        </td>
                        <td><?= $c['usosActuales'] ?> / <?= $c['usoMaximo'] ?></td>
                        <td class="texto-muted">
                            <?= date('d/m/Y', strtotime($c['fechaInicio'])) ?> —
                            <?= date('d/m/Y', strtotime($c['fechaVencimiento'])) ?>
                            <?php if ($vencido): ?>
                                <span class="badge badge--gris" title="Este cupón ya venció">Vencido</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $c['activo'] ? 'badge--verde' : 'badge--rojo' ?>">
                                <?= $c['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="acciones">
                            <a href="<?= BASE_URL ?>cupones/editar?id=<?= $c['idCupon'] ?>"
                               class="btn-tabla btn-tabla--editar">Editar</a>

                            <?php if ($vencido): ?>
                                <!-- Cupón vencido: eliminación permitida -->
                                <form method="POST" action="<?= BASE_URL ?>cupones/eliminar"
                                      style="display:inline"
                                      onsubmit="return confirmarEliminar(this)">
                                    <input type="hidden" name="id" value="<?= $c['idCupon'] ?>">
                                    <input type="hidden" name="codigo" value="<?= htmlspecialchars($c['codigo']) ?>">
                                    <button class="btn-tabla btn-tabla--eliminar" type="submit">
                                        Eliminar
                                    </button>
                                </form>
                            <?php else: ?>
                                <!-- Cupón vigente: botón deshabilitado con aviso -->
                                <button class="btn-tabla btn-tabla--eliminar btn-tabla--deshabilitado"
                                        type="button"
                                        disabled
                                        title="No se puede eliminar: el cupón aún está vigente (vence el <?= date('d/m/Y', strtotime($c['fechaVencimiento'])) ?>)">
                                    Eliminar
                                </button>
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
function confirmarEliminar(form) {
    const codigo = form.querySelector('[name="codigo"]').value;
    return confirm('¿Eliminar el cupón "' + codigo + '"?\nEsta acción no se puede deshacer.');
}
</script>
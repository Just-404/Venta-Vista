<?php $cupones = $cupones ?? []; ?>

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
                    <?php foreach ($cupones as $c): ?>
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
                        </td>
                        <td>
                            <span class="badge <?= $c['activo'] ? 'badge--verde' : 'badge--rojo' ?>">
                                <?= $c['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="acciones">
                            <a href="<?= BASE_URL ?>cupones/editar?id=<?= $c['idCupon'] ?>" class="btn-tabla btn-tabla--editar">Editar</a>
                            <form method="POST" action="<?= BASE_URL ?>cupones/eliminar" style="display:inline"
                                  onsubmit="return confirm('¿Eliminar este cupón?')">
                                <input type="hidden" name="id" value="<?= $c['idCupon'] ?>">
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
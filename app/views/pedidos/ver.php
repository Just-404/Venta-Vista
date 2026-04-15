<?php
$pedido  = $pedido  ?? [];
$detalle = $detalle ?? [];
$pago    = $pago    ?? null;
$envio   = $envio   ?? null;

$estados       = ['Pendiente','Confirmado','En_proceso','Enviado','Entregado','Cancelado','Devuelto'];
$estadoActual  = $pedido['estado'] ?? '';
$pasosCancelado = in_array($estadoActual, ['Cancelado','Devuelto']);

// Orden del flujo normal (sin cancelado/devuelto)
$flujo = ['Pendiente','Confirmado','En_proceso','Enviado','Entregado'];
$idxActual = array_search($estadoActual, $flujo);

$rol = $usuario['rol'] ?? 0;
?>
<!-- ── header ────────────────────────────────────────────── -->
<div class="page-header">
    <div>
        <h1 class="page-titulo"><?= htmlspecialchars($pedido['numeroPedido'] ?? '') ?></h1>
        <p class="page-sub">Detalle del pedido</p>
    </div>
    <a href="<?= BASE_URL ?>pedidos" class="btn btn-contorno">← Volver</a>
</div>

<!-- ── Barra de progreso de estado ───────────────────────────── -->
<?php if (!$pasosCancelado): ?>
<div class="panel" style="margin-bottom:20px">
    <div style="padding:16px 20px">
        <div style="display:flex;align-items:stretch">
            <?php foreach ($flujo as $i => $paso): ?>
            <?php
                $done    = $idxActual !== false && $i < $idxActual;
                $current = $idxActual !== false && $i === $idxActual;
                $color   = $done ? 'var(--exito)' : ($current ? 'var(--acento)' : 'var(--borde)');
                $txtColor= $done ? 'var(--exito)' : ($current ? 'var(--acento)' : 'var(--texto-muted)');
            ?>
            <div style="flex:1;text-align:center;position:relative">
                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;
                            color:<?= $txtColor ?>;padding-bottom:8px">
                    <?= str_replace('_', ' ', $paso) ?>
                </div>
                <div style="height:3px;background:<?= $color ?>;border-radius:99px;transition:.3s"></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div style="margin-bottom:20px">
    <span class="badge badge--rojo" style="font-size:.9rem;padding:6px 16px">
        ⚠ Pedido <?= str_replace('_', ' ', $estadoActual) ?>
    </span>
</div>
<?php endif; ?>

<!-- ── Contenido en dos columnas ─────────────────────────────── -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start">

    <!-- Columna izquierda -->
    <div>

        <!-- Productos -->
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Productos del pedido</h2></div>
            <div class="tabla-wrapper">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio unit.</th>
                            <th style="text-align:center">Cant.</th>
                            <th style="text-align:right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($detalle)): ?>
                            <tr><td colspan="4" class="tabla-vacia">Sin productos registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($detalle as $d): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($d['producto']) ?></strong></td>
                                <td class="texto-muted">RD$ <?= number_format($d['precioUnitario'], 2) ?></td>
                                <td style="text-align:center"><?= $d['cantidad'] ?></td>
                                <td style="text-align:right">RD$ <?= number_format($d['subtotal'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right;padding:12px 16px;color:var(--texto-muted);font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Subtotal</td>
                            <td style="padding:12px 16px;text-align:right">RD$ <?= number_format($pedido['subtotal'] ?? 0, 2) ?></td>
                        </tr>
                        <?php if (($pedido['descuento'] ?? 0) > 0): ?>
                        <tr>
                            <td colspan="3" style="text-align:right;padding:4px 16px;color:var(--texto-muted);font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Descuento</td>
                            <td style="padding:4px 16px;text-align:right;color:var(--exito)">
                                − RD$ <?= number_format($pedido['descuento'], 2) ?>
                                <?php if (!empty($pedido['cupon'])): ?>
                                    <span class="codigo" style="margin-left:6px"><?= htmlspecialchars($pedido['cupon']) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr style="font-size:1rem;font-weight:700">
                            <td colspan="3" style="text-align:right;padding:12px 16px">Total</td>
                            <td style="padding:12px 16px;text-align:right;color:var(--acento)">
                                RD$ <?= number_format($pedido['total'] ?? 0, 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Pago -->
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Información de pago</h2></div>
            <div style="padding:20px">
                <?php if ($pago): ?>
                    <div class="detalle-fila">
                        <span class="detalle-label">Método</span>
                        <span><?= htmlspecialchars(str_replace('_', ' ', $pago['metodoPago'])) ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Monto</span>
                        <span><strong>RD$ <?= number_format($pago['monto'], 2) ?></strong></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Referencia</span>
                        <span class="codigo"><?= htmlspecialchars($pago['referencia'] ?? '—') ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Estado</span>
                        <?php $p = $pago; include __DIR__ . '/../partials/badge-estado.php'; ?>
                    </div>
                <?php else: ?>
                    <p class="texto-muted" style="margin-bottom:16px">No se ha registrado un pago para este pedido.</p>
                    <form method="POST" action="<?= BASE_URL ?>pagos/crear" style="display:block">
                        <input type="hidden" name="idPedido" value="<?= $pedido['idPedido'] ?? '' ?>">
                        <input type="hidden" name="monto"    value="<?= $pedido['total']    ?? 0 ?>">
                        <div class="grid-form">
                            <div class="grupo-form">
                                <label class="etiqueta-form">Método de pago</label>
                                <select class="select-form" name="metodoPago">
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta_Credito">Tarjeta de crédito</option>
                                    <option value="Tarjeta_Debito">Tarjeta de débito</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                            </div>
                            <div class="grupo-form">
                                <label class="etiqueta-form">Referencia (opcional)</label>
                                <input class="input-form" type="text" name="referencia" placeholder="TXN-000">
                            </div>
                        </div>
                        <div style="margin-top:4px">
                            <button class="btn btn-primario btn-sm" type="submit">Registrar pago</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Envío -->
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Información de envío</h2></div>
            <div style="padding:20px">
                <?php if ($envio): ?>
                    <div class="detalle-fila">
                        <span class="detalle-label">Empresa</span>
                        <span><?= htmlspecialchars($envio['empresa'] ?? '—') ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Código rastreo</span>
                        <span class="codigo"><?= htmlspecialchars($envio['codigoRastreo'] ?? '—') ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Fecha estimada</span>
                        <span><?= !empty($envio['fechaEstimada'])
                            ? date('d/m/Y', strtotime($envio['fechaEstimada'])) : '—' ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Estado</span>
                        <?php $p = $envio; include __DIR__ . '/../partials/badge-estado.php'; ?>
                    </div>
                    <?php if (($envio['estado'] ?? '') !== 'Entregado'): ?>
                    <form method="POST" action="<?= BASE_URL ?>envios/entregar" style="display:block;margin-top:14px">
                        <input type="hidden" name="id" value="<?= $envio['idEnvio'] ?? '' ?>">
                        <button class="btn btn-exito btn-sm" type="submit">✓ Marcar como entregado</button>
                    </form>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="texto-muted">No se ha registrado un envío para este pedido.</p>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /col izquierda -->

    <!-- Columna derecha -->
    <div>

        <!-- Resumen -->
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Resumen</h2></div>
            <div style="padding:20px">
                <div class="detalle-fila">
                    <span class="detalle-label">Cliente</span>
                    <span><?= htmlspecialchars($pedido['cliente'] ?? '—') ?></span>
                </div>
                <div class="detalle-fila">
                    <span class="detalle-label">Fecha</span>
                    <span class="texto-muted">
                        <?= !empty($pedido['fechaPedido'])
                            ? date('d/m/Y H:i', strtotime($pedido['fechaPedido'])) : '—' ?>
                    </span>
                </div>
                <?php if (!empty($pedido['cupon'])): ?>
                <div class="detalle-fila">
                    <span class="detalle-label">Cupón</span>
                    <span class="codigo"><?= htmlspecialchars($pedido['cupon']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($pedido['notas'])): ?>
                <div class="detalle-fila">
                    <span class="detalle-label">Notas</span>
                    <span class="texto-muted" style="font-size:.82rem"><?= htmlspecialchars($pedido['notas']) ?></span>
                </div>
                <?php endif; ?>
                <div style="margin-top:14px">
                    <?php $p = $pedido; include __DIR__ . '/../partials/badge-estado.php'; ?>
                </div>
            </div>
        </div>

        <!-- Cambiar estado -->
         <?php if($rol != 3): ?>
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Cambiar estado</h2></div>
            <div style="padding:20px">
                <form method="POST" action="<?= BASE_URL ?>pedidos/estado" style="display:block">
                    <input type="hidden" name="id" value="<?= $pedido['idPedido'] ?? '' ?>">
                    <div class="grupo-form">
                        <label>Nuevo estado</label>
                        <select class="select-form" name="estado">
                            <?php foreach ($estados as $est): ?>
                                <?php if($rol == 1 && $pedido['estado'] != 'Pendiente' && in_array($est, ['Cancelado','Devuelto'])) continue; ?>
                                <option value="<?= $est ?>" <?= ($pedido['estado'] ?? '') === $est ? 'selected' : '' ?>>
                                    <?= str_replace('_', ' ', $est) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primario btn-completo" type="submit">Actualizar estado</button>
                </form>
            </div>
        </div>
        <?php endif?>
        <!-- Historial rápido -->
        <?php if (!empty($historial)): ?>
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Historial</h2></div>
            <div style="padding:20px">
                <?php foreach ($historial as $i => $h): ?>
                <div style="display:flex;gap:12px;padding-bottom:<?= $i < count($historial)-1 ? '14px' : '0' ?>;
                            position:relative">
                    <div style="width:9px;height:9px;border-radius:50%;
                                background:<?= $i === 0 ? 'var(--acento)' : 'var(--exito)' ?>;
                                flex-shrink:0;margin-top:4px;z-index:1"></div>
                    <?php if ($i < count($historial) - 1): ?>
                    <div style="position:absolute;left:4px;top:13px;bottom:0;width:1px;background:var(--borde)"></div>
                    <?php endif; ?>
                    <div>
                        <div style="font-size:.8rem;font-weight:600">
                            <?= htmlspecialchars(str_replace('_',' ', $h['estado'])) ?>
                        </div>
                        <div class="texto-muted" style="font-size:.72rem">
                            <?= date('d/m/Y H:i', strtotime($h['fecha'])) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /col derecha -->

</div>

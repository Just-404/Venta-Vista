<?php
$pedido  = $pedido  ?? [];
$detalle = $detalle ?? [];
$pago    = $pago    ?? null;
$envio   = $envio   ?? null;

$estados = ['Pendiente','Confirmado','En_proceso','Enviado','Entregado','Cancelado','Devuelto'];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo"><?= htmlspecialchars($pedido['numeroPedido'] ?? '') ?></h1>
        <p class="page-sub">Detalle del pedido</p>
    </div>
    <a href="<?= BASE_URL ?>pedidos" class="btn btn-contorno">← Volver</a>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

    <!-- Columna izquierda -->
    <div>

        <!-- Detalle de productos -->
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Productos</h2></div>
            <div class="tabla-wrapper">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio unit.</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalle as $d): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($d['producto']) ?></strong></td>
                            <td>RD$ <?= number_format($d['precioUnitario'], 2) ?></td>
                            <td><?= $d['cantidad'] ?></td>
                            <td>RD$ <?= number_format($d['subtotal'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right;padding:12px 16px;color:var(--texto-muted)">Subtotal</td>
                            <td style="padding:12px 16px">RD$ <?= number_format($pedido['subtotal'], 2) ?></td>
                        </tr>
                        <?php if ($pedido['descuento'] > 0): ?>
                        <tr>
                            <td colspan="3" style="text-align:right;padding:4px 16px;color:var(--texto-muted)">Descuento</td>
                            <td style="padding:4px 16px;color:var(--exito)">− RD$ <?= number_format($pedido['descuento'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr style="font-size:1rem;font-weight:700">
                            <td colspan="3" style="text-align:right;padding:12px 16px">Total</td>
                            <td style="padding:12px 16px;color:var(--acento)">RD$ <?= number_format($pedido['total'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Pago -->
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Pago</h2></div>
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
                    <p class="texto-muted">No se ha registrado un pago para este pedido.</p>
                    <form method="POST" action="<?= BASE_URL ?>pagos/crear" style="margin-top:16px">
                        <input type="hidden" name="idPedido" value="<?= $pedido['idPedido'] ?>">
                        <input type="hidden" name="monto" value="<?= $pedido['total'] ?>">
                        <div class="grid-form">
                            <div class="grupo-form">
                                <label class="etiqueta-form">Método de pago</label>
                                <select class="select-form" name="metodoPago">
                                    <option>Efectivo</option>
                                    <option>Tarjeta_Credito</option>
                                    <option>Tarjeta_Debito</option>
                                    <option>Transferencia</option>
                                </select>
                            </div>
                            <div class="grupo-form">
                                <label class="etiqueta-form">Referencia</label>
                                <input class="input-form" type="text" name="referencia" placeholder="TXN-000">
                            </div>
                        </div>
                        <button class="btn btn-primario btn-sm" type="submit">Registrar pago</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Envío -->
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Envío</h2></div>
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
                        <span><?= $envio['fechaEstimada'] ? date('d/m/Y', strtotime($envio['fechaEstimada'])) : '—' ?></span>
                    </div>
                    <div class="detalle-fila">
                        <span class="detalle-label">Estado</span>
                        <?php $p = $envio; include __DIR__ . '/../partials/badge-estado.php'; ?>
                    </div>
                    <?php if ($envio['estado'] !== 'Entregado'): ?>
                    <form method="POST" action="<?= BASE_URL ?>envios/entregar" style="margin-top:12px">
                        <input type="hidden" name="id" value="<?= $envio['idEnvio'] ?>">
                        <button class="btn btn-exito btn-sm" type="submit">Marcar como entregado</button>
                    </form>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="texto-muted">No se ha registrado un envío para este pedido.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

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
                    <span class="texto-muted"><?= date('d/m/Y H:i', strtotime($pedido['fechaPedido'])) ?></span>
                </div>
                <div class="detalle-fila">
                    <span class="detalle-label">Cupón</span>
                    <span><?= !empty($pedido['cupon']) ? '<span class="codigo">' . htmlspecialchars($pedido['cupon']) . '</span>' : '—' ?></span>
                </div>
                <?php if (!empty($pedido['notas'])): ?>
                <div class="detalle-fila">
                    <span class="detalle-label">Notas</span>
                    <span class="texto-muted"><?= htmlspecialchars($pedido['notas']) ?></span>
                </div>
                <?php endif; ?>
                <div style="margin-top:16px">
                    <?php $p = $pedido; include __DIR__ . '/../partials/badge-estado.php'; ?>
                </div>
            </div>
        </div>

        <!-- Cambiar estado -->
        <div class="panel">
            <div class="panel-header"><h2 class="panel-titulo">Cambiar estado</h2></div>
            <div style="padding:20px">
                <form method="POST" action="<?= BASE_URL ?>pedidos/estado">
                    <input type="hidden" name="id" value="<?= $pedido['idPedido'] ?>">
                    <div class="grupo-form">
                        <label class="etiqueta-form">Nuevo estado</label>
                        <select class="select-form" name="estado">
                            <?php foreach ($estados as $est): ?>
                                <option value="<?= $est ?>" <?= $pedido['estado'] === $est ? 'selected' : '' ?>>
                                    <?= str_replace('_', ' ', $est) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primario btn-completo" type="submit">Actualizar estado</button>
                </form>
            </div>
        </div>

    </div>

</div>
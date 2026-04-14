<?php
$pedido = $pedido ?? [];
$items  = $items ?? [];
$total  = $total ?? 0;
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Pago del Pedido</h1>
        <p class="page-sub">
            Pedido #<?= htmlspecialchars($pedido['numeroPedido'] ?? '') ?>
        </p>
    </div>

    <a href="<?= BASE_URL ?>carrito" class="btn btn-contorno">
        ← Volver al carrito
    </a>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

    <!-- Métodos de Pago -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-titulo">Método de Pago</h2>
        </div>

        <form method="POST" action="<?= BASE_URL ?>pagos/crear">
            
            <input type="hidden" name="idPedido" 
                value="<?= $pedido['idPedido'] ?>">

            <input type="hidden" name="monto" 
                value="<?= $total ?>">

            <div style="padding:20px">

                <!-- Tarjeta -->
                <div class="metodo-pago">
                    <label class="radio-pago">
                        <input type="radio" 
                               name="metodoPago" 
                               value="Tarjeta_Credito" 
                               checked>

                        <span>
                            Tarjeta de Crédito
                        </span>
                    </label>

                    <div class="grupo-form">
                        <label class="etiqueta-form">Número de tarjeta</label>
                        <input class="input-form"
                               type="text"
                               placeholder="0000 0000 0000 0000">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div class="grupo-form">
                            <label class="etiqueta-form">Expiración</label>
                            <input class="input-form"
                                   type="text"
                                   placeholder="MM/AA">
                        </div>

                        <div class="grupo-form">
                            <label class="etiqueta-form">CVV</label>
                            <input class="input-form"
                                   type="text"
                                   placeholder="123">
                        </div>
                    </div>
                </div>

                <div class="separador-seccion"></div>

                <!-- Transferencia -->
                <div class="metodo-pago">
                    <label class="radio-pago">
                        <input type="radio" 
                               name="metodoPago" 
                               value="Transferencia">

                        <span>
                            Transferencia Bancaria
                        </span>
                    </label>

                    <div class="alert-stock">
                        <span>ℹ️</span>
                        <span>
                            Banco Popular<br>
                            Cuenta: 123456789<br>
                            Nombre: VentaVista
                        </span>
                    </div>

                    <div class="grupo-form">
                        <label class="etiqueta-form">Referencia</label>
                        <input class="input-form"
                               name="referencia"
                               placeholder="Número de transferencia">
                    </div>
                </div>

                <div class="separador-seccion"></div>

                <!-- Efectivo -->
                <div class="metodo-pago">
                    <label class="radio-pago">
                        <input type="radio" 
                               name="metodoPago" 
                               value="Efectivo">

                        <span>
                            Pago contra entrega
                        </span>
                    </label>

                    <p class="texto-muted" style="margin-top:6px">
                        Pagarás cuando recibas tu pedido.
                    </p>
                </div>

                <button class="btn btn-primario btn-completo"
                        style="margin-top:20px"
                        type="submit">
                    Confirmar Pago
                </button>

            </div>
        </form>
    </div>


    <!-- Resumen -->
    <div>
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-titulo">Resumen del Pedido</h2>
            </div>

            <div style="padding:20px">

                <?php foreach ($items as $item): ?>
                    <div class="detalle-fila">
                        <span>
                            <?= htmlspecialchars($item['producto']) ?>
                            (x<?= $item['cantidad'] ?>)
                        </span>
                        <span>
                            RD$ <?= number_format($item['subtotal'], 2) ?>
                        </span>
                    </div>
                <?php endforeach; ?>

                <div class="separador-seccion"></div>

                <div class="detalle-fila">
                    <span class="detalle-label">
                        Subtotal
                    </span>
                    <span>
                        RD$ <?= number_format($total, 2) ?>
                    </span>
                </div>

                <div class="detalle-fila"
                     style="font-size:1.1rem;
                            font-weight:700;
                            margin-top:12px">

                    <span>Total</span>

                    <span style="color:var(--acento)">
                        RD$ <?= number_format($total, 2) ?>
                    </span>
                </div>

            </div>
        </div>
    </div>

</div>
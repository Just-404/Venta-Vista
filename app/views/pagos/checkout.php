<?php
$items  = $items ?? [];
$total  = $total ?? 0;
$subtotal = $subtotal ?? 0;
$descuento = $descuento ?? 0;
$cupon = $cupon ?? null;
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

        <form method="POST" action="<?= BASE_URL ?>pagos/procesar">

            <input type="hidden" name="monto" value="<?= $total ?>">

            <div style="padding:20px">

                <!-- Tarjeta -->
                <div class="metodo-pago">
                    <label class="radio-pago">
                        <input type="radio" 
                               name="metodoPago" 
                               value="Tarjeta_Credito" 
                               checked>

                        <span>Tarjeta de Crédito</span>
                    </label>

                    <div class="grupo-form">
                        <label class="etiqueta-form">Número de tarjeta</label>
                        <input class="input-form"
                               type="text"
                               name="numeroTarjeta"
                               placeholder="0000 0000 0000 0000"
                               pattern="[0-9\s]{13,19}"
                               maxlength="19">

                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div class="grupo-form">
                            <label class="etiqueta-form">Expiración</label>
                            <input class="input-form"
                                   type="text"
                                   name="expiracion"
                                   placeholder="MM/AA"
                                   maxlength="5"
                                   pattern="(0[1-9]|1[0-2])\/[0-9]{2}">                                  
                        </div>

                        <div class="grupo-form">
                            <label class="etiqueta-form">CVV</label>
                            <input class="input-form"
                                   type="text"
                                   name="cvv"
                                   placeholder="123"
                                   pattern="[0-9]{3,4}"
                                   maxlength="4">
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
                               placeholder="Ej: TXN-004-2026"
                               pattern="[A-Za-z0-9\-]{5,30}">
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
                            <?= htmlspecialchars($item['nombre']) ?>
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
                        RD$ <?= number_format($subtotal, 2) ?>
                    </span>
                </div>

                <?php if ($descuento > 0): ?>
                <div class="detalle-fila" style="color:var(--exito)">
                    <span>
                        Cupón <?= htmlspecialchars($cupon['codigo']) ?>
                    </span>
                    <span>
                        - RD$ <?= number_format($descuento, 2) ?>
                    </span>
                </div>
                <?php endif; ?>
                
                <div class="separador-seccion"></div>
                
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
<script>
document.addEventListener("DOMContentLoaded", function(){

const metodoPago = document.querySelectorAll('input[name="metodoPago"]')

const numeroTarjeta = document.querySelector('[name="numeroTarjeta"]')
const expiracion = document.querySelector('[name="expiracion"]')
const cvv = document.querySelector('[name="cvv"]')
const referencia = document.querySelector('[name="referencia"]')

function actualizarValidaciones(){

const metodo = document.querySelector('input[name="metodoPago"]:checked').value

// Reset
if(numeroTarjeta) numeroTarjeta.required = false
if(expiracion) expiracion.required = false
if(cvv) cvv.required = false
if(referencia) referencia.required = false

if(metodo === "Tarjeta_Credito"){
if(numeroTarjeta) numeroTarjeta.required = true
if(expiracion) expiracion.required = true
if(cvv) cvv.required = true
}

if(metodo === "Transferencia"){
if(referencia) referencia.required = true
}

}

metodoPago.forEach(radio=>{
radio.addEventListener('change', actualizarValidaciones)
})

actualizarValidaciones()

})

document.querySelectorAll('input[name="metodoPago"]').forEach(radio => {

radio.addEventListener('change', function(){

document.querySelectorAll('.metodo-pago').forEach(div=>{
div.classList.remove('activo')
})

this.closest('.metodo-pago').classList.add('activo')

})

})

</script>
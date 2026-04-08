<?php
// Uso: $p debe tener clave 'estado'
$estado = $p['estado'] ?? $envio['estado'] ?? $pago['estado'] ?? '';

$mapa = [
    'Pendiente'   => 'badge--amarillo',
    'Confirmado'  => 'badge--azul',
    'En_proceso'  => 'badge--azul',
    'Enviado'     => 'badge--morado',
    'En_Camino'   => 'badge--morado',
    'En_Destino'  => 'badge--morado',
    'Entregado'   => 'badge--verde',
    'Cancelado'   => 'badge--rojo',
    'Devuelto'    => 'badge--rojo',
    'Aprobado'    => 'badge--verde',
    'Rechazado'   => 'badge--rojo',
    'Reembolsado' => 'badge--amarillo',
    'activo'      => 'badge--verde',
    'abandonado'  => 'badge--rojo',
    'convertido'  => 'badge--azul',
];

$clase = $mapa[$estado] ?? 'badge--gris';
?>
<span class="badge <?= $clase ?>"><?= htmlspecialchars(str_replace('_', ' ', $estado)) ?></span>
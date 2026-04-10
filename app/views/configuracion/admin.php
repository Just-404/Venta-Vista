<?php
$config = $config ?? [];
$prefs  = $prefs  ?? [];
$perfil = $perfil ?? [];
$u      = $usuario ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Configuración</h1>
        <p class="page-sub">Parámetros del sistema y preferencias del administrador</p>
    </div>
</div>

<!-- Tabs -->
<div class="config-tabs">
    <button class="config-tab-btn activo" data-tab="fiscal">📋 Datos Fiscales</button>
    <button class="config-tab-btn" data-tab="impuestos">💰 Impuestos y Envío</button>
    <button class="config-tab-btn" data-tab="notificaciones">🔔 Notificaciones</button>
    <button class="config-tab-btn" data-tab="perfil">👤 Mi Perfil</button>
    <button class="config-tab-btn" data-tab="password">🔑 Contraseña</button>
</div>

<!-- Tab: Datos Fiscales -->
<div class="config-panel activo" id="tab-fiscal">
    <div class="config-seccion">
        <div class="config-seccion-header"><h2>📋 Datos Fiscales del Negocio</h2></div>
        <div class="config-seccion-body">
            <form method="POST" action="<?= BASE_URL ?>configuracion/fiscal">
                <div class="grid-form">
                    <div class="grupo-form completo">
                        <label class="etiqueta-form">Nombre del Negocio</label>
                        <input class="input-form" type="text" name="negocio_nombre"
                               value="<?= htmlspecialchars($config['negocio_nombre'] ?? '') ?>" required>
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">RNC / Cédula Fiscal</label>
                        <input class="input-form" type="text" name="negocio_rnc"
                               value="<?= htmlspecialchars($config['negocio_rnc'] ?? '') ?>">
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">Teléfono</label>
                        <input class="input-form" type="text" name="negocio_telefono"
                               value="<?= htmlspecialchars($config['negocio_telefono'] ?? '') ?>">
                    </div>
                    <div class="grupo-form completo">
                        <label class="etiqueta-form">Dirección</label>
                        <input class="input-form" type="text" name="negocio_direccion"
                               value="<?= htmlspecialchars($config['negocio_direccion'] ?? '') ?>">
                    </div>
                    <div class="grupo-form completo">
                        <label class="etiqueta-form">Correo Institucional</label>
                        <input class="input-form" type="email" name="negocio_email"
                               value="<?= htmlspecialchars($config['negocio_email'] ?? '') ?>">
                    </div>
                </div>
                <div class="config-acciones">
                    <button type="submit" class="btn btn-primario">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tab: Impuestos -->
<div class="config-panel" id="tab-impuestos">
    <div class="config-seccion">
        <div class="config-seccion-header"><h2>💰 Impuestos y Envío</h2></div>
        <div class="config-seccion-body">
            <form method="POST" action="<?= BASE_URL ?>configuracion/impuestos">
                <div class="grid-form">
                    <div class="grupo-form">
                        <label class="etiqueta-form">Tasa ITBIS (%)</label>
                        <input class="input-form" type="number" name="itbis_porcentaje"
                               min="0" max="100" step="0.1"
                               value="<?= htmlspecialchars($config['itbis_porcentaje'] ?? '18') ?>">
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">Costo de Envío Base (RD$)</label>
                        <input class="input-form" type="number" name="envio_costo_base"
                               min="0" step="1"
                               value="<?= htmlspecialchars($config['envio_costo_base'] ?? '200') ?>">
                    </div>
                </div>
                <div class="config-acciones">
                    <button type="submit" class="btn btn-primario">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tab: Notificaciones -->
<div class="config-panel" id="tab-notificaciones">
    <div class="config-seccion">
        <div class="config-seccion-header"><h2>🔔 Preferencias de Notificaciones</h2></div>
        <div class="config-seccion-body">
            <form method="POST" action="<?= BASE_URL ?>configuracion/preferencias">
                <?php $toggles = [
                    'confirmar_pedido'    => 'Enviar correo al confirmar un pedido',
                    'alerta_stock'        => 'Alerta cuando el stock sea ≤ 5 unidades',
                    'factura_automatica'  => 'Envío automático de facturas al cliente',
                    'notif_estado_pedido' => 'Notificar al cliente al cambiar estado del pedido',
                    'registro_publico'    => 'Permitir registro público de nuevos clientes',
                ]; ?>
                <?php foreach ($toggles as $key => $label): ?>
                    <div class="toggle-fila">
                        <span class="toggle-label"><?= $label ?></span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="<?= $key ?>" value="1"
                                   <?= !empty($prefs[$key]) ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                <?php endforeach; ?>
                <div class="config-acciones">
                    <button type="submit" class="btn btn-primario">Guardar Preferencias</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tab: Mi Perfil -->
<div class="config-panel" id="tab-perfil">
    <div class="config-seccion">
        <div class="config-seccion-header"><h2>👤 Mi Perfil</h2></div>
        <div class="config-seccion-body">
            <form method="POST" action="<?= BASE_URL ?>configuracion/perfil">
                <div class="grid-form">
                    <div class="grupo-form">
                        <label class="etiqueta-form">Nombre</label>
                        <input class="input-form" type="text" name="nombre"
                               value="<?= htmlspecialchars($perfil['nombre'] ?? '') ?>" required>
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">Apellidos</label>
                        <input class="input-form" type="text" name="apellidos"
                               value="<?= htmlspecialchars($perfil['apellidos'] ?? '') ?>" required>
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">Cédula</label>
                        <input class="input-form" type="text" name="cedula"
                               value="<?= htmlspecialchars($perfil['cedula'] ?? '') ?>">
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">Teléfono</label>
                        <input class="input-form" type="text" name="telefono"
                               value="<?= htmlspecialchars($perfil['telefono'] ?? '') ?>">
                    </div>
                    <div class="grupo-form completo">
                        <label class="etiqueta-form">Correo Personal</label>
                        <input class="input-form" type="email" name="email"
                               value="<?= htmlspecialchars($u['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="config-acciones">
                    <button type="submit" class="btn btn-primario">Actualizar Perfil</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tab: Contraseña -->
<div class="config-panel" id="tab-password">
    <div class="config-seccion">
        <div class="config-seccion-header"><h2>🔑 Cambiar Contraseña</h2></div>
        <div class="config-seccion-body">
            <form method="POST" action="<?= BASE_URL ?>configuracion/password">
                <div class="grid-form">
                    <div class="grupo-form completo">
                        <label class="etiqueta-form">Contraseña Actual</label>
                        <input class="input-form" type="password" name="password_actual" required>
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">Nueva Contraseña</label>
                        <input class="input-form" type="password" name="password_nueva" minlength="6" required>
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">Confirmar Nueva</label>
                        <input class="input-form" type="password" name="password_confirma" minlength="6" required>
                    </div>
                </div>
                <div class="config-acciones">
                    <button type="submit" class="btn btn-primario">Cambiar Contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.config-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.config-tab-btn').forEach(b => b.classList.remove('activo'));
        document.querySelectorAll('.config-panel').forEach(p => p.classList.remove('activo'));
        btn.classList.add('activo');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('activo');
    });
});
</script>


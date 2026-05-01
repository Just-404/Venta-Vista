<?php
$prefs       = $prefs       ?? [];
$perfil      = $perfil      ?? [];
$u           = $usuario     ?? [];
$direcciones = $direcciones ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Mi Cuenta</h1>
        <p class="page-sub">Gestiona tu información, direcciones y preferencias</p>
    </div>
</div>

<div class="config-tabs">
    <button class="config-tab-btn activo" data-tab="perfil">👤 Mi Perfil</button>
    <button class="config-tab-btn" data-tab="direcciones">📍 Direcciones</button>
    <button class="config-tab-btn" data-tab="facturacion">🧾 Facturación</button>
    <button class="config-tab-btn" data-tab="notificaciones">🔔 Notificaciones</button>
    <button class="config-tab-btn" data-tab="password">🔑 Contraseña</button>
</div>

<!-- Tab: Perfil -->
<div class="config-panel activo" id="tab-perfil">
    <div class="config-seccion">
        <div class="config-seccion-header"><h2>👤 Información Personal</h2></div>
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
                        <label class="etiqueta-form">Cédula / Pasaporte</label>
                        <input class="input-form" type="text" name="cedula"
                               pattern="^\d{3}-\d{7}-\d{1}$"
                               title="Formato: 001-0000000-0"
                               value="<?= htmlspecialchars($perfil['cedula'] ?? '') ?>">
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">Teléfono</label>
                        <input class="input-form" type="text" name="telefono"
                               pattern="^\d{3}-\d{3}-\d{4}$"
                               title="Formato: 829-000-0000"
                               value="<?= htmlspecialchars($perfil['telefono'] ?? '') ?>">
                    </div>
                    <div class="grupo-form completo">
                        <label class="etiqueta-form">Correo Electrónico</label>
                        <input class="input-form" type="email" name="email"
                               value="<?= htmlspecialchars($u['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="config-acciones">
                    <button type="submit" class="btn btn-primario">Guardar Información</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tab: Direcciones -->
<div class="config-panel" id="tab-direcciones">
    <div class="config-seccion">
        <div class="config-seccion-header"><h2>📍 Mis Direcciones de Envío</h2></div>
        <div class="config-seccion-body">
            <?php if (empty($direcciones)): ?>
                <p style="color:var(--texto-muted); font-size:.9rem; margin-bottom:1.25rem">
                    No tienes direcciones guardadas aún.
                </p>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:.75rem; margin-bottom:1.5rem">
                    <?php foreach ($direcciones as $dir): ?>
                    <div class="direccion-card <?= $dir['esPrincipal'] ? 'direccion-card--principal' : '' ?>">
                        <div class="direccion-info">
                            <div class="direccion-texto">
                                <strong><?= htmlspecialchars($dir['calle']) ?></strong>
                                <span>
                                    <?= htmlspecialchars($dir['ciudad']) ?>,
                                    <?= htmlspecialchars($dir['provincia']) ?>
                                    <?= $dir['codigoPostal'] ? '— CP ' . htmlspecialchars($dir['codigoPostal']) : '' ?>
                                </span>
                            </div>
                            <?php if ($dir['esPrincipal']): ?>
                                <span class="badge badge--verde">Principal</span>
                            <?php endif; ?>
                        </div>
                        <div class="direccion-acciones">
                            <?php if (!$dir['esPrincipal']): ?>
                            <form method="POST" action="<?= BASE_URL ?>configuracion/direccion/principal" style="display:inline">
                                <input type="hidden" name="idDireccion" value="<?= $dir['idDireccion'] ?>">
                                <button type="submit" class="btn btn-sm btn-contorno">Hacer principal</button>
                            </form>
                            <?php endif; ?>
                            <form method="POST" action="<?= BASE_URL ?>configuracion/direccion/eliminar" style="display:inline"
                                  onsubmit="return confirm('¿Eliminar esta dirección?')">
                                <input type="hidden" name="idDireccion" value="<?= $dir['idDireccion'] ?>">
                                <button type="submit" class="btn btn-sm btn-peligro">Eliminar</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <details class="agregar-dir-toggle">
                <summary class="btn btn-contorno" style="cursor:pointer; display:inline-block">
                    + Agregar nueva dirección
                </summary>
                <div style="margin-top:1.25rem">
                    <form method="POST" action="<?= BASE_URL ?>configuracion/direccion/agregar">
                        <div class="grid-form">
                            <div class="grupo-form completo">
                                <label class="etiqueta-form">Calle / Dirección completa</label>
                                <input class="input-form" type="text" name="calle"
                                       placeholder="Ej: Av. 27 de Febrero #45, Apto 3B" required>
                            </div>
                            <div class="grupo-form">
                                <label class="etiqueta-form">Ciudad</label>
                                <input class="input-form" type="text" name="ciudad"
                                       placeholder="Santiago" required>
                            </div>
                            <div class="grupo-form">
                                <label class="etiqueta-form">Provincia</label>
                                <input class="input-form" type="text" name="provincia"
                                       placeholder="Santiago" required>
                            </div>
                            <div class="grupo-form">
                                <label class="etiqueta-form">Código Postal</label>
                                <input class="input-form" type="text" name="codigoPostal" placeholder="51000">
                            </div>
                            <div class="grupo-form" style="display:flex; align-items:center; gap:.5rem; padding-top:1.5rem">
                                <input type="checkbox" name="esPrincipal" value="1" id="esPrincipal"
                                       style="width:16px; height:16px; accent-color:var(--acento)">
                                <label for="esPrincipal" class="etiqueta-form" style="margin:0; cursor:pointer">
                                    Usar como dirección principal
                                </label>
                            </div>
                        </div>
                        <div class="config-acciones">
                            <button type="submit" class="btn btn-primario">Guardar Dirección</button>
                        </div>
                    </form>
                </div>
            </details>
        </div>
    </div>
</div>

<!-- Tab: Facturación -->
<div class="config-panel" id="tab-facturacion">
    <div class="config-seccion">
        <div class="config-seccion-header"><h2>🧾 Preferencias de Facturación</h2></div>
        <div class="config-seccion-body">
            <form method="POST" action="<?= BASE_URL ?>configuracion/perfil">
                <div class="grid-form">
                    <div class="grupo-form">
                        <label class="etiqueta-form">Tipo de Comprobante</label>
                        <select class="input-form" name="tipo_comprobante" id="tipo_comprobante">
                            <option value="consumidor_final"
                                <?= ($perfil['tipo_comprobante'] ?? '') === 'consumidor_final' ? 'selected' : '' ?>>
                                Consumidor Final
                            </option>
                            <option value="credito_fiscal"
                                <?= ($perfil['tipo_comprobante'] ?? '') === 'credito_fiscal' ? 'selected' : '' ?>>
                                Crédito Fiscal (Empresa)
                            </option>
                        </select>
                    </div>
                    <div class="grupo-form" id="grupo-rnc"
                         style="<?= ($perfil['tipo_comprobante'] ?? '') !== 'credito_fiscal' ? 'display:none' : '' ?>">
                        <label class="etiqueta-form">RNC de la Empresa</label>
                        <input class="input-form" type="text" name="rnc_empresa" data-val="rnc"
                               value="<?= htmlspecialchars($perfil['rnc_empresa'] ?? '') ?>"
                               placeholder="1-31-00000-0">
                    </div>
                    <div class="grupo-form completo" id="grupo-nombre-empresa"
                         style="<?= ($perfil['tipo_comprobante'] ?? '') !== 'credito_fiscal' ? 'display:none' : '' ?>">
                        <label class="etiqueta-form">Nombre / Razón Social</label>
                        <input class="input-form" type="text" name="nombre_empresa"
                               value="<?= htmlspecialchars($perfil['nombre_empresa'] ?? '') ?>"
                               placeholder="Empresa S.R.L.">
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
        <div class="config-seccion-header"><h2>🔔 Notificaciones</h2></div>
        <div class="config-seccion-body">
            <form method="POST" action="<?= BASE_URL ?>configuracion/preferencias">
                <?php $toggles = [
                    'confirmar_pedido'    => 'Recibir confirmación de mis pedidos por correo',
                    'notif_estado_pedido' => 'Notificarme cuando cambie el estado de mi pedido',
                    'factura_automatica'  => 'Recibir facturas automáticamente por correo',
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
                    <button type="submit" class="btn btn-primario">Guardar</button>
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
            <form method="POST" action="<?= BASE_URL ?>configuracion/password" id="form-password-cliente">
                <div class="grid-form">
                    <div class="grupo-form completo">
                        <label class="etiqueta-form">Contraseña Actual</label>
                        <input class="input-form" type="password" name="password_actual" required>
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">Nueva Contraseña</label>
                        <input class="input-form" type="password" name="password_nueva" id="pw-nueva-c" minlength="8" required>
                    </div>
                    <div class="grupo-form">
                        <label class="etiqueta-form">Confirmar Nueva</label>
                        <input class="input-form" type="password" name="password_confirma" id="pw-conf-c" minlength="8" required>
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

document.getElementById('tipo_comprobante')?.addEventListener('change', function () {
    const show = this.value === 'credito_fiscal';
    document.getElementById('grupo-rnc').style.display           = show ? '' : 'none';
    document.getElementById('grupo-nombre-empresa').style.display = show ? '' : 'none';
});

document.getElementById('form-password-cliente').addEventListener('submit', function(e) {
    const nueva    = document.getElementById('pw-nueva-c').value;
    const confirma = document.getElementById('pw-conf-c').value;
    if (nueva !== confirma) {
        e.preventDefault();
        alert('Las contraseñas nuevas no coinciden.');
        document.getElementById('pw-conf-c').focus();
    }
});
</script>

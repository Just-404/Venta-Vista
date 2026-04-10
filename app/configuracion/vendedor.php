<?php
$prefs  = $prefs  ?? [];
$perfil = $perfil ?? [];
$u      = $usuario ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Configuración</h1>
        <p class="page-sub">Ajusta tu perfil y preferencias personales</p>
    </div>
</div>

<div class="config-tabs">
    <button class="config-tab-btn activo" data-tab="perfil">👤 Mi Perfil</button>
    <button class="config-tab-btn" data-tab="notificaciones">🔔 Notificaciones</button>
    <button class="config-tab-btn" data-tab="password">🔑 Contraseña</button>
</div>

<!-- Tab: Perfil -->
<div class="config-panel activo" id="tab-perfil">
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
                        <label class="etiqueta-form">Correo</label>
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

<!-- Tab: Notificaciones -->
<div class="config-panel" id="tab-notificaciones">
    <div class="config-seccion">
        <div class="config-seccion-header"><h2>🔔 Notificaciones</h2></div>
        <div class="config-seccion-body">
            <form method="POST" action="<?= BASE_URL ?>configuracion/preferencias">
                <?php $toggles = [
                    'confirmar_pedido'    => 'Recibir correo al confirmar un pedido',
                    'alerta_stock'        => 'Alerta cuando el stock sea ≤ 5 unidades',
                    'notif_estado_pedido' => 'Notificación al cambiar estado de un pedido',
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

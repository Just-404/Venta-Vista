<?php $cliente = $cliente ?? []; ?>

<!-- ── header ────────────────────────────────────────────── -->
<div class="page-header">
    <div>
        <h1 class="page-titulo">Editar Cliente</h1>
        <p class="page-sub">
            <?= htmlspecialchars(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellidos'] ?? '')) ?>
        </p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?= BASE_URL ?>clientes/ver?id=<?= $cliente['idCliente'] ?? '' ?>" class="btn btn-contorno">← Ver perfil</a>
        <a href="<?= BASE_URL ?>clientes" class="btn btn-contorno">Lista</a>
    </div>
</div>

<div class="panel" style="max-width:740px">
    <form method="POST" action="<?= BASE_URL ?>clientes/editar?id=<?= $cliente['idCliente'] ?? '' ?>"
          style="display:block">

        <div style="padding:24px">

            <!-- Sección 1: Datos personales -->
            <p class="panel-titulo" style="margin-bottom:16px">Datos personales</p>
            <div class="grid-form">
                <div class="grupo-form">
                    <label>Nombre</label>
                    <input class="input-form" type="text" name="nombre" required
                           value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>">
                </div>
                <div class="grupo-form">
                    <label>Apellidos</label>
                    <input class="input-form" type="text" name="apellidos" required
                           value="<?= htmlspecialchars($cliente['apellidos'] ?? '') ?>">
                </div>
                <div class="grupo-form">
                    <label>Cédula</label>
                    <input class="input-form" type="text" name="cedula" required
                           maxlength="13" minlength="13"
                           pattern="^\d{3}-\d{7}-\d{1}$"
                           title="Formato: 001-0000000-0"
                           value="<?= htmlspecialchars($cliente['cedula'] ?? '') ?>">
                </div>
                <div class="grupo-form">
                    <label>Teléfono</label>
                    <input class="input-form" type="text" name="telefono"
                           pattern="^\d{3}-\d{3}-\d{4}$"
                           title="Formato: 829-000-0000"
                           value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>">
                </div>
                <div class="grupo-form completo">
                    <label>Email</label>
                    <input class="input-form" type="email" name="email" required
                           value="<?= htmlspecialchars($cliente['email'] ?? '') ?>">
                </div>
            </div>

            <!-- Separador -->
            <div class="separador-seccion" style="margin:20px 0"></div>

            <!-- Sección 2: Estado -->
            <p class="panel-titulo" style="margin-bottom:16px">Estado de la cuenta</p>
            <div class="grid-form">
                <div class="grupo-form">
                    <label>Estado</label>
                    <select class="select-form" name="activo">
                        <option value="1" <?= ($cliente['activo'] ?? 0) ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= !($cliente['activo'] ?? 1) ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>

        </div>

        <!-- Acciones -->
        <div class="form-acciones" style="padding:0 24px 24px">
            <a href="<?= BASE_URL ?>clientes/ver?id=<?= $cliente['idCliente'] ?? '' ?>"
               class="btn btn-secundario">Cancelar</a>
            <button class="btn btn-primario" type="submit">Guardar cambios</button>
        </div>

    </form>

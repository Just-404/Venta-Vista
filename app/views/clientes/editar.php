<?php $cliente = $cliente ?? []; ?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Editar Cliente</h1>
        <p class="page-sub"><?= htmlspecialchars(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellidos'] ?? '')) ?></p>
    </div>
    <a href="<?= BASE_URL ?>clientes" class="btn btn-contorno">← Volver</a>
</div>

<div class="panel" style="max-width:720px">
    <form method="POST" action="<?= BASE_URL ?>clientes/editar?id=<?= $cliente['idCliente'] ?>">
        <div style="padding:20px">
            <div class="grid-form">
                <div class="grupo-form">
                    <label class="etiqueta-form">Nombre</label>
                    <input class="input-form" type="text" name="nombre"
                           value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Apellidos</label>
                    <input class="input-form" type="text" name="apellidos"
                           value="<?= htmlspecialchars($cliente['apellidos'] ?? '') ?>" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Cédula</label>
                    <input class="input-form" type="text" name="cedula"
                           value="<?= htmlspecialchars($cliente['cedula'] ?? '') ?>" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Teléfono</label>
                    <input class="input-form" type="text" name="telefono"
                           value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>">
                </div>
                <div class="grupo-form completo">
                    <label class="etiqueta-form">Email</label>
                    <input class="input-form" type="email" name="email"
                           value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" required>
                </div>
            </div>
        </div>
        <div class="form-acciones" style="padding:0 20px 20px">
            <a href="<?= BASE_URL ?>clientes" class="btn btn-secundario">Cancelar</a>
            <button class="btn btn-primario" type="submit">Guardar cambios</button>
        </div>
    </form>
</div>
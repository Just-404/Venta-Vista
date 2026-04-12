<!-- ── header ────────────────────────────────────────────── -->
<div class="page-header">
    <div>
        <h1 class="page-titulo">Nuevo Usuario</h1>
        <p class="page-sub">Crea un usuario y su perfil según el rol asignado</p>
    </div>
    <a href="<?= BASE_URL ?>usuarios" class="btn btn-contorno">← Volver</a>
</div>

<div class="panel" style="max-width:720px">
    <form method="POST" action="<?= BASE_URL ?>usuarios/crear" style="display:block">

        <div style="padding:24px">

            <!-- Sección 1: Datos personales -->
            <p class="panel-titulo" style="margin-bottom:16px">Datos personales</p>
            <div class="grid-form">
                <div class="grupo-form">
                    <label>Nombre</label>
                    <input class="input-form" type="text" name="nombre"
                           placeholder="Carlos" required
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                </div>
                <div class="grupo-form">
                    <label>Apellidos</label>
                    <input class="input-form" type="text" name="apellidos"
                           placeholder="Martínez Pérez" required
                           value="<?= htmlspecialchars($_POST['apellidos'] ?? '') ?>">
                </div>
                <div class="grupo-form">
                    <label>Cédula</label>
                    <input class="input-form" type="text" name="cedula"
                           placeholder="001-0000000-0" required
                           maxlength="13" minlength="13"
                           pattern="^\d{3}-\d{7}-\d{1}$"
                           title="Formato: 001-0000000-0"
                           value="<?= htmlspecialchars($_POST['cedula'] ?? '') ?>">
                </div>
                <div class="grupo-form">
                    <label>Teléfono</label>
                    <input class="input-form" type="text" name="telefono"
                           placeholder="829-000-0000"
                           value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                </div>
            </div>

            <!-- Separador -->
            <div class="separador-seccion" style="margin:20px 0"></div>

            <!-- Sección 2: Acceso al sistema -->
            <p class="panel-titulo" style="margin-bottom:16px">Acceso al sistema</p>
            <div class="grid-form">
                <div class="grupo-form">
                    <label>Nombre de usuario</label>
                    <input class="input-form" type="text" name="nombreUsuario"
                           placeholder="usuario123" required
                           value="<?= htmlspecialchars($_POST['nombreUsuario'] ?? '') ?>">
                </div>
                <div class="grupo-form">
                    <label>Email</label>
                    <input class="input-form" type="email" name="email"
                           placeholder="correo@ejemplo.com" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="grupo-form">
                    <label>Contraseña</label>
                    <input class="input-form" type="password" name="password"
                           placeholder="Mínimo 8 caracteres" required minlength="8">
                </div>
                <div class="grupo-form">
                    <label>Rol</label>
                    <select class="select-form" name="idRol" required>
                        <option value="1" <?= ($_POST['idRol'] ?? '') == '1' ? 'selected' : '' ?>>
                            Administrador
                        </option>
                        <option value="2" <?= ($_POST['idRol'] ?? '2') == '2' ? 'selected' : '' ?>>
                            Vendedor
                        </option>
                    </select>
                </div>
            </div>

            <!-- Aviso de rol -->
            <div id="aviso-rol" style="margin-top:14px;padding:12px 16px;border-radius:var(--radio-sm);
                                        background:rgba(59,130,246,.08);border:1.5px solid rgba(59,130,246,.2);
                                        font-size:.82rem;color:var(--info,#3b82f6)">
                <strong>Vendedor:</strong> puede gestionar catálogo, pedidos, clientes e inventario.
            </div>

        </div>

        <!-- Acciones -->
        <div class="form-acciones" style="padding:0 24px 24px">
            <a href="<?= BASE_URL ?>usuarios" class="btn btn-secundario">Cancelar</a>
            <button class="btn btn-primario" type="submit">Crear usuario</button>
        </div>

    </form>
</div>

<script>
const avisos = {
    '1': '<strong>Administrador:</strong> acceso total al sistema, incluyendo reportes y configuración.',
    '2': '<strong>Vendedor:</strong> puede gestionar catálogo, pedidos, clientes e inventario.',
};
document.querySelector('select[name="idRol"]').addEventListener('change', function () {
    document.getElementById('aviso-rol').innerHTML = avisos[this.value] || '';
});
</script>

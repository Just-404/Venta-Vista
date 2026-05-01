<!-- ──  header ────────────────────────────────────────────── -->
<div class="page-header">
    <div>
        <h1 class="page-titulo">Nuevo Cliente</h1>
        <p class="page-sub">Completa los datos del cliente y su acceso al sistema</p>
    </div>
    <a href="<?= BASE_URL ?>clientes" class="btn btn-contorno">← Volver</a>
</div>

<div class="panel" style="max-width:740px">
    <form method="POST" action="<?= BASE_URL ?>clientes/crear" style="display:block">

        <div style="padding:24px">

            <!-- Sección 1: Datos personales -->
            <p class="panel-titulo" style="margin-bottom:16px">Datos personales</p>
            <div class="grid-form">
                <div class="grupo-form">
                    <label>Nombre</label>
                    <input class="input-form" type="text" name="nombre"
                           placeholder="María" required
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                </div>
                <div class="grupo-form">
                    <label>Apellidos</label>
                    <input class="input-form" type="text" name="apellidos"
                           placeholder="González López" required
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
                           pattern="^\d{3}-\d{3}-\d{4}$"
                           title="Formato: 829-000-0000"
                           value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                </div>
                <div class="grupo-form completo">
                    <label>Email</label>
                    <input class="input-form" type="email" name="email"
                           placeholder="correo@ejemplo.com" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
            </div>

            <!-- Separador -->
            <div class="separador-seccion" style="margin:20px 0"></div>

            <!-- Sección 2: Acceso -->
            <p class="panel-titulo" style="margin-bottom:16px">Acceso al sistema</p>
            <div class="grid-form">
                <div class="grupo-form">
                    <label>Nombre de usuario</label>
                    <input class="input-form" type="text" name="nombreUsuario"
                           placeholder="usuario123" required
                           value="<?= htmlspecialchars($_POST['nombreUsuario'] ?? '') ?>">
                </div>
                <div class="grupo-form">
                    <label>Contraseña</label>
                    <input class="input-form" type="password" name="password"
                           placeholder="Mínimo 8 caracteres" required minlength="8">
                </div>
            </div>

        </div>

        <!-- Acciones -->
        <div class="form-acciones" style="padding:0 24px 24px">
            <a href="<?= BASE_URL ?>clientes" class="btn btn-secundario">Cancelar</a>
            <button class="btn btn-primario" type="submit">Registrar cliente</button>
        </div>

    </form>
</div>

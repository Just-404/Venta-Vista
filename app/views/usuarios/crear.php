<div class="page-header">
    <div>
        <h1 class="page-titulo">Nuevo Usuario</h1>
        <p class="page-sub">Crea un usuario y su perfil según el rol asignado</p>
    </div>
    <a href="<?= BASE_URL ?>usuarios" class="btn btn-contorno">← Volver</a>
</div>

<div class="panel" style="max-width:720px">
    <form method="POST" action="<?= BASE_URL ?>usuarios/crear">
        <div style="padding:20px">

            <p class="panel-titulo" style="margin-bottom:16px">Datos personales</p>
            <div class="grid-form">
                <div class="grupo-form">
                    <label class="etiqueta-form">Nombre</label>
                    <input class="input-form" type="text" name="nombre" placeholder="Carlos" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Apellidos</label>
                    <input class="input-form" type="text" name="apellidos" placeholder="Martínez Pérez" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Cédula</label>
                    <input class="input-form" type="text" name="cedula" placeholder="001-0000000-0" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Teléfono</label>
                    <input class="input-form" type="text" name="telefono" placeholder="829-000-0000">
                </div>
            </div>

            <div class="separador-seccion"></div>

            <p class="panel-titulo" style="margin-bottom:16px">Acceso al sistema</p>
            <div class="grid-form">
                <div class="grupo-form">
                    <label class="etiqueta-form">Nombre de usuario</label>
                    <input class="input-form" type="text" name="nombreUsuario" placeholder="usuario123" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Email</label>
                    <input class="input-form" type="email" name="email" placeholder="correo@ejemplo.com" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Contraseña</label>
                    <input class="input-form" type="password" name="password" placeholder="Mínimo 8 caracteres" required>
                </div>
                <div class="grupo-form">
                    <label class="etiqueta-form">Rol</label>
                    <select class="select-form" name="idRol" required>
                        <option value="1">Administrador</option>
                        <option value="2" selected>Vendedor</option>
                    </select>
                </div>
            </div>

        </div>
        <div class="form-acciones" style="padding:0 20px 20px">
            <a href="<?= BASE_URL ?>usuarios" class="btn btn-secundario">Cancelar</a>
            <button class="btn btn-primario" type="submit">Crear usuario</button>
        </div>
    </form>
</div>
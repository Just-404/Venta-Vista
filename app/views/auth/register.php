<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/common.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <title>Registro - Venta Vista</title>
</head>
<body>
<div class="bg"></div>
<div class="bg bg2"></div>
<div class="bg bg3"></div>
<div id="contenedor-login">
    <div class="register-box">

      <div class="login-logo">
            <div class="login-logo-icono"><img src="<?= BASE_URL ?>images/Logo_VentaVista.png" alt="logo sistema"></div>
            <div>
                <h1>Venta Vista</h1>
                <p class="login-logo-sub">Sistema de Ventas por Catálogo</p>
            </div>
        </div>
 
        <h2 class="login-titulo">Crear cuenta</h2>
        <p class="login-sub">Regístrate para empezar a comprar.</p>
<?php if (!empty($flash)): ?>
        <div id="contenedor-toast">
            <div class="toast <?= $flash['tipo'] ?>">
                <?= htmlspecialchars($flash['mensaje']) ?>
            </div>
        </div>
    <?php endif; ?>
      <!--
        CORRECCIÓN CRÍTICA: action apunta a do-register (no a register)
        'register' solo muestra el formulario
        'do-register' lo procesa (AuthController::processRegister)
      -->
<form method="POST" action="<?= BASE_URL ?>registrarCliente">

  <h3>Datos personales</h3>

  <div class="grid-form">

    <div class="grupo-form">
      <label>Nombre</label>
      <input class="input-form" type="text" name="nombre" required
             pattern="^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]{2,50}$"
             title="Solo letras, mínimo 2 caracteres">
    </div>

    <div class="grupo-form">
      <label>Apellidos</label>
      <input class="input-form" type="text" name="apellidos" required
             pattern="^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]{2,50}$">
    </div>

    <div class="grupo-form">
      <label>Cédula</label>
      <input class="input-form" type="text" name="cedula" required
             placeholder="000-0000000-0"
             pattern="^\d{3}-\d{7}-\d{1}$"
             maxLength="13"
             title="Formato: 000-0000000-0">
    </div>

    <div class="grupo-form">
      <label>Teléfono</label>
      <input class="input-form" type="text" name="telefono"
             placeholder="809-000-0000"
             pattern="^(809|829|849)-\d{3}-\d{4}$"
             maxLength="20"
             title="Formato: 809-000-0000">
    </div>

    <div class="grupo-form">
      <label>Correo</label>
      <input class="input-form" type="email" name="email" required>
    </div>

  </div>

  <h3>Ubicación</h3>

  <div class="grid-form">

    <div class="grupo-form">
      <label>Dirección</label>
      <input class="input-form" type="text" name="direccion">
    </div>

    <div class="grupo-form">
      <label>Ciudad</label>
      <input class="input-form" type="text" name="ciudad">
    </div>

    <div class="grupo-form">
      <label>Provincia</label>
      <input class="input-form" type="text" name="provincia">
    </div>

  </div>

  <h3>Seguridad</h3>

  <div class="grid-form">
    <div class="grupo-form">
      <label>Nombre de usuario</label>
      <input class="input-form" type="text" name="usuario" required>
    </div>

    <div class="grupo-form">
      <label>Contraseña</label>
      <input class="input-form" type="password" id="contrasena" name="password" required
             pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
             title="Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 símbolo"
             oninput="checkStrength(this.value)">
    </div>

    <div class="grupo-form">
      <label>Confirmar contraseña</label>
      <input class="input-form" type="password" id="confirmar" name="confirmar" required>
    </div>

  </div>

  <div class="pass-strength">
    <div class="ps-bar" id="strength-bar"></div>
  </div>
  <p class="ps-label">
    Seguridad: <strong id="strength-label">Ingresa una contraseña</strong>
  </p>

  <button class="btn btn-primario">Crear cuenta</button>

</form>

      <p class="login-hint" style="margin-top:16px;">
        ¿Ya tienes cuenta?
        <a href="<?= BASE_URL ?>login" style="color:var(--accent);font-weight:500;">
          Inicia sesión
        </a>
      </p>

    </div>
  </div>

<script>
function checkStrength(val) {
  const bar   = document.getElementById('strength-bar');
  const label = document.getElementById('strength-label');
  let score   = 0;
  if (val.length >= 8)            score++;
  if (/[A-Z]/.test(val))          score++;
  if (/[0-9]/.test(val))          score++;
  if (/[^A-Za-z0-9]/.test(val))   score++;
  const cfg = {
    0: { w:'0%',   c:'#e24343', t:'Ingresa una contraseña' },
    1: { w:'25%',  c:'#e24343', t:'Muy débil' },
    2: { w:'50%',  c:'#e8a022', t:'Débil' },
    3: { w:'75%',  c:'#2e6de6', t:'Media' },
    4: { w:'100%', c:'#1fbd74', t:'Fuerte ✓' },
  }[score];
  bar.style.width      = cfg.w;
  bar.style.background = cfg.c;
  label.textContent    = cfg.t;
  label.style.color    = cfg.c;
}
document.getElementById("confirmar").addEventListener("input", function() {
  const pass = document.getElementById("contrasena").value;
  if (this.value !== pass) {
    this.setCustomValidity("Las contraseñas no coinciden");
  } else {
    this.setCustomValidity("");
  }
});
</script>
</body>
</html>
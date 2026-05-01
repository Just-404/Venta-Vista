<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/common.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <title>Login - Ventas por Catálogo</title>

</head>
<body>
<div class="bg"></div>
<div class="bg bg2"></div>
<div class="bg bg3"></div>
<div id="contenedor-login">
    <?php if (!empty($flash) && $flash['tipo'] === 'error'): ?>
        <div id="contenedor-toast">
            <div class="toast <?= $flash['tipo'] ?>">
                 <?= htmlspecialchars($flash['mensaje']) ?>
            </div>
           
        </div>
    <?php endif; ?>
  <div class="login-box">
    <div class="login-logo">
      <div class="login-logo-icono"><img src="<?= BASE_URL ?>images/Logo_VentaVista.png" alt="logo sistema"></div>
      <div>
        <h1>Venta Vista</h1>
        <p class="login-logo-sub">Sistema de Ventas por Catálogo</p>
      </div>
    </div>

    <div>
      <h2 class="login-titulo">Bienvenido de vuelta</h2>
      <p class="login-sub">Ingresa tus credenciales para acceder al sistema.</p>
    </div>

    <div class="separador-seccion"></div>

    <!-- Formulario principal de login -->
    <form method="POST" action="<?= BASE_URL ?>login">

    <div class="grupo-form">
      <label for="login-usuario">Usuario</label>
      <input class="input-form" type="text" id="login-usuario"
             placeholder="Usuario" value="" name="usuario" required/>
    </div>

    <div class="grupo-form">
      <label for="login-pass">Contraseña</label>
      <input class="input-form" type="password" id="login-pass"
             placeholder="Contraseña" value="" name="password" required/>
    </div>

    <div class="grupo-links">
      <a class="login-olvidaste">¿Olvidaste tu contraseña?</a>
      <a class="login-registrarse">Registrarse</a>
    </div>
    <button class="btn btn-primario btn-completo" type="submit">Iniciar Sesión</button>

    </form>

  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toast = document.querySelector("#contenedor-toast .toast");

    if (toast) {
        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateX(50px)";

            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000); // 3 segundos
    }
});
</script>
</body>
</html>
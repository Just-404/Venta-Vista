<header class="topbar">
      
  <div class="usuario">
    <div class="usuario-avatar" id="sb-avatar"><?= $iniciales ?></div>
    <div>
      <div class="usuario-nombre" id="sb-nombre"><?= $nombreUsuario ?></div>
      <div class="usuario-rol" id="sb-rol"><?= $nombreRol ?></div>
    </div>
  </div>

  <div class="topbar-derecha">
    <!-- Carrito (solo clientes) -->
    <?php if (($usuario['rol'] ?? 0) == 3): ?>
    <a href="<?= BASE_URL ?>carrito" class="accion-topbar" style="text-decoration:none">
      🛒
      <span class="indicador-badge" id="badge-carrito" style="display:none"></span>
    </a>
    <?php else: ?>
    <button class="accion-topbar">
      🛒
      <span class="indicador-badge" id="badge-carrito" style="display:none"></span>
    </button>
    <?php endif; ?>

    <!-- Notificaciones -->
    <button class="accion-topbar">
      🔔<span class="indicador-badge"></span>
    </button>

    <!-- Configuración → redirige según rol -->
    <a href="<?= BASE_URL ?>configuracion" class="accion-topbar" style="text-decoration:none" title="Configuración">
      ⚙️
    </a>
  </div>

</header><!-- /topbar -->

<header class="topbar">
        
      <div class="usuario">
        <div class="usuario-avatar" id="sb-avatar"><?= $iniciales ?></div>
        <div>
          <div class="usuario-nombre" id="sb-nombre"><?= $nombreUsuario ?></div>
          <div class="usuario-rol" id="sb-rol"><?= $nombreRol ?></div>
        </div>
      </div>
        
        <!-- Acciones del topbar: carrito, notificaciones, ajustes -->
        <div class="topbar-derecha">
          <!-- Botón carrito con punto indicador de ítems -->
          <button class="accion-topbar">
            🛒
            <span class="indicador-badge" id="badge-carrito" style="display:none"></span>
          </button>
          <!-- Botón notificaciones -->
          <button class="accion-topbar">
            🔔<span class="indicador-badge"></span>
          </button>
          <!-- Botón configuración -->
          <button class="accion-topbar">⚙️</button>
        </div>
</header><!-- /topbar -->
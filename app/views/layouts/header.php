<header class="topbar">
        <div class="topbar-izquierda">
          <div>
            <div class="titulo-pagina"  id="topbar-titulo">Dashboard</div>
            <div class="breadcrumb-pagina" id="topbar-breadcrumb">Inicio / Dashboard</div>
          </div>
        </div>

        <!-- Acciones del topbar: carrito, notificaciones, ajustes -->
        <div class="topbar-derecha">
          <!-- Botón carrito con punto indicador de ítems -->
          <button class="accion-topbar" onclick="mostrarCarrito()">
            🛒
            <span class="indicador-badge" id="badge-carrito" style="display:none"></span>
          </button>
          <!-- Botón notificaciones -->
          <button class="accion-topbar"
                  onclick="mostrarToast('No hay nuevas notificaciones','info')">
            🔔<span class="indicador-badge"></span>
          </button>
          <!-- Botón configuración -->
          <button class="accion-topbar" onclick="navegar('config')">⚙️</button>
        </div>
</header><!-- /topbar -->
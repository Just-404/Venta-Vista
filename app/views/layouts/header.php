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
      <img src="<?= BASE_URL ?>images/icons/carrito-icon.png" class="icon" alt="logo sistema">
      <span class="indicador-badge" id="badge-carrito" style="display:none"></span>
    </a>
    <?php endif; ?>

<!-- Notificaciones -->
    <div class="notif-wrapper" id="notif-wrapper">
      <button class="accion-topbar" id="notif-btn" title="Notificaciones">
        <img src="<?= BASE_URL ?>images/icons/notificacion-icon.png" class="icon" alt="logo sistema">
        <span class="indicador-badge" id="notif-badge" style="display:none"></span>
      </button>
      <div class="notif-panel" id="notif-panel" style="display:none">
        <div class="notif-panel-header">
          <span>Notificaciones</span>
          <button class="notif-btn-leer-todas" id="notif-leer-todas">Marcar leídas</button>
        </div>
        <div class="notif-lista" id="notif-lista">
          <div class="notif-vacia">Cargando…</div>
        </div>
        <div class="notif-panel-footer">
          <button class="notif-btn-limpiar" id="notif-limpiar-todas">🗑️ Limpiar historial</button>
        </div>
      </div>
    </div>

    <!-- Configuración → redirige según rol -->
    <a href="<?= BASE_URL ?>configuracion" class="accion-topbar" style="text-decoration:none" title="Configuración">
      <img src="<?= BASE_URL ?>images/icons/configuracion-icon.png" class="icon" alt="logo sistema">
    </a>
  </div>

  <style>
.notif-wrapper { position: relative; display: inline-block; }

.notif-panel {
  position: absolute; right: 0; top: calc(100% + 8px);
  width: 330px; background: var(--bg-card, #fff);
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,.12);
  z-index: 1000; overflow: hidden;
  display: flex; flex-direction: column;
}

.notif-panel-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 12px 16px; border-bottom: 1px solid var(--border, #e2e8f0);
  font-weight: 600; font-size: 14px; color: var(--text, #1e293b);
  flex-shrink: 0;
}

.notif-btn-leer-todas {
  background: none; border: none; color: var(--primary, #2563eb);
  font-size: 12px; cursor: pointer; padding: 4px 8px; border-radius: 4px;
}
.notif-btn-leer-todas:hover { background: #eff6ff; }

.notif-lista { max-height: 320px; overflow-y: auto; flex: 1; }

.notif-panel-footer {
  display: flex; justify-content: center;
  padding: 10px 16px; border-top: 1px solid var(--border, #e2e8f0);
  flex-shrink: 0;
}

.notif-btn-limpiar {
  background: none; border: 1px solid #fca5a5; color: #dc2626;
  font-size: 12px; cursor: pointer; padding: 5px 14px;
  border-radius: 6px; width: 100%;
  transition: background .15s, color .15s;
}
.notif-btn-limpiar:hover { background: #fee2e2; }

.notif-item {
  display: flex; gap: 10px; padding: 12px 16px;
  border-bottom: 1px solid var(--border, #f1f5f9);
  cursor: pointer; transition: background .15s;
  text-decoration: none; color: inherit; position: relative;
}
.notif-item:hover { background: var(--bg-hover, #f8fafc); }
.notif-item.no-leida { background: #eff6ff; }
.notif-item.no-leida:hover { background: #dbeafe; }

.notif-item-del {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: #cbd5e1; font-size: 14px;
  cursor: pointer; padding: 4px; line-height: 1; border-radius: 4px;
  opacity: 0; transition: opacity .15s, color .15s;
}
.notif-item:hover .notif-item-del { opacity: 1; }
.notif-item-del:hover { color: #ef4444 !important; }

.notif-icono { font-size: 20px; flex-shrink: 0; margin-top: 2px; }

.notif-contenido { flex: 1; min-width: 0; padding-right: 18px; }
.notif-titulo { font-size: 13px; font-weight: 600; color: var(--text, #1e293b);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.notif-mensaje { font-size: 12px; color: #64748b;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
.notif-fecha { font-size: 11px; color: #94a3b8; margin-top: 4px; }

.notif-vacia {
  padding: 32px 16px; text-align: center;
  font-size: 13px; color: #94a3b8;
}
</style>
      


</header><!-- /topbar -->

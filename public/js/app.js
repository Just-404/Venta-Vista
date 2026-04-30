function toggleSidebar() {
    const sidebar   = document.getElementById('sidebar');
    const mainArea  = document.querySelector('.main-area');
    const isMobile  = window.innerWidth <= 768;
 
    if (isMobile) {
        sidebar.classList.toggle('sidebar--open');
    } else {
        const hidden = sidebar.classList.toggle('sidebar--hidden');
        mainArea.style.marginLeft = hidden ? '0' : 'var(--sidebar-w)';
    }
}

// ── Notificaciones ────────────────────────────────────────────────────────────

const NOTIF_ICONOS = {
    pedido_nuevo:      '🛍️',
    estado_pedido:     '📦',
    stock_bajo:        '⚠️',
    nuevo_cliente:     '👥',
    cupon_vence:       '🎟️',
    envio_actualizado: '🚚',
    pago:              '💳',
    factura:           '🧾',
    sistema:           '🔔',
};

function getBase() {
    return window.APP_BASE || '/';
}

async function cargarNotificaciones() {
    try {
        const res = await fetch(getBase() + 'notificaciones/obtener', { credentials: 'same-origin' });
        if (!res.ok) return;
        const data = await res.json();

        // Badge
        const badge = document.getElementById('notif-badge');
        if (badge) {
            if (data.noLeidas > 0) {
                badge.textContent = data.noLeidas > 9 ? '9+' : data.noLeidas;
                badge.style.display = '';
            } else {
                badge.style.display = 'none';
            }
        }

        // Lista
        const lista = document.getElementById('notif-lista');
        if (!lista) return;

        if (!data.lista || data.lista.length === 0) {
            lista.innerHTML = '<div class="notif-vacia">✅ Sin notificaciones</div>';
            return;
        }

        lista.innerHTML = data.lista.map(n => {
            const icono   = NOTIF_ICONOS[n.tipo] ?? '🔔';
            const leida   = parseInt(n.leida) === 1;
            const url     = n.url || '#';
            const mensaje = n.mensaje
                ? `<div class="notif-mensaje">${escHtml(n.mensaje)}</div>` : '';
            return `
              <a href="${escHtml(url)}"
                 class="notif-item ${leida ? '' : 'no-leida'}"
                 data-id="${n.idNotificacion}"
                 data-url="${escHtml(url)}">
                <div class="notif-icono">${icono}</div>
                <div class="notif-contenido">
                  <div class="notif-titulo">${escHtml(n.titulo)}</div>
                  ${mensaje}
                  <div class="notif-fecha">${escHtml(n.fechaFormato ?? '')}</div>
                </div>
                <button class="notif-item-del" data-del="${n.idNotificacion}" title="Eliminar">✕</button>
              </a>`;
        }).join('');

        // Eventos de los ítems (delegación)
        lista.querySelectorAll('.notif-item').forEach(el => {
            el.addEventListener('click', e => {
                // Si se hizo clic en el botón eliminar, no navegar
                if (e.target.closest('.notif-item-del')) return;
                e.preventDefault();
                marcarLeida(parseInt(el.dataset.id), el.dataset.url);
            });
        });

        lista.querySelectorAll('.notif-item-del').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                e.stopPropagation();
                eliminarNotif(parseInt(btn.dataset.del));
            });
        });

    } catch (err) {
        console.warn('[Notificaciones] Error al cargar:', err);
    }
}

async function marcarLeida(idNotif, url) {
    try {
        const fd = new FormData();
        fd.append('id', idNotif);
        await fetch(getBase() + 'notificaciones/leer', {
            method: 'POST', body: fd, credentials: 'same-origin'
        });
    } catch (_) {}
    window.location.href = url;
}

async function eliminarNotif(idNotif) {
    // Quitar del DOM inmediatamente (no esperar al servidor)
    const el = document.querySelector(`.notif-item[data-id="${idNotif}"]`);
    const eraNoLeida = el && el.classList.contains('no-leida');
    if (el) el.remove();

    // Mostrar estado vacío si ya no quedan
    const lista = document.getElementById('notif-lista');
    if (lista && lista.querySelectorAll('.notif-item').length === 0) {
        lista.innerHTML = '<div class="notif-vacia">✅ Sin notificaciones</div>';
    }

    // Actualizar el badge manualmente
    const badge = document.getElementById('notif-badge');
    if (badge && eraNoLeida) {
        const nuevo = Math.max(0, (parseInt(badge.textContent) || 0) - 1);
        if (nuevo > 0) {
            badge.textContent = nuevo > 9 ? '9+' : nuevo;
        } else {
            badge.style.display = 'none';
        }
    }

    try {
        const fd = new FormData();
        fd.append('id', idNotif);
        await fetch(getBase() + 'notificaciones/limpiar', {
            method: 'POST', body: fd, credentials: 'same-origin'
        });
    } catch (err) {
        console.warn('[Notificaciones] Error al eliminar:', err);
        cargarNotificaciones(); // revertir si falla el servidor
    }
}

async function leerTodasNotif() {
    try {
        await fetch(getBase() + 'notificaciones/leer-todas', {
            method: 'POST', credentials: 'same-origin'
        });
        cargarNotificaciones();
    } catch (err) {
        console.warn('[Notificaciones]', err);
    }
}

async function limpiarTodasNotif() {
    if (!confirm('¿Eliminar todo el historial de notificaciones?')) return;
    try {
        await fetch(getBase() + 'notificaciones/limpiar-todas', {
            method: 'POST', credentials: 'same-origin'
        });
        cargarNotificaciones();
    } catch (err) {
        console.warn('[Notificaciones]', err);
    }
}

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// Cierra el panel si se hace clic fuera
document.addEventListener('click', e => {
    const wrapper = document.getElementById('notif-wrapper');
    const panel   = document.getElementById('notif-panel');
    if (panel && wrapper && !wrapper.contains(e.target)) {
        panel.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // Abrir/cerrar panel
    const btn = document.getElementById('notif-btn');
    if (btn) {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            const panel = document.getElementById('notif-panel');
            if (!panel) return;
            const abierto = panel.style.display !== 'none';
            panel.style.display = abierto ? 'none' : 'flex';
            if (!abierto) cargarNotificaciones();
        });
    }

    // Marcar todas leídas
    const btnLeer = document.getElementById('notif-leer-todas');
    if (btnLeer) btnLeer.addEventListener('click', leerTodasNotif);

    // Limpiar historial
    const btnLimpiar = document.getElementById('notif-limpiar-todas');
    if (btnLimpiar) btnLimpiar.addEventListener('click', limpiarTodasNotif);

    // Carga inicial + polling
    if (document.getElementById('notif-badge')) {
        cargarNotificaciones();
        setInterval(cargarNotificaciones, 60_000);
    }
});

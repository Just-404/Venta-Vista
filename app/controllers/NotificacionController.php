<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Notificacion;

class NotificacionController extends Controller {

    /**
     * GET /notificaciones/obtener
     * Devuelve JSON con conteo de no leídas y lista reciente.
     */
    public function obtener(): void {
        $this->requireAuth();
        $usuario = $this->usuarioActual();
        $id      = (int) ($usuario['id']  ?? 0);
        $rol     = (int) ($usuario['rol'] ?? 0);

        // Solo admins: verificar cupones próximos a vencer (una vez al día)
        if ($rol === 1) {
            Notificacion::verificarCuponesVencimiento($id);
        }

        $lista = Notificacion::obtenerRecientes($id, 10);

        foreach ($lista as &$n) {
            $n['fechaFormato'] = self::formatearFecha($n['fechaCreacion']);
        }
        unset($n);

        $this->json([
            'noLeidas' => Notificacion::contarNoLeidas($id),
            'lista'    => $lista,
        ]);
    }

    /**
     * POST /notificaciones/leer
     * Marca una notificación como leída. Body: id=X
     */
    public function leer(): void {
        $this->requireAuth();
        $idUsuario = (int) ($this->usuarioActual()['id'] ?? 0);
        $idNotif   = (int) $this->post('id');

        $ok = Notificacion::marcarLeida($idNotif, $idUsuario);
        $this->json(['ok' => $ok]);
    }

    /**
     * POST /notificaciones/leer-todas
     * Marca todas las notificaciones del usuario como leídas.
     */
    public function leerTodas(): void {
        $this->requireAuth();
        $id = (int) ($this->usuarioActual()['id'] ?? 0);

        $ok = Notificacion::marcarTodasLeidas($id);
        $this->json(['ok' => $ok]);
    }

    /**
     * POST /notificaciones/limpiar
     * Elimina una notificación del historial. Body: id=X
     */
    public function limpiar(): void {
        $this->requireAuth();
        $idUsuario = (int) ($this->usuarioActual()['id'] ?? 0);
        $idNotif   = (int) $this->post('id');

        $ok = Notificacion::eliminar($idNotif, $idUsuario);
        $this->json(['ok' => $ok]);
    }

    /**
     * POST /notificaciones/limpiar-todas
     * Elimina todas las notificaciones del historial del usuario.
     */
    public function limpiarTodas(): void {
        $this->requireAuth();
        $id = (int) ($this->usuarioActual()['id'] ?? 0);

        $ok = Notificacion::limpiarTodas($id);
        $this->json(['ok' => $ok]);
    }

    // ── Helper privado ───────────────────────────────────────────────────────

    private static function formatearFecha(string $fecha): string {
        $ts   = strtotime($fecha);
        $diff = time() - $ts;

        if ($diff < 60)     return 'Ahora mismo';
        if ($diff < 3600)   return (int)($diff / 60) . ' min';
        if ($diff < 86400)  return (int)($diff / 3600) . ' h';
        if ($diff < 604800) return (int)($diff / 86400) . ' d';
        return date('d/m/Y', $ts);
    }
}

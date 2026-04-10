<?php
namespace app\models;

use app\core\Model;
use PDO;

class Configuracion extends Model {

    // ── Sistema (admin) ──────────────────────────────────────────────────────
    public static function obtenerTodas(): array {
        $rows = self::db()
            ->query("SELECT clave, valor FROM configuracion_sistema")
            ->fetchAll(PDO::FETCH_KEY_PAIR);
        return $rows ?: [];
    }

    public static function obtener(string $clave): ?string {
        $stmt = self::db()->prepare(
            "SELECT valor FROM configuracion_sistema WHERE clave = :clave"
        );
        $stmt->execute(['clave' => $clave]);
        $r = $stmt->fetchColumn();
        return $r !== false ? $r : null;
    }

    public static function guardar(string $clave, string $valor): bool {
        $stmt = self::db()->prepare(
            "INSERT INTO configuracion_sistema (clave, valor)
             VALUES (:clave, :valor)
             ON DUPLICATE KEY UPDATE valor = :valor2"
        );
        return $stmt->execute(['clave' => $clave, 'valor' => $valor, 'valor2' => $valor]);
    }

    public static function guardarMultiple(array $datos): bool {
        foreach ($datos as $clave => $valor) {
            if (!self::guardar($clave, (string) $valor)) return false;
        }
        return true;
    }

    // ── Preferencias de notificación (por usuario) ───────────────────────────
    public static function obtenerPreferencias(int $idUsuario): array {
        $stmt = self::db()->prepare(
            "SELECT * FROM preferencias_notificacion WHERE idUsuario = :id"
        );
        $stmt->execute(['id' => $idUsuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
          
            // Crear fila por defecto si no existe
            self::db()->prepare(
                "INSERT IGNORE INTO preferencias_notificacion (idUsuario) VALUES (:id)"
            )->execute(['id' => $idUsuario]);

            return [
                'confirmar_pedido'    => 1,
                'alerta_stock'        => 1,
                'factura_automatica'  => 1,
                'notif_estado_pedido' => 1,
                'registro_publico'    => 0,
            ];
        }

        return $row;
    }

    public static function guardarPreferencias(int $idUsuario, array $datos): bool {
        $stmt = self::db()->prepare(
            "INSERT INTO preferencias_notificacion
                (idUsuario, confirmar_pedido, alerta_stock, factura_automatica,
                 notif_estado_pedido, registro_publico)
             VALUES
                (:idUsuario, :confirmar_pedido, :alerta_stock, :factura_automatica,
                 :notif_estado_pedido, :registro_publico)
             ON DUPLICATE KEY UPDATE
                confirmar_pedido    = VALUES(confirmar_pedido),
                alerta_stock        = VALUES(alerta_stock),
                factura_automatica  = VALUES(factura_automatica),
                notif_estado_pedido = VALUES(notif_estado_pedido),
                registro_publico    = VALUES(registro_publico)"
        );
        return $stmt->execute([
            'idUsuario'           => $idUsuario,
            'confirmar_pedido'    => (int) ($datos['confirmar_pedido']    ?? 0),
            'alerta_stock'        => (int) ($datos['alerta_stock']        ?? 0),
            'factura_automatica'  => (int) ($datos['factura_automatica']  ?? 0),
            'notif_estado_pedido' => (int) ($datos['notif_estado_pedido'] ?? 0),
            'registro_publico'    => (int) ($datos['registro_publico']    ?? 0),
        ]);
    }
}

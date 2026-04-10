<?php

namespace app\models;

use app\core\Model;
use PDO;

class Notificacion extends Model {

    /**
     * Crea una notificación para un usuario específico.
     * $data = ['idUsuario', 'tipo', 'titulo', 'mensaje'(opt), 'url'(opt)]
     */
    public static function crear(array $data): bool {
        $stmt = self::db()->prepare(
            "INSERT INTO notificaciones (idUsuario, tipo, titulo, mensaje, url)
             VALUES (:idUsuario, :tipo, :titulo, :mensaje, :url)"
        );
        return $stmt->execute([
            'idUsuario' => $data['idUsuario'],
            'tipo'      => $data['tipo'],
            'titulo'    => $data['titulo'],
            'mensaje'   => $data['mensaje'] ?? null,
            'url'       => $data['url']     ?? null,
        ]);
    }

    /**
     * Crea una notificación para todos los usuarios activos de un rol,
     * filtrando opcionalmente por una preferencia de notificación.
     * El $prefFiltro debe ser uno de los campos de preferencias_notificacion.
     */
    public static function crearParaRol(int $idRol, array $data, ?string $prefFiltro = null): void {
        if ($prefFiltro) {
            // Nota: $prefFiltro es siempre un literal desde el código, nunca entrada del usuario.
            $sql = "SELECT u.idUsuario
                    FROM usuarios u
                    LEFT JOIN preferencias_notificacion pn ON pn.idUsuario = u.idUsuario
                    WHERE u.idRol = :idRol AND u.activo = 1
                      AND COALESCE(pn.{$prefFiltro}, 1) = 1";
        } else {
            $sql = "SELECT idUsuario FROM usuarios WHERE idRol = :idRol AND activo = 1";
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute(['idRol' => $idRol]);
        $usuarios = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($usuarios as $idUsuario) {
            self::crear(array_merge($data, ['idUsuario' => (int) $idUsuario]));
        }
    }

    /**
     * Retorna usuarios activos de un rol con su email y nombre,
     * filtrando por preferencia. Usado para enviar correos.
     */
    public static function obtenerDestinatariosEmail(int $idRol, string $prefFiltro): array {
        $sql = "SELECT u.idUsuario, u.nombreUsuario, u.email
                FROM usuarios u
                LEFT JOIN preferencias_notificacion pn ON pn.idUsuario = u.idUsuario
                WHERE u.idRol = :idRol AND u.activo = 1
                  AND COALESCE(pn.{$prefFiltro}, 1) = 1";
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['idRol' => $idRol]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Retorna las últimas $limite notificaciones de un usuario (no leídas primero) */
    public static function obtenerRecientes(int $idUsuario, int $limite = 10): array {
        $stmt = self::db()->prepare(
            "SELECT * FROM notificaciones
             WHERE idUsuario = :id
             ORDER BY leida ASC, fechaCreacion DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':id',     $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Cuenta las notificaciones no leídas de un usuario */
    public static function contarNoLeidas(int $idUsuario): int {
        $stmt = self::db()->prepare(
            "SELECT COUNT(*) FROM notificaciones WHERE idUsuario = :id AND leida = 0"
        );
        $stmt->execute(['id' => $idUsuario]);
        return (int) $stmt->fetchColumn();
    }

    /** Marca una notificación específica como leída (valida que pertenezca al usuario) */
    public static function marcarLeida(int $idNotificacion, int $idUsuario): bool {
        $stmt = self::db()->prepare(
            "UPDATE notificaciones SET leida = 1
             WHERE idNotificacion = :idNotif AND idUsuario = :idUsuario"
        );
        return $stmt->execute(['idNotif' => $idNotificacion, 'idUsuario' => $idUsuario]);
    }

    /** Marca todas las notificaciones de un usuario como leídas */
    public static function marcarTodasLeidas(int $idUsuario): bool {
        $stmt = self::db()->prepare(
            "UPDATE notificaciones SET leida = 1 WHERE idUsuario = :id"
        );
        return $stmt->execute(['id' => $idUsuario]);
    }

    /** Elimina una notificación específica del usuario */
    public static function eliminar(int $idNotificacion, int $idUsuario): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM notificaciones
             WHERE idNotificacion = :idNotif AND idUsuario = :idUsuario"
        );
        return $stmt->execute(['idNotif' => $idNotificacion, 'idUsuario' => $idUsuario]);
    }

    /** Elimina todas las notificaciones de un usuario */
    public static function limpiarTodas(int $idUsuario): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM notificaciones WHERE idUsuario = :id"
        );
        return $stmt->execute(['id' => $idUsuario]);
    }

    /**
     * Verifica cupones que vencen en los próximos 7 días y crea una notificación
     * por cada uno si aún no fue notificado hoy. Solo para administradores.
     */
    public static function verificarCuponesVencimiento(int $idUsuario): void {
        $db = self::db();

        $stmt = $db->query(
            "SELECT * FROM cupones
             WHERE activo = 1
               AND fechaVencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
        );
        $cupones = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($cupones as $cupon) {
            // Evitar duplicados: si ya se notificó este cupón hoy, saltar
            $check = $db->prepare(
                "SELECT COUNT(*) FROM notificaciones
                 WHERE idUsuario = :id
                   AND tipo = 'cupon_vence'
                   AND titulo LIKE :titulo
                   AND DATE(fechaCreacion) = CURDATE()"
            );
            $check->execute(['id' => $idUsuario, 'titulo' => '%' . $cupon['codigo'] . '%']);
            if ((int) $check->fetchColumn() > 0) continue;

            $diasRestantes = max(0, (int) ceil(
                (strtotime($cupon['fechaVencimiento']) - time()) / 86400
            ));
            $fechaFormato = date('d/m/Y', strtotime($cupon['fechaVencimiento']));

            self::crear([
                'idUsuario' => $idUsuario,
                'tipo'      => 'cupon_vence',
                'titulo'    => "Cupón próximo a vencer: {$cupon['codigo']}",
                'mensaje'   => "Vence en {$diasRestantes} día(s) — el {$fechaFormato}",
                'url'       => BASE_URL . 'cupones',
            ]);
        }
    }
}

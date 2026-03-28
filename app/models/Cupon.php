<?php

namespace app\models;

use app\core\Model;
use PDO;

class Cupon extends Model {

    public static function obtenerTodos(): array {
        return self::db()
            ->query("SELECT * FROM cupones ORDER BY fechaVencimiento DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorId(int $id): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM cupones WHERE idCupon = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    /**
     * Valida que el cupón exista, este activo, en fecha y con usos disponibles.
     */
    public static function validar(string $codigo): array|false {
        $sql = "SELECT * FROM cupones
                WHERE codigo           = :codigo
                  AND activo           = 1
                  AND fechaInicio      <= CURDATE()
                  AND fechaVencimiento >= CURDATE()
                  AND usosActuales     < usoMaximo";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerActivos(): array {
        $sql = "SELECT * FROM cupones
                WHERE activo = 1
                  AND fechaInicio      <= CURDATE()
                  AND fechaVencimiento >= CURDATE()
                  AND usosActuales     < usoMaximo
                ORDER BY fechaVencimiento";
 
        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function crear(array $data): bool {
        $sql = "INSERT INTO cupones
                    (codigo, tipo, descuento, usoMaximo, usosActuales,
                     fechaInicio, fechaVencimiento, activo)
                VALUES
                    (:codigo, :tipo, :descuento, :usoMaximo, :usosActuales,
                     :fechaInicio, :fechaVencimiento, :activo)";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    public static function actualizar(array $data): bool {
        $sql = "UPDATE cupones
                SET codigo           = :codigo,
                    tipo             = :tipo,
                    descuento        = :descuento,
                    usoMaximo        = :usoMaximo,
                    fechaInicio      = :fechaInicio,
                    fechaVencimiento = :fechaVencimiento,
                    activo           = :activo
                WHERE idCupon = :idCupon";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    /** Incrementa el contador de usos cada vez que se aplica el cupón */
    public static function registrarUso(int $id): bool {
        $stmt = self::db()->prepare(
            "UPDATE cupones SET usosActuales = usosActuales + 1 WHERE idCupon = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
 
    public static function cambiarEstado(int $id, bool $activo): bool {
        $stmt = self::db()->prepare(
            "UPDATE cupones SET activo = :activo WHERE idCupon = :id"
        );
        return $stmt->execute(['activo' => (int) $activo, 'id' => $id]);
    }
 
    public static function eliminar(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM cupones WHERE idCupon = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
}
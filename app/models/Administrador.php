<?php

namespace app\models;

use app\core\Model;
use PDO;

class Administrador extends Model {

    public static function obtenerTodos(): array{
        $sql = "SELECT a.*, u.nombreUsuario, u.email, u.activo
                FROM administradores a
                JOIN usuarios u ON a.idUsuario = u.idUsuario";
 
        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId(int $id): array|false {
        $sql = "SELECT a.*, u.nombreUsuario, u.email, u.activo
                FROM administradores a
                JOIN usuarios u ON a.idUsuario = u.idUsuario
                WHERE a.idAdmin = :id";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorUsuario(int $idUsuario): array|false {
        $sql = "SELECT * FROM administradores WHERE idUsuario = :idUsuario";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['idUsuario' => $idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function crear(array $data): bool {
        $sql = "INSERT INTO administradores
                    (nombre, apellidos, cedula, telefono, idUsuario)
                VALUES
                    (:nombre, :apellidos, :cedula, :telefono, :idUsuario)";
 
        return self::db()->prepare($sql)->execute($data);
    }
    public static function actualizar(array $data): bool {
        $sql = "UPDATE administradores
                SET nombre    = :nombre,
                    apellidos = :apellidos,
                    cedula    = :cedula,
                    telefono  = :telefono
                WHERE idAdmin = :idAdmin";
 
        return self::db()->prepare($sql)->execute($data);
    }

    public static function eliminar(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM administradores WHERE idAdmin = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
 
    public static function buscarPorCedula(string $cedula): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM administradores WHERE cedula = :cedula"
        );
        $stmt->execute(['cedula' => $cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
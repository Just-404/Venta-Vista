<?php

namespace app\models;

use app\core\Model;
use PDO;

class Usuario extends Model{
 
    public static function obtenerTodos(): array{
        $sql = "SELECT u.*, r.nombre AS rol
                FROM usuarios u
                JOIN roles r ON u.idRol = r.idRol";
 
        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function buscarPorUsuario($username) {

        $sql = "SELECT u.*, r.nombre AS rol
                FROM usuarios u
                JOIN roles r ON u.idRol = r.idRol
                WHERE u.nombreUsuario = :usuario
                AND u.activo = 1";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['usuario' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public static function obtenerPorId(int $id): array|false {
        $sql = "SELECT u.*, r.nombre AS rol
                FROM usuarios u
                JOIN roles r ON u.idRol = r.idRol
                WHERE u.idUsuario = :id";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarPorEmail(string $email): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM usuarios WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function crear(array $data): bool{
        /* Estrcutura esperada de $data = [
            'nombreUsuario' => 'juan123',
            'contrasena' => password_hash('123456', PASSWORD_DEFAULT),
            'email' => 'juan@gmail.com',
            'idRol' => 2,
            'estado' => 1
            ]; */
            
        $sql = "INSERT INTO usuarios 
                (nombreUsuario, contrasena, email, idRol)
                VALUES 
                (:nombreUsuario, :contrasena, :email, :idRol)";

        $stmt = self::db()->prepare($sql);

        return $stmt->execute($data);
    }

    /* Retorna el ID del último usuario insertado */
    public static function crearYObtenerID(array $data): int {
        self::crear($data);
        return (int) self::db()->lastInsertId();
    }

    public static function actualizar(array $data): bool {
        $sql = "UPDATE usuarios
                SET nombreUsuario = :nombreUsuario,
                    email         = :email,
                    idRol         = :idRol
                WHERE idUsuario = :idUsuario";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    public static function actualizarContrasena(int $id, string $hash): bool {
        $stmt = self::db()->prepare(
            "UPDATE usuarios SET contrasena = :hash WHERE idUsuario = :id"
        );
        return $stmt->execute(['hash' => $hash, 'id' => $id]);
    }
 
    public static function cambiarEstado(int $id, bool $activo): bool {
        $stmt = self::db()->prepare(
            "UPDATE usuarios SET activo = :activo WHERE idUsuario = :id"
        );
        return $stmt->execute(['activo' => (int) $activo, 'id' => $id]);
    }
 
    public static function eliminar(int $id): bool {
        return self::cambiarEstado($id, false);
    }
 
    public static function eliminarFisico(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM usuarios WHERE idUsuario = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
}

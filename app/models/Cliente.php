<?php

namespace app\models;

use app\core\Model;
use PDO;

class Cliente extends Model {

    public static function obtenerTodos(): array {
        $sql = "SELECT c.*, u.nombreUsuario, u.email AS emailUsuario, u.activo
                FROM clientes c
                JOIN usuarios u ON c.idUsuario = u.idUsuario";
 
        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorId(int $id): array|false {
        $sql = "SELECT c.*, u.nombreUsuario, u.activo
                FROM clientes c
                JOIN usuarios u ON c.idUsuario = u.idUsuario
                WHERE c.idCliente = :id";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorUsuario(int $idUsuario): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM clientes WHERE idUsuario = :idUsuario"
        );
        $stmt->execute(['idUsuario' => $idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function buscarPorEmail(string $email): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM clientes WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function buscarPorCedula(string $cedula): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM clientes WHERE cedula = :cedula"
        );
        $stmt->execute(['cedula' => $cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
  
    /*
      $data = ['nombre','apellidos','cedula','telefono','email','idUsuario']
     */
    public static function crear(array $data): bool {
        $sql = "INSERT INTO clientes
                    (nombre, apellidos, cedula, telefono, email, idUsuario)
                VALUES
                    (:nombre, :apellidos, :cedula, :telefono, :email, :idUsuario)";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    public static function crearYObtenerID(array $data): int {
        self::crear($data);
        return (int) self::db()->lastInsertId();
    }
  
    /*
      $data = ['nombre','apellidos','cedula','telefono','email','idCliente']
     */
    public static function actualizar(array $data): bool {
        $sql = "UPDATE clientes
                SET nombre    = :nombre,
                    apellidos = :apellidos,
                    cedula    = :cedula,
                    telefono  = :telefono,
                    email     = :email
                WHERE idCliente = :idCliente";
 
        return self::db()->prepare($sql)->execute($data);
    }
  
    public static function eliminar(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM clientes WHERE idCliente = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
}
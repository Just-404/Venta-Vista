<?php

namespace app\models;

use app\core\Model;
use PDO;

class Calificacion extends Model {

    public static function obtenerPorProducto(int $idProducto): array {
        $sql = "SELECT cal.*, CONCAT(c.nombre,' ',c.apellidos) AS cliente
                FROM calificaciones cal
                JOIN clientes c ON cal.idCliente = c.idCliente
                WHERE cal.idProducto = :id
                ORDER BY cal.fecha DESC";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['id' => $idProducto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorCliente(int $idCliente): array {
        $sql = "SELECT cal.*, p.nombre AS producto
                FROM calificaciones cal
                JOIN productos p ON cal.idProducto = p.idProducto
                WHERE cal.idCliente = :id
                ORDER BY cal.fecha DESC";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['id' => $idCliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorId(int $id): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM calificaciones WHERE idCalificacion = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    /** Comprueba si el cliente ya calificó este producto (UK en schema) */
    public static function yaCalifico(int $idProducto, int $idCliente): bool {
        $stmt = self::db()->prepare(
            "SELECT COUNT(*) FROM calificaciones
             WHERE idProducto = :idProducto AND idCliente = :idCliente"
        );
        $stmt->execute(['idProducto' => $idProducto, 'idCliente' => $idCliente]);
        return (bool) $stmt->fetchColumn();
    }
 
    public static function promedioProducto(int $idProducto): float {
        $stmt = self::db()->prepare(
            "SELECT COALESCE(ROUND(AVG(nota),1), 0) FROM calificaciones WHERE idProducto = :id"
        );
        $stmt->execute(['id' => $idProducto]);
        return (float) $stmt->fetchColumn();
    }
  
    /*
      $data = ['nota','comentario','idProducto','idCliente']
     */
    public static function crear(array $data): bool {
        $sql = "INSERT INTO calificaciones
                    (nota, comentario, idProducto, idCliente)
                VALUES
                    (:nota, :comentario, :idProducto, :idCliente)";
 
        return self::db()->prepare($sql)->execute($data);
    }
  
    /*
      $data = ['nota','comentario','idCalificacion']
     */
    public static function actualizar(array $data): bool {
        $sql = "UPDATE calificaciones
                SET nota       = :nota,
                    comentario = :comentario
                WHERE idCalificacion = :idCalificacion";
 
        return self::db()->prepare($sql)->execute($data);
    }
  
    public static function eliminar(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM calificaciones WHERE idCalificacion = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
}
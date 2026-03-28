<?php

namespace app\models;

use app\core\Model;
use PDO;

class Direccion extends Model {

    public static function obtenerPorCliente(int $idCliente): array {
        $stmt = self::db()->prepare(
            "SELECT * FROM direcciones WHERE idCliente = :id ORDER BY esPrincipal DESC"
        );
        $stmt->execute(['id' => $idCliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorId(int $id): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM direcciones WHERE idDireccion = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPrincipal(int $idCliente): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM direcciones
             WHERE idCliente = :idCliente AND esPrincipal = 1
             LIMIT 1"
        );
        $stmt->execute(['idCliente' => $idCliente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    /*
      $data = ['calle','ciudad','provincia','codigoPostal','esPrincipal','idCliente']
     */
    public static function crear(array $data): bool {
        // Si la nueva dirección es principal, quita el flag de las demás
        if (!empty($data['esPrincipal'])) {
            self::quitarPrincipal((int) $data['idCliente']);
        }
 
        $sql = "INSERT INTO direcciones
                    (calle, ciudad, provincia, codigoPostal, esPrincipal, idCliente)
                VALUES
                    (:calle, :ciudad, :provincia, :codigoPostal, :esPrincipal, :idCliente)";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    /*
      $data = ['calle','ciudad','provincia','codigoPostal','esPrincipal','idDireccion']
     */
    public static function actualizar(array $data): bool {
        if (!empty($data['esPrincipal'])) {
            // Necesitamos el idCliente para quitar el flag de las demás
            $dir = self::obtenerPorId((int) $data['idDireccion']);
            if ($dir) self::quitarPrincipal((int) $dir['idCliente']);
        }
 
        $sql = "UPDATE direcciones
                SET calle        = :calle,
                    ciudad       = :ciudad,
                    provincia    = :provincia,
                    codigoPostal = :codigoPostal,
                    esPrincipal  = :esPrincipal
                WHERE idDireccion = :idDireccion";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    public static function establecerPrincipal(int $idDireccion, int $idCliente): bool {
        self::quitarPrincipal($idCliente);
        $stmt = self::db()->prepare(
            "UPDATE direcciones SET esPrincipal = 1 WHERE idDireccion = :id"
        );
        return $stmt->execute(['id' => $idDireccion]);
    }
  
    public static function eliminar(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM direcciones WHERE idDireccion = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
 
    // Ayudante interno 
 
    private static function quitarPrincipal(int $idCliente): void {
        self::db()->prepare(
            "UPDATE direcciones SET esPrincipal = 0 WHERE idCliente = :idCliente"
        )->execute(['idCliente' => $idCliente]);
    }

}
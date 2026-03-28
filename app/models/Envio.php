<?php

namespace app\models;

use app\core\Model;

class Envio extends Model {

    public static function obtenerPorPedido(int $idPedido): array|false {
        $sql = "SELECT e.*, d.calle, d.ciudad, d.provincia
                FROM envios e
                JOIN direcciones d ON e.idDireccion = d.idDireccion
                WHERE e.idPedido = :idPedido";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['idPedido' => $idPedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function obtenerPorId(int $id): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM envios WHERE idEnvio = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerTodos(): array {
        $sql = "SELECT e.*, pe.numeroPedido,
                       CONCAT(c.nombre,' ',c.apellidos) AS cliente
                FROM envios e
                JOIN pedidos  pe ON e.idPedido  = pe.idPedido
                JOIN clientes c  ON pe.idCliente = c.idCliente
                ORDER BY e.fechaEstimada";
 
        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($data){

        $sql = "INSERT INTO envios
        (codigoRastreo, empresa, fechaEstimada, fechaEntrega, idPedido, idDireccion)
        VALUES
        (:codigoRastreo, :empresa, :fechaEstimada, :fechaEntrega, :idPedido, :idDireccion)";

        return self::db()->prepare($sql)->execute($data);
    }

    public static function actualizarEstado(int $id, string $estado): bool {
        $stmt = self::db()->prepare(
            "UPDATE envios SET estado = :estado WHERE idEnvio = :id"
        );
        return $stmt->execute(['estado' => $estado, 'id' => $id]);
    }
 
    public static function registrarEntrega(int $id, string $fecha): bool {
        $stmt = self::db()->prepare(
            "UPDATE envios
             SET estado = 'Entregado', fechaEntrega = :fecha
             WHERE idEnvio = :id"
        );
        return $stmt->execute(['fecha' => $fecha, 'id' => $id]);
    }
 
    public static function actualizarRastreo(int $id, string $codigo, string $empresa): bool {
        $stmt = self::db()->prepare(
            "UPDATE envios
             SET codigoRastreo = :codigo, empresa = :empresa
             WHERE idEnvio = :id"
        );
        return $stmt->execute(['codigo' => $codigo, 'empresa' => $empresa, 'id' => $id]);
    }
    
    public static function eliminar(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM envios WHERE idEnvio = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
}
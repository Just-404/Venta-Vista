<?php

namespace app\models;

use app\core\Model;

/*
    Estados: Pendiente | Aprobado | Rechazado | Reembolsado
    Métodos:  Tarjeta_Credito | Tarjeta_Debito | Transferencia | Efectivo 
*/

class Pago extends Model {

    public static function obtenerPorPedido(int $idPedido): array|false{
        $stmt = self::db()->prepare(
            "SELECT * FROM pagos WHERE idPedido = :idPedido"
        );
        $stmt->execute(['idPedido' => $idPedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId(int $id): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM pagos WHERE idPago = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerTodos(): array {
        $sql = "SELECT pg.*, pe.numeroPedido,
                       CONCAT(c.nombre,' ',c.apellidos) AS cliente
                FROM pagos pg
                JOIN pedidos pe  ON pg.idPedido  = pe.idPedido
                JOIN clientes c  ON pe.idCliente = c.idCliente
                ORDER BY pg.fechaPago DESC";
 
        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
        $data = ['monto','estado','referencia','metodoPago','idPedido']
     */

    public static function crear(array $data): bool{

        $sql = "INSERT INTO pagos
        (monto, estado, referencia, metodoPago, idPedido)
        VALUES
        (:monto, :estado, :referencia, :metodoPago, :idPedido)";

        return self::db()->prepare($sql)->execute($data);
    }

    public static function actualizarEstado(int $id, string $estado): bool {
        $stmt = self::db()->prepare(
            "UPDATE pagos SET estado = :estado WHERE idPago = :id"
        );
        return $stmt->execute(['estado' => $estado, 'id' => $id]);
    }

    public static function actualizarReferencia(int $id, string $referencia): bool {
        $stmt = self::db()->prepare(
            "UPDATE pagos SET referencia = :referencia WHERE idPago = :id"
        );
        return $stmt->execute(['referencia' => $referencia, 'id' => $id]);
    }

     public static function eliminar(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM pagos WHERE idPago = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
}
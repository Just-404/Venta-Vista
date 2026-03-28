<?php

namespace app\models;

use app\core\Model;
use PDO;

class Pedido extends Model {

    public static function obtenerTodos(): array {
        $sql = "SELECT p.*,
                       CONCAT(c.nombre,' ',c.apellidos) AS cliente,
                       cu.codigo AS cupon
                FROM pedidos p
                JOIN clientes c ON p.idCliente = c.idCliente
                LEFT JOIN cupones cu ON p.idCupon = cu.idCupon
                ORDER BY p.fechaPedido DESC";
 
        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorId(int $id): array|false {
        $sql = "SELECT p.*,
                       CONCAT(c.nombre,' ',c.apellidos) AS cliente,
                       cu.codigo AS cupon, cu.tipo AS tipoCupon
                FROM pedidos p
                JOIN clientes c  ON p.idCliente = c.idCliente
                LEFT JOIN cupones cu ON p.idCupon = cu.idCupon
                WHERE p.idPedido = :id";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorCliente(int $idCliente): array {
        $sql = "SELECT * FROM pedidos
                WHERE idCliente = :idCliente
                ORDER BY fechaPedido DESC";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['idCliente' => $idCliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorEstado(string $estado): array {
        $stmt = self::db()->prepare(
            "SELECT * FROM pedidos WHERE estado = :estado ORDER BY fechaPedido DESC"
        );
        $stmt->execute(['estado' => $estado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function buscarPorNumero(string $numero): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM pedidos WHERE numeroPedido = :numero"
        );
        $stmt->execute(['numero' => $numero]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    /*
      $data = ['numeroPedido','subtotal','descuento','total','notas','idCliente','idCupon']
      Retorna el ID del pedido creado.
     */
    public static function crear(array $data): int {
        $sql = "INSERT INTO pedidos
                    (numeroPedido, subtotal, descuento, total, notas, idCliente, idCupon)
                VALUES
                    (:numeroPedido, :subtotal, :descuento, :total, :notas, :idCliente, :idCupon)";
 
        self::db()->prepare($sql)->execute($data);
        return (int) self::db()->lastInsertId();
    }
 
    /* Genera un número de pedido con formato PED-YYYY-NNNNN */
    public static function generarNumeroPedido(): string {
        $year = date('Y');
        $stmt = self::db()->prepare(
            "SELECT COUNT(*) FROM pedidos WHERE YEAR(fechaPedido) = :year"
        );
        $stmt->execute(['year' => $year]);
        $count = (int) $stmt->fetchColumn() + 1;
        return sprintf('PED-%s-%05d', $year, $count);
    }
 
    public static function actualizarEstado(int $id, string $estado): bool {
        $stmt = self::db()->prepare(
            "UPDATE pedidos SET estado = :estado WHERE idPedido = :id"
        );
        return $stmt->execute(['estado' => $estado, 'id' => $id]);
    }
 
    public static function eliminar(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM pedidos WHERE idPedido = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
}
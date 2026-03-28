<?php

namespace app\models;

use app\core\Model;
use PDO;

class Pedido extends Model {

    public static function obtenerTodos(){

        $sql = "SELECT p.*, c.nombre as cliente
                FROM pedidos p
                JOIN clientes c 
                ON p.idCliente = c.idCliente";

        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($data){

        $sql = "INSERT INTO pedidos
        (numeroPedido, subtotal, descuento, total, notas, idCliente, idCupon)
        VALUES
        (:numeroPedido, :subtotal, :descuento, :total, :notas, :idCliente, :idCupon)";

        $stmt = self::db()->prepare($sql);

        $stmt->execute($data);

        return self::db()->lastInsertId();
    }

}
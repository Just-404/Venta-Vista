<?php 

namespace app\models;

use app\core\Model;
use PDO;

class Producto extends Model{
    public static function obtenerTodos(){

        $sql = "SELECT p.*, c.nombre as categoria
                FROM productos p
                JOIN categorias c 
                ON p.idCategoria = c.idCategoria";

        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id){

        $sql = "SELECT * FROM productos WHERE idProducto = :id";

        $stmt = self::db()->prepare($sql);

        $stmt->execute(['id'=>$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function crear($data){

        $sql = "INSERT INTO productos 
        (nombre, descripcion, precio, descuento, stock, imagenes, idCategoria)
        VALUES 
        (:nombre,:descripcion,:precio,:descuento, :stock,:imagenes,:idCategoria)";

        return self::db()->prepare($sql)->execute($data);
    }
}
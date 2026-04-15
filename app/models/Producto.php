<?php

namespace app\models;

use app\core\Model;
use PDO;

class Producto extends Model
{

    public static function obtenerTodos(): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria
                FROM productos p
                JOIN categorias c ON p.idCategoria = c.idCategoria
                ORDER BY p.fechaCreacion DESC";

        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerActivos(): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria
                FROM productos p
                JOIN categorias c ON p.idCategoria = c.idCategoria
                WHERE p.activo = 1
                ORDER BY p.nombre";

        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT p.*, c.nombre AS categoria
                FROM productos p
                JOIN categorias c ON p.idCategoria = c.idCategoria
                WHERE p.idProducto = :id";

        $stmt = self::db()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorCategoria(int $idCategoria): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria
                FROM productos p
                JOIN categorias c ON p.idCategoria = c.idCategoria
                WHERE p.idCategoria = :idCategoria AND p.activo = 1
                ORDER BY p.nombre";

        $stmt = self::db()->prepare($sql);
        $stmt->execute(['idCategoria' => $idCategoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Productos asignados a un vendedor específico */
    public static function obtenerPorVendedor(int $idUsuario): array
    {
        $sql = "SELECT p.*, 
                   u.nombreUsuario AS vendedor,
                   c.nombre AS categoria
            FROM productos p
            JOIN vendedores v ON p.idVendedor = v.idVendedor
            JOIN usuarios u   ON v.idUsuario = u.idUsuario
            JOIN categorias c ON p.idCategoria = c.idCategoria
            WHERE v.idVendedor= :idVendedor
            ORDER BY p.nombre";

        $stmt = self::db()->prepare($sql);
        $stmt->execute(['idVendedor' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Usa la vista v_productos_rating del schema */
    public static function obtenerConRating(): array
    {
        return self::db()
            ->query("SELECT * FROM v_productos_rating ORDER BY promedio DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscar(string $termino): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria
                FROM productos p
                JOIN categorias c ON p.idCategoria = c.idCategoria
                WHERE p.activo = 1
                  AND (p.nombre LIKE :t OR p.descripcion LIKE :t)
                ORDER BY p.nombre";

        $stmt = self::db()->prepare($sql);
        $stmt->execute(['t' => "%$termino%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerDestacados(int $limite = 8): array
    {
        $sql = "SELECT * FROM v_productos_rating
                ORDER BY promedio DESC, totalResenas DESC
                LIMIT :limite";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
      $data = ['nombre','descripcion','precio','descuento','stock','imagenes',
               'idCategoria','idVendedor']
     */
    public static function crear(array $data): bool
    {
        $sql = "INSERT INTO productos
                    (nombre, descripcion, precio, descuento, stock, imagenes,
                     idCategoria, idVendedor)
                VALUES
                    (:nombre, :descripcion, :precio, :descuento, :stock, :imagenes,
                     :idCategoria, :idVendedor)";

        $stmt = self::db()->prepare($sql);
        if ($stmt->execute($data)) {
            return (int) self::db()->lastInsertId();
        }
        return false;
    }

    /*
      $data = ['nombre','descripcion','precio','descuento','stock','imagenes',
               'idCategoria','idVendedor','idProducto']
     */
    public static function actualizar(array $data): bool
    {
        $sql = "UPDATE productos
                SET nombre      = :nombre,
                    descripcion = :descripcion,
                    precio      = :precio,
                    descuento   = :descuento,
                    stock       = :stock,
                    imagenes    = :imagenes,
                    idCategoria = :idCategoria,
                    idVendedor  = :idVendedor
                WHERE idProducto = :idProducto";

        return self::db()->prepare($sql)->execute($data);
    }

    public static function actualizarStock(int $id, int $cantidad): bool
    {
        $stmt = self::db()->prepare(
            "UPDATE productos SET stock = stock + :cantidad WHERE idProducto = :id"
        );
        return $stmt->execute(['cantidad' => $cantidad, 'id' => $id]);
    }

    public static function cambiarEstado(int $id, bool $activo): bool
    {
        $stmt = self::db()->prepare(
            "UPDATE productos SET activo = :activo WHERE idProducto = :id"
        );
        return $stmt->execute(['activo' => (int) $activo, 'id' => $id]);
    }

    public static function eliminar(int $id): bool
    {
        return self::cambiarEstado($id, false);
    }

    public static function eliminarFisico(int $id): bool
    {
        $stmt = self::db()->prepare(
            "DELETE FROM productos WHERE idProducto = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
}

<?php

namespace app\models;

use app\core\Model;
use PDO;

class Categoria extends Model {

    public static function obtenerTodas(): array{

        return self::db()
            ->query("SELECT * FROM categorias ORDER BY nombre")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId(int $id): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM categorias WHERE idCategoria = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function buscarPorNombre(string $nombre): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM categorias WHERE nombre = :nombre"
        );
        $stmt->execute(['nombre' => $nombre]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    /** Categorías que tienen al menos un producto activo */
    public static function obtenerConProductos(): array {
        $sql = "SELECT c.*, COUNT(p.idProducto) AS totalProductos
                FROM categorias c
                JOIN productos p ON p.idCategoria = c.idCategoria AND p.activo = 1
                GROUP BY c.idCategoria
                ORDER BY c.nombre";
 
        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

     public static function crear(array $data): bool{

        $sql = "INSERT INTO categorias 
        (nombre, descripcion)
        VALUES 
        (:nombre,:descripcion)";

        return self::db()->prepare($sql)->execute($data);
    }

    public static function actualizar(array $data): bool {
        $sql = "UPDATE categorias
                SET nombre      = :nombre,
                    descripcion = :descripcion
                WHERE idCategoria = :idCategoria";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    public static function eliminar(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM categorias WHERE idCategoria = :id"
        );
        return $stmt->execute(['id' => $id]);
    }

    // Todas las categorías con su conteo de productos (incluye las vacías).
    public static function obtenerTodosConConteo(): array {
        $sql = "SELECT c.*,
                    COUNT(p.idProducto) AS totalProductos
                FROM categorias c
                LEFT JOIN productos p ON p.idCategoria = c.idCategoria
                GROUP BY c.idCategoria
                ORDER BY c.nombre ASC";

        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verifica si ya existe una categoría con ese nombre.
    public static function existeNombre(string $nombre, int $excluirId = 0): bool {
        $stmt = self::db()->prepare(
            "SELECT COUNT(*) FROM categorias
            WHERE nombre = :nombre AND idCategoria != :excluirId"
        );
        $stmt->execute(['nombre' => $nombre, 'excluirId' => $excluirId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
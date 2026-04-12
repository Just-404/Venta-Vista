<?php

namespace app\models;

use app\core\Model;
use PDO;

class Vendedor extends Model {

    public static function obtenerTodos(): array {
        $sql = "SELECT v.*, u.nombreUsuario, u.email, u.activo
                FROM vendedores v
                JOIN usuarios u ON v.idUsuario = u.idUsuario";
 
        return self::db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorId(int $id): array|false {
        $sql = "SELECT v.*, u.nombreUsuario, u.email, u.activo
                FROM vendedores v
                JOIN usuarios u ON v.idUsuario = u.idUsuario
                WHERE v.idVendedor = :id";
 
        $stmt = self::db()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function obtenerPorUsuario(int $idUsuario): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM vendedores WHERE idUsuario = :idUsuario"
        );
        $stmt->execute(['idUsuario' => $idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    /*
      $data = ['nombre','apellidos','cedula','telefono','idUsuario']
     */
    public static function crear(array $data): bool {
        $sql = "INSERT INTO vendedores
                    (nombre, apellidos, cedula, telefono, idUsuario)
                VALUES
                    (:nombre, :apellidos, :cedula, :telefono, :idUsuario)";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    public static function actualizar(array $data): bool {
        $sql = "UPDATE vendedores
                SET nombre    = :nombre,
                    apellidos = :apellidos,
                    cedula    = :cedula,
                    telefono  = :telefono
                WHERE idVendedor = :idVendedor";
 
        return self::db()->prepare($sql)->execute($data);
    }
 
    public static function eliminar(int $id): bool {
        $stmt = self::db()->prepare(
            "DELETE FROM vendedores WHERE idVendedor = :id"
        );
        return $stmt->execute(['id' => $id]);
    }
 
    /**
     * Resumen total por vendedor.
     * Usa la vista v_ventas_vendedor (corregida en 001_fix_ventas_vendedor.sql).
     * Cadena: vendedores → productos → detalle_pedido → pedidos
     */
    public static function obtenerResumenVentas(): array {
        return self::db()
            ->query("SELECT * FROM v_ventas_vendedor ORDER BY montoTotal DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function buscarPorCedula(string $cedula): array|false {
        $stmt = self::db()->prepare(
            "SELECT * FROM vendedores WHERE cedula = :cedula"
        );
        $stmt->execute(['cedula' => $cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Serie mensual por vendedor para el gráfico de líneas del reporte.
     * Usa la vista v_ventas_vendedor_mensual (creada en 001_fix_ventas_vendedor.sql).
     * Resultado: [['vendedor'=>'Carlos M.','mes'=>'2026-03','total'=>4500.00], ...]
     */
    public static function obtenerSerieVentasMensual(int $meses = 6): array {
        $sql = "SELECT vendedor, mes, total
                FROM v_ventas_vendedor_mensual
                WHERE mes >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL :meses MONTH), '%Y-%m')
                ORDER BY mes ASC, vendedor ASC";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':meses', $meses, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

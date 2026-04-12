<?php

namespace app\controllers;
use app\core\Controller;
use app\models\Pedido;
use app\models\Producto;
use app\models\Cliente;
use app\models\Usuario;
use app\core\Database;
use PDO;

class DashboardController extends Controller {

    public function index(): void {
        $this->requireAuth();
        $usuario = $this->usuarioActual();
        $rol = $usuario['rol'] ?? 0;
        $db  = Database::getConnection();
        
        // Clientes no tienen acceso al dashboard
         if (($this->usuarioActual()['rol'] ?? 0) === 3) {
         header('Location: ' . BASE_URL . 'productos');
         exit;
        }
        
        /* ── Ventas del mes ── */
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(total),0) AS monto,
                   COALESCE(SUM(CASE WHEN MONTH(fechaPedido)=MONTH(CURDATE())-1
                                      AND YEAR(fechaPedido)=YEAR(CURDATE())
                                 THEN total ELSE 0 END),0) AS montoMesAnterior
            FROM pedidos
            WHERE estado NOT IN ('Cancelado','Devuelto')
              AND MONTH(fechaPedido)=MONTH(CURDATE())
              AND YEAR(fechaPedido)=YEAR(CURDATE())
        ");
        $stmt->execute();
        $ventasMesRow   = $stmt->fetch(PDO::FETCH_ASSOC);
        $ventasMes      = (float) $ventasMesRow['monto'];
        $ventasMesAnt   = (float) $ventasMesRow['montoMesAnterior'];
        $crecimientoMes = $ventasMesAnt > 0
            ? round((($ventasMes - $ventasMesAnt) / $ventasMesAnt) * 100, 1)
            : null;

        /* ── Pedidos activos (no entregados/cancelados) ── */
        $stmt = $db->query("
            SELECT COUNT(*) AS total,
                   SUM(CASE WHEN DATE(fechaPedido)=CURDATE() THEN 1 ELSE 0 END) AS hoy
            FROM pedidos
            WHERE estado NOT IN ('Entregado','Cancelado','Devuelto')
        ");
        $pedidosRow    = $stmt->fetch(PDO::FETCH_ASSOC);
        $pedidosActivos = (int) $pedidosRow['total'];
        $pedidosHoy     = (int) $pedidosRow['hoy'];

        /* ── Clientes registrados ── */
        $stmt = $db->query("SELECT COUNT(*) AS total FROM clientes");
        $clientesRow         = $stmt->fetch(PDO::FETCH_ASSOC);
        $clientesRegistrados = (int) $clientesRow['total'];
        $clientesEstaSemana  = 0;

        /* ── Productos sin stock ── */
        $stmt = $db->query("SELECT COUNT(*) AS total FROM productos WHERE stock = 0 AND activo = 1");
        $sinStock = (int) $stmt->fetchColumn();

        /* ── Ventas semanales (últimas 4 semanas) ── */
        $stmt = $db->query("
            SELECT
                CONCAT('Semana ', (DATEDIFF(CURDATE(), DATE(fechaPedido)) DIV 7) + 1) AS semana,
                (DATEDIFF(CURDATE(), DATE(fechaPedido)) DIV 7) AS semanaNum,
                COALESCE(SUM(total),0) AS total
            FROM pedidos
            WHERE estado NOT IN ('Cancelado','Devuelto')
              AND fechaPedido >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)
            GROUP BY semanaNum
            ORDER BY semanaNum DESC
            LIMIT 4
        ");
        $rawSemanas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Garantizamos exactamente 4 semanas (S1 a S4) de más antiguo a más reciente
        $mapaVentas = [];
        foreach ($rawSemanas as $s) {
            $mapaVentas[(int)$s['semanaNum']] = (float)$s['total'];
        }
        $ventasSemanales = [];
        for ($i = 3; $i >= 0; $i--) {
            $ventasSemanales[] = [
                'label' => 'Semana ' . (4 - $i),
                'total' => $mapaVentas[$i] ?? 0,
            ];
        }

        /* ── Ventas por categoría ── */
        $stmt = $db->query("
            SELECT c.nombre AS categoria, COALESCE(SUM(dp.subtotal),0) AS total
            FROM detalle_pedido dp
            JOIN productos pr ON dp.idProducto = pr.idProducto
            JOIN categorias c  ON pr.idCategoria = c.idCategoria
            JOIN pedidos p      ON dp.idPedido = p.idPedido
            WHERE p.estado NOT IN ('Cancelado','Devuelto')
            GROUP BY c.idCategoria
            ORDER BY total DESC
        ");
        $ventasCategorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /* ── Pedidos recientes ── */
        $pedidosRecientes = array_slice(Pedido::obtenerTodos(), 0, 5);

        /* ── Totales extra para Admin ── */
        $totalUsuarios  = ($rol === 1) ? count(Usuario::obtenerTodos()) : null;
        $totalProductos = count(Producto::obtenerActivos());

        $datos = [
            'ventasMes'           => $ventasMes,
            'crecimientoMes'      => $crecimientoMes,
            'pedidosActivos'      => $pedidosActivos,
            'pedidosHoy'          => $pedidosHoy,
            'clientesRegistrados' => $clientesRegistrados,
            'clientesEstaSemana'  => $clientesEstaSemana,
            'sinStock'            => $sinStock,
            'ventasSemanales'     => $ventasSemanales,
            'ventasCategorias'    => $ventasCategorias,
            'pedidosRecientes'    => $pedidosRecientes,
            'totalProductos'      => $totalProductos,
            'totalUsuarios'       => $totalUsuarios,
            'flash'               => $this->getFlash(),
            'usuario'             => $usuario,
        ];

        $this->render('dashboard/index', $datos);
    }
    
}

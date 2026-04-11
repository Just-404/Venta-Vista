<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Pedido;
use app\models\Producto;
use app\models\Vendedor;
use app\models\Cliente;

class ReporteController extends Controller {

    // ─── Solo administradores (rol = 1) ────────────────────────
    private function soloAdmin(): void {
        $this->requireAuth();
        $this->requireRol(1);
    }

    // ─── GET /reportes ──────────────────────────────────────────
    public function index(): void {
        $this->soloAdmin();

        $pedidos     = Pedido::obtenerTodos();
        $porVendedor = Vendedor::obtenerResumenVentas();

        // Serie temporal mensual de ventas por vendedor para el gráfico de líneas
        $serieVendedor = Vendedor::obtenerSerieVentasMensual();

        $this->render('reportes/index', [
            'pedidos'        => $pedidos,
            'porVendedor'    => $porVendedor,
            'serieVendedor'  => $serieVendedor,
            'todos'          => Producto::obtenerConRating(),
            'usuario'        => $this->usuarioActual(),
            'flash'          => $this->getFlash(),
        ]);
    }

    // ─── GET /reportes/ventas ───────────────────────────────────
    public function ventas(): void {
        $this->soloAdmin();

        $this->render('reportes/ventas', [
            'pedidos'       => Pedido::obtenerTodos(),
            'porVendedor'   => Vendedor::obtenerResumenVentas(),
            'usuario'       => $this->usuarioActual(),
            'flash'         => $this->getFlash(),
        ]);
    }

    // ─── GET /reportes/productos ────────────────────────────────
    public function productos(): void {
        $this->soloAdmin();

        $this->render('reportes/productos', [
            'destacados' => Producto::obtenerDestacados(10),
            'todos'      => Producto::obtenerConRating(),
            'usuario'    => $this->usuarioActual(),
            'flash'      => $this->getFlash(),
        ]);
    }

    // ─── GET /reportes/clientes ─────────────────────────────────
    public function clientes(): void {
        $this->soloAdmin();

        $this->render('reportes/clientes', [
            'clientes' => Cliente::obtenerTodos(),
            'usuario'  => $this->usuarioActual(),
            'flash'    => $this->getFlash(),
        ]);
    }

    // ─── GET /reportes/exportar?tipo=ventas|productos|clientes&formato=excel|pdf ──
    public function exportar(): void {
        $this->soloAdmin();

        $tipo    = $this->get('tipo',    'ventas');
        $formato = $this->get('formato', 'excel');

        // Recoge los datos según el tipo solicitado
        switch ($tipo) {
            case 'productos':
                $datos    = Producto::obtenerConRating();
                $titulo   = 'Reporte de Productos';
                $columnas = ['Producto', 'Categoría', 'Precio', 'Stock', 'Promedio', 'Reseñas'];
                $filas    = array_map(fn($r) => [
                    $r['nombre'],
                    $r['categoria'],
                    'RD$ ' . number_format($r['precio'], 2),
                    $r['stock'],
                    $r['promedio'] ?? '0.0',
                    $r['totalResenas'] ?? 0,
                ], $datos);
                break;

            case 'clientes':
                $datos    = Cliente::obtenerTodos();
                $titulo   = 'Reporte de Clientes';
                $columnas = ['Nombre', 'Cédula', 'Teléfono', 'Email', 'Estado'];
                $filas    = array_map(fn($r) => [
                    $r['nombre'] . ' ' . $r['apellidos'],
                    $r['cedula']       ?? '—',
                    $r['telefono']     ?? '—',
                    $r['emailUsuario'] ?? '—',
                    $r['activo'] ? 'Activo' : 'Inactivo',
                ], $datos);
                break;

            default: // ventas
                $datos    = Pedido::obtenerTodos();
                $titulo   = 'Reporte de Ventas';
                $columnas = ['Número', 'Cliente', 'Total', 'Estado', 'Fecha'];
                $filas    = array_map(fn($r) => [
                    $r['numeroPedido'],
                    $r['cliente'],
                    'RD$ ' . number_format($r['total'], 2),
                    $r['estado'],
                    date('d/m/Y', strtotime($r['fechaPedido'])),
                ], $datos);
                break;
        }

        if ($formato === 'pdf') {
            $this->exportarPdf($titulo, $columnas, $filas);
        } else {
            $this->exportarExcel($titulo, $columnas, $filas);
        }
    }

    // ─── Exportar a Excel (CSV con UTF-8 BOM para compatibilidad) ──
    private function exportarExcel(string $titulo, array $columnas, array $filas): void {
        $nombre = strtolower(str_replace(' ', '_', $titulo)) . '_' . date('Ymd') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        // BOM para que Excel lo abra bien
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Encabezado del reporte
        fputcsv($out, [$titulo . ' — ' . date('d/m/Y H:i')]);
        fputcsv($out, []);
        fputcsv($out, $columnas);

        foreach ($filas as $fila) {
            fputcsv($out, $fila);
        }

        fclose($out);
        exit;
    }

    // ─── Exportar a PDF (HTML imprimible sin dependencias externas) ──
    private function exportarPdf(string $titulo, array $columnas, array $filas): void {
        $nombre = strtolower(str_replace(' ', '_', $titulo)) . '_' . date('Ymd') . '.html';

        header('Content-Type: text/html; charset=UTF-8');

        $th = implode('', array_map(fn($c) => "<th>{$c}</th>", $columnas));
        $tbody = '';
        foreach ($filas as $i => $fila) {
            $bg  = ($i % 2 === 0) ? '#f9f9fb' : '#ffffff';
            $tds = implode('', array_map(fn($v) => "<td>" . htmlspecialchars((string)$v) . "</td>", $fila));
            $tbody .= "<tr style='background:{$bg}'>{$tds}</tr>";
        }

        $total = count($filas);
        $fecha = date('d/m/Y H:i');

        echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>{$titulo}</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; color: #1a1f2e; background: #fff; padding: 32px; }
    .header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px; border-bottom: 3px solid #e85d26; padding-bottom: 16px; }
    .header-left h1 { font-size: 1.6rem; color: #1e2a4a; }
    .header-left p { color: #6b7494; font-size: .85rem; margin-top: 4px; }
    .header-right { font-size: .8rem; color: #6b7494; text-align: right; }
    .badge { display: inline-block; background: #1e2a4a; color: #fff; padding: 4px 12px; border-radius: 99px; font-size: .75rem; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; font-size: .875rem; }
    thead th { background: #1e2a4a; color: #fff; padding: 10px 14px; text-align: left; font-weight: 600; }
    tbody td { padding: 9px 14px; border-bottom: 1px solid #eef1f8; }
    .footer { margin-top: 24px; text-align: center; font-size: .75rem; color: #9ca3af; }
    @media print { body { padding: 0; } .no-print { display: none; } }
    .no-print { text-align: center; margin-bottom: 20px; }
    .btn-imprimir { background: #e85d26; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-size: .9rem; cursor: pointer; font-family: inherit; font-weight: 600; }
  </style>
</head>
<body>
  <div class="no-print">
    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>
  </div>
  <div class="header">
    <div class="header-left">
      <h1>{$titulo}</h1>
      <p>Generado el {$fecha}</p>
    </div>
    <div class="header-right">
      <span class="badge">Venta Vista</span><br>
      <span style="margin-top:6px;display:block">{$total} registros</span>
    </div>
  </div>
  <table>
    <thead><tr>{$th}</tr></thead>
    <tbody>{$tbody}</tbody>
  </table>
  <div class="footer">© {$fecha} — Ventas Catálogo PRO — Todos los derechos reservados</div>
</body>
</html>
HTML;
        exit;
    }
}

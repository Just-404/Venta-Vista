<?php

namespace app\helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper {

    private static function mailer(): PHPMailer {
        $mail = new PHPMailer(true);

        $smtpHost = $_ENV['MAIL_HOST'] ?? null;

        if ($smtpHost) {
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USER'] ?? '';
            $mail->Password   = $_ENV['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPT'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) ($_ENV['MAIL_PORT'] ?? 587);
        }

        $mail->setFrom(
            $_ENV['MAIL_FROM']      ?? 'noreply@ventavista.do',
            $_ENV['MAIL_FROM_NAME'] ?? 'Venta Vista'
        );
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);

        return $mail;
    }

    /**
     * Envía un correo genérico.
     * Retorna true si se envió, false si falló o no hay configuración SMTP.
     */
    public static function enviar(string $destinatario, string $nombre, string $asunto, string $cuerpoHtml): bool {
        try {
            $mail = self::mailer();
            $mail->addAddress($destinatario, $nombre);
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpoHtml;
            $mail->AltBody = strip_tags($cuerpoHtml);
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('[MailHelper] Error enviando a ' . $destinatario . ': ' . $e->getMessage());
            return false;
        }
    }

    /** Notifica a admin/vendedor sobre un nuevo pedido */
    public static function pedidoNuevo(string $email, string $nombre, string $numeroPedido, float $total): bool {
        $asunto = "🛍️ Nuevo Pedido: $numeroPedido";
        $cuerpo = self::plantilla(
            'Nuevo Pedido Recibido',
            "<p>Hola <strong>" . htmlspecialchars($nombre) . "</strong>,</p>
             <p>Se ha registrado un nuevo pedido en el sistema.</p>
             <table style='border-collapse:collapse;width:100%;margin:16px 0'>
               <tr><td style='padding:10px 12px;border:1px solid #e2e8f0;background:#f8fafc'><strong>Número</strong></td>
                   <td style='padding:10px 12px;border:1px solid #e2e8f0'>$numeroPedido</td></tr>
               <tr><td style='padding:10px 12px;border:1px solid #e2e8f0;background:#f8fafc'><strong>Total</strong></td>
                   <td style='padding:10px 12px;border:1px solid #e2e8f0'>RD\$ " . number_format($total, 2) . "</td></tr>
             </table>
             <p>Ingresa al sistema para gestionar el pedido.</p>"
        );
        return self::enviar($email, $nombre, $asunto, $cuerpo);
    }

    /** Notifica al cliente sobre un cambio de estado en su pedido */
    public static function estadoPedido(string $email, string $nombre, string $numeroPedido, string $estado): bool {
        $asunto = "📦 Tu pedido $numeroPedido — Estado: $estado";
        $cuerpo = self::plantilla(
            'Actualización de tu Pedido',
            "<p>Hola <strong>" . htmlspecialchars($nombre) . "</strong>,</p>
             <p>Tu pedido <strong>$numeroPedido</strong> ha cambiado de estado:</p>
             <p style='font-size:20px;font-weight:700;text-align:center;padding:18px;
                        background:#eff6ff;border-radius:8px;color:#1d4ed8'>$estado</p>
             <p>Ingresa a tu cuenta para ver los detalles de tu pedido.</p>"
        );
        return self::enviar($email, $nombre, $asunto, $cuerpo);
    }

    /** Alerta de stock bajo a admin/vendedor */
    public static function alertaStock(string $email, string $nombre, string $producto, int $stock): bool {
        $asunto = "⚠️ Stock Bajo: $producto";
        $cuerpo = self::plantilla(
            'Alerta de Stock Bajo',
            "<p>Hola <strong>" . htmlspecialchars($nombre) . "</strong>,</p>
             <p>El siguiente producto tiene stock crítico:</p>
             <table style='border-collapse:collapse;width:100%;margin:16px 0'>
               <tr><td style='padding:10px 12px;border:1px solid #e2e8f0;background:#f8fafc'><strong>Producto</strong></td>
                   <td style='padding:10px 12px;border:1px solid #e2e8f0'>" . htmlspecialchars($producto) . "</td></tr>
               <tr><td style='padding:10px 12px;border:1px solid #e2e8f0;background:#f8fafc'><strong>Stock actual</strong></td>
                   <td style='padding:10px 12px;border:1px solid #e2e8f0;color:#dc2626'><strong>$stock unidades</strong></td></tr>
             </table>
             <p>Por favor, actualiza el inventario a la brevedad.</p>"
        );
        return self::enviar($email, $nombre, $asunto, $cuerpo);
    }

    private static function plantilla(string $titulo, string $contenido): string {
        $anio = date('Y');
        return "<!DOCTYPE html>
<html lang='es'>
<head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'></head>
<body style='margin:0;padding:0;font-family:Arial,sans-serif;background:#f1f5f9'>
  <div style='max-width:600px;margin:32px auto;background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.08)'>
    <div style='background:#2563eb;padding:28px 32px;text-align:center'>
      <h1 style='color:#fff;margin:0;font-size:22px;letter-spacing:.5px'>Venta Vista</h1>
    </div>
    <div style='padding:32px;color:#334155;line-height:1.6'>
      <h2 style='margin-top:0;color:#1e293b;font-size:18px'>$titulo</h2>
      $contenido
    </div>
    <div style='background:#f8fafc;padding:18px 32px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0'>
      © $anio Venta Vista &mdash; Mensaje automático, por favor no responder.
    </div>
  </div>
</body>
</html>";
    }
}

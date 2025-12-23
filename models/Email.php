<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/constantes.php';
require_once __DIR__ . '/../config/smtp.php';
require_once 'controllers/AuthController.php';
class Email
{
    private $mailer;

    public function __construct()
    {
        require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
        require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

        $this->mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        $this->configurarMailer();
    }

    private function configurarMailer()
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USER;
            $this->mailer->Password = SMTP_PASS;
            $this->mailer->SMTPSecure = SMTP_SECURE;
            $this->mailer->Port = SMTP_PORT;
            $this->mailer->SMTPDebug = SMTP_DEBUG;
            $this->mailer->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $this->mailer->CharSet = 'UTF-8';
        } catch (Exception $e) {
            error_log("Error PHPMailer: " . $e->getMessage());
        }
    }

    public function enviarVerificacion($email, $nombre, $token)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $nombre);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Verifica tu cuenta - ' . SITE_NAME;
            $verification_link = SITE_URL . "verify.php?token=" . urlencode($token);
            $this->mailer->Body = $this->crearTemplateVerificacion($nombre, $verification_link);
            $this->mailer->AltBody = "Hola $nombre,\n\nVerifica tu cuenta: $verification_link\n\nVálido por 24 horas.";

            if ($this->mailer->send()) {
                return true;
            }
            $this->logEmail($email, "Verificación", $verification_link);
            return false;

        } catch (Exception $e) {
            error_log("Error enviando verificación: " . $this->mailer->ErrorInfo);

            $this->logEmail($email, "Verificación (Fallback por Error)", $verification_link);
            return true;
        }
    }

    private function logEmail($to, $subject, $content)
    {
        $logFile = __DIR__ . '/../email_log.txt';
        $logEntry = date('Y-m-d H:i:s') . " | To: $to | Subject: $subject | Content: $content" . PHP_EOL . str_repeat('-', 50) . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    public function enviarCodigoRecuperacion($email, $nombre, $codigo)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $nombre);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Código de recuperación - ' . SITE_NAME;
            $this->mailer->Body = $this->crearTemplateRecuperacion($nombre, $codigo);
            $this->mailer->AltBody = "Código de recuperación: $codigo\nVálido por 15 minutos.";

            if ($this->mailer->send()) {
                return true;
            }
            $this->logEmail($email, "Recuperación", $codigo);
            return false;

        } catch (Exception $e) {
            error_log("Error enviando recuperación: " . $this->mailer->ErrorInfo);
            $this->logEmail($email, "Recuperación (Fallback)", $codigo);
            return true;
        }
    }

    private function crearTemplateVerificacion($nombre, $enlace)
    {
        return '
        <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#1e1e1e;color:#fff;border-radius:10px;border:2px solid #28a745;">
            <div style="background:#000;color:#28a745;padding:20px;text-align:center;">
                <h1 style="margin:0;">' . SITE_NAME . '</h1>
            </div>
            <div style="padding:30px;">
                <h2 style="color:#28a745;">¡Hola ' . htmlspecialchars($nombre) . '!</h2>
                <p>Para activar tu cuenta, haz clic en el botón:</p>
                <div style="text-align:center;margin:30px 0;">
                    <a href="' . $enlace . '" style="background:#28a745;color:white;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
                        VERIFICAR CUENTA
                    </a>
                </div>
                <p>O copia este enlace:</p>
                <div style="background:#2d2d2d;padding:15px;border-radius:5px;margin:15px 0;">
                    <code style="color:#28a745;">' . $enlace . '</code>
                </div>
                <p>Este enlace expira en 24 horas.</p>
            </div>
        </div>';
    }

    private function crearTemplateRecuperacion($nombre, $codigo)
    {
        return '
        <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#1e1e1e;color:#fff;border-radius:10px;border:2px solid #dc3545;">
            <div style="background:#000;color:#dc3545;padding:20px;text-align:center;">
                <h1 style="margin:0;">Recuperación de Contraseña</h1>
            </div>
            <div style="padding:30px;">
                <h2 style="color:#dc3545;">¡Hola ' . htmlspecialchars($nombre) . '!</h2>
                <p>Usa este código de 6 dígitos:</p>
                <div style="font-size:42px;font-weight:bold;color:#dc3545;letter-spacing:15px;text-align:center;padding:20px;background:#2d2d2d;border-radius:5px;margin:20px 0;">
                    ' . $codigo . '
                </div>
                <p>Válido por 15 minutos.</p>
            </div>
        </div>';
    }

    public function enviarFactura($email, $nombre, $plan, $monto, $transaccionId)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $nombre);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Factura de Suscripción - ' . SITE_NAME;
            $this->mailer->Body = $this->crearTemplateFactura($nombre, $plan, $monto, $transaccionId);
            $this->mailer->AltBody = "Hola $nombre, gracias por tu compra.\nPlan: $plan\nMonto: $$monto\nTransacción: $transaccionId";

            if ($this->mailer->send()) {
                return true;
            }
            $this->logEmail($email, "Factura Fallida", "ID: $transaccionId");
            return false;
        } catch (Exception $e) {
            error_log("Error enviando factura: " . $this->mailer->ErrorInfo);
            $this->logEmail($email, "Factura Error", "ID: $transaccionId");
            return true;
        }
    }

    private function crearTemplateFactura($nombre, $plan, $monto, $transaccionId)
    {
        $fecha = date('d/m/Y H:i');
        return '
        <div style="font-family:\'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; max-width:600px; margin:0 auto; background-color:#121416; color:#e0e0e0; border-radius:12px; overflow:hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #198754 0%, #157347 100%); padding: 30px; text-align: center;">
                <h1 style="margin:0; color:white; font-size: 24px;">¡Gracias por tu suscripción!</h1>
                <p style="margin: 10px 0 0; color: rgba(255,255,255,0.8);">Tu pago ha sido procesado exitosamente</p>
            </div>

            <!-- Body -->
            <div style="padding: 40px 30px; background-color: #212529;">
                <p style="margin-top: 0;">Hola <strong>' . htmlspecialchars($nombre) . '</strong>,</p>
                <p>Hemos recibido confirmación de tu pago. A continuación los detalles de tu transacción:</p>

                <div style="background-color: #2c3034; border-radius: 8px; padding: 20px; margin: 25px 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; color: #adb5bd;">Plan Suscrito:</td>
                            <td style="padding: 8px 0; text-align: right; color: #fff; font-weight: bold; text-transform: capitalize;">' . htmlspecialchars($plan) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #adb5bd;">ID Transacción:</td>
                            <td style="padding: 8px 0; text-align: right; color: #fff; font-family: monospace;">' . htmlspecialchars($transaccionId) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #adb5bd;">Fecha:</td>
                            <td style="padding: 8px 0; text-align: right; color: #fff;">' . $fecha . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-top: 1px solid #495057; margin-top: 8px; font-weight: bold; color: #fff;">TOTAL PAGADO</td>
                            <td style="padding: 8px 0; border-top: 1px solid #495057; margin-top: 8px; text-align: right; font-weight: bold; color: #198754; font-size: 18px;">$' . number_format($monto, 2) . ' USD</td>
                        </tr>
                    </table>
                </div>

                <p style="font-size: 0.9em; color: #adb5bd; line-height: 1.5;">
                    Ahora tienes acceso completo a las características de tu nuevo plan. 
                    Puedes descargar este comprobante en cualquier momento desde tu perfil.
                </p>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="' . SITE_URL . '" style="background-color: #0d6efd; color: white; padding: 12px 30px; text-decoration: none; border-radius: 50px; font-weight: bold; display: inline-block;">Ir a Mis Cursos</a>
                </div>
            </div>

            <!-- Footer -->
            <div style="background-color: #1a1d20; padding: 20px; text-align: center; font-size: 12px; color: #6c757d;">
                <p style="margin: 0;">&copy; ' . date('Y') . ' BolaNet Learning Code. Todos los derechos reservados.</p>
                <p style="margin: 5px 0 0;">Este es un correo automático, por favor no respondas a este mensaje.</p>
            </div>
        </div>';
    }
}
?>
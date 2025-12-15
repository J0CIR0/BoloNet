<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/constantes.php';
require_once __DIR__ . '/../config/smtp.php';

class Email {
    private $mailer;
    
    public function __construct() {
        require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
        require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
        
        $this->mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        $this->configurarMailer();
    }
    
    private function configurarMailer() {
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
    
    public function enviarVerificacion($email, $nombre, $token) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $nombre);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Verifica tu cuenta - ' . SITE_NAME;
            
            $verification_link = SITE_URL . "verify.php?token=" . $token;
            
            $this->mailer->Body = $this->crearTemplateVerificacion($nombre, $verification_link);
            $this->mailer->AltBody = "Hola $nombre,\n\nVerifica tu cuenta: $verification_link\n\nVálido por 24 horas.";
            
            if ($this->mailer->send()) {
                return true;
            }
            return false;
            
        } catch (Exception $e) {
            error_log("Error enviando verificación: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
    
    public function enviarCodigoRecuperacion($email, $nombre, $codigo) {
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
            return false;
            
        } catch (Exception $e) {
            error_log("Error enviando recuperación: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
    
    private function crearTemplateVerificacion($nombre, $enlace) {
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
    
    private function crearTemplateRecuperacion($nombre, $codigo) {
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
}
?>
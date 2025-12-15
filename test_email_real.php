<?php
// test_email_real.php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';
require_once 'config/smtp.php';

echo "<h2>🚀 Prueba de envío REAL de email</h2>";

// 1. Verificar archivos PHPMailer
echo "<h3>1. Verificando PHPMailer...</h3>";
$files = [
    'vendor/PHPMailer/src/PHPMailer.php',
    'vendor/PHPMailer/src/SMTP.php',
    'vendor/PHPMailer/src/Exception.php'
];

$all_files_exist = true;
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file<br>";
    } else {
        echo "❌ $file - FALTANTE<br>";
        $all_files_exist = false;
    }
}

if (!$all_files_exist) {
    echo "<p style='color:red;'>⚠️ Falta(n) archivo(s) de PHPMailer</p>";
    exit;
}

// 2. Cargar PHPMailer manualmente
echo "<h3>2. Cargando PHPMailer...</h3>";
require_once 'vendor/PHPMailer/src/Exception.php';
require_once 'vendor/PHPMailer/src/PHPMailer.php';
require_once 'vendor/PHPMailer/src/SMTP.php';

try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    echo "✅ PHPMailer instanciado<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
    exit;
}

// 3. Configurar y enviar email de prueba
echo "<h3>3. Enviando email de prueba...</h3>";

try {
    // Configuración SMTP
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port       = SMTP_PORT;
    $mail->SMTPDebug  = SMTP_DEBUG; // 2 para ver todo
    
    // Remitente y destinatario
    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail->addAddress('clarosrocajosue@gmail.com', 'Josue Claros'); // A TI MISMO
    
    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Prueba BoloNet - ' . date('H:i:s');
    $mail->Body    = '<h1>¡Prueba exitosa!</h1><p>PHPMailer funciona correctamente.</p>';
    $mail->AltBody = 'Prueba exitosa - PHPMailer funcionando';
    
    // Enviar
    if ($mail->send()) {
        echo "<p style='color:green;font-weight:bold;'>✅ ¡Email enviado CORRECTAMENTE!</p>";
        echo "<p>Revisa tu bandeja de entrada en <strong>clarosrocajosue@gmail.com</strong></p>";
        echo "<p>También revisa la carpeta <strong>SPAM</strong> si no lo ves.</p>";
    } else {
        echo "<p style='color:red;'>❌ Error al enviar: " . $mail->ErrorInfo . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// 4. Mostrar configuración usada
echo "<h3>4. Configuración usada:</h3>";
echo "<pre style='background:#2d2d2d;color:#fff;padding:10px;'>";
echo "SMTP_HOST: " . SMTP_HOST . "\n";
echo "SMTP_USER: " . SMTP_USER . "\n";
echo "SMTP_FROM: " . SMTP_FROM . "\n";
echo "SMTP_PORT: " . SMTP_PORT . "\n";
echo "SMTP_SECURE: " . SMTP_SECURE . "\n";
echo "</pre>";

// 5. Consejos si no llega
echo "<h3>5. Si no llega el email:</h3>";
echo "<ul>";
echo "<li>Revisa la carpeta <strong>SPAM</strong> de Gmail</li>";
echo "<li>Verifica que la contraseña de aplicación sea correcta</li>";
echo "<li>Asegúrate de tener internet</li>";
echo "<li>Prueba desactivar temporalmente el firewall/antivirus</li>";
echo "</ul>";

echo "<hr><p><a href='register.php'>Volver al registro</a></p>";
?>
<?php
echo "<h2>Instalando PHPMailer...</h2>";

// Directorio destino
$vendor_dir = __DIR__ . '/vendor';
$phpmailer_dir = $vendor_dir . '/PHPMailer/src';

// Crear directorios
if (!is_dir($vendor_dir)) {
    mkdir($vendor_dir, 0755, true);
    echo "<p>✓ Directorio 'vendor' creado</p>";
}

if (!is_dir($phpmailer_dir)) {
    mkdir($phpmailer_dir, 0755, true);
    echo "<p>✓ Directorio 'PHPMailer/src' creado</p>";
}

// Archivos esenciales
$files = [
    'PHPMailer.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php',
    'SMTP.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php', 
    'Exception.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php'
];

$success = 0;
foreach ($files as $file => $url) {
    $content = @file_get_contents($url);
    if ($content) {
        file_put_contents($phpmailer_dir . '/' . $file, $content);
        echo "<p>✓ $file descargado</p>";
        $success++;
    } else {
        echo "<p style='color:red'>✗ Error descargando $file</p>";
    }
}

// Crear autoload simple
$autoload_content = '<?php
spl_autoload_register(function ($class) {
    if (strpos($class, "PHPMailer\\\\PHPMailer\\\\") === 0) {
        $file = __DIR__ . "/" . str_replace("PHPMailer\\\\PHPMailer\\\\", "", $class) . ".php";
        if (file_exists($file)) {
            require $file;
        }
    }
});
?>';

file_put_contents($vendor_dir . '/PHPMailer/autoload.php', $autoload_content);
echo "<p>✓ Autoload creado</p>";

// Verificar instalación
echo "<hr><h3>Verificación:</h3>";
$check_files = ['PHPMailer.php', 'SMTP.php', 'Exception.php'];
foreach ($check_files as $file) {
    $path = $phpmailer_dir . '/' . $file;
    if (file_exists($path)) {
        $size = filesize($path);
        echo "<p>✓ $file existe ($size bytes)</p>";
    } else {
        echo "<p style='color:red'>✗ $file NO existe</p>";
    }
}

if ($success == 3) {
    echo "<p style='color:green;font-weight:bold;padding:10px;background:#d4edda;'>✅ PHPMailer instalado correctamente!</p>";
    echo "<p>Ahora puedes <strong>eliminar este archivo</strong> (install_phpmailer.php).</p>";
} else {
    echo "<p style='color:red;font-weight:bold;'>⚠️ Problemas con la instalación</p>";
}
?>
<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';

if (!isset($_GET['token'])) {
    $_SESSION['error'] = 'Token no válido';
    header('Location: index.php');
    exit();
}

require_once 'models/Usuario.php';
$usuario = new Usuario();

$token = $_GET['token'];
$result = $usuario->verificarUsuario($token);

if ($result['success']) {
    $_SESSION['success'] = 'Cuenta verificada exitosamente. Ya puedes iniciar sesión.';
} else {
    $_SESSION['error'] = 'Enlace inválido o expirado';
}

header('Location: index.php');
exit();
?>
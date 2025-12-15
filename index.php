<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

require_once 'controllers/AuthController.php';

$controller = new AuthController();
$controller->login();
?>
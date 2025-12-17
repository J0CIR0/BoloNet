<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
require_once 'controllers/RolPermisoController.php';
$controller = new RolPermisoController();
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;
if ($action == 'manage' && $id) {
    $controller->manage($id);
} else {
    $controller->index();
}
?>
<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'controllers/PersonaController.php';

$controller = new PersonaController();
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($action == 'create') {
    $controller->create();
} elseif ($action == 'edit' && $id) {
    $controller->edit($id);
} elseif ($action == 'delete' && $id) {
    $controller->delete($id);
} else {
    $controller->index();
}
?>
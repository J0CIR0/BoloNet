<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'controllers/CursoController.php';

$controller = new CursoController();
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($action == 'create') {
    $controller->create();
} elseif ($action == 'edit' && $id) {
    $controller->edit($id);
} elseif ($action == 'delete' && $id) {
    $controller->delete($id);
} elseif ($action == 'inscribir') {
    $controller->inscribir();
} elseif ($action == 'mis_cursos') {
    $controller->misCursos();
} elseif ($action == 'gestionar' && $id) {
    $controller->gestionarInscripciones($id);
} else {
    $controller->index();
}
?>
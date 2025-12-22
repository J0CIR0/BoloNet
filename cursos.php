<?php
// cursos.php
// Este archivo ahora solo sirve para redirigir enlaces viejos al nuevo Router

$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? '&id=' . $_GET['id'] : '';
$id_curso = isset($_GET['id_curso']) ? '&id_curso=' . $_GET['id_curso'] : '';

// Redirigimos la petición al index.php (El nuevo jefe)
// Ejemplo: cursos.php?action=create  -->  index.php?controller=Curso&action=create
header("Location: index.php?controller=Curso&action=" . $action . $id . $id_curso);
exit();
?>
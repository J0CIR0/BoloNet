<?php
require_once __DIR__ . '/../config/conexion.php';
class RolController {
    private $rol;
    private $usuario;
    public function __construct() {
        require_once __DIR__ . '/../models/Rol.php';
        require_once __DIR__ . '/../models/Usuario.php';
        $this->rol = new Rol();
        $this->usuario = new Usuario();
    }
    public function checkPermission($permiso) {
        if (!isset($_SESSION['user_id']) || !$this->usuario->hasPermission($_SESSION['user_id'], $permiso)) {
            $_SESSION['error'] = 'No tienes permisos para esta acción';
            header('Location: dashboard.php');
            exit();
        }
    }
    public function index() {
        $this->checkPermission('ver_roles');
        $roles = $this->rol->getAll();
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/roles/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function create() {
        $this->checkPermission('crear_rol');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $this->rol->create($nombre, $descripcion);
            header('Location: roles.php');
            exit();
        }
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/roles/create.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function edit($id) {
        $this->checkPermission('editar_rol');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $this->rol->update($id, $nombre, $descripcion);
            header('Location: roles.php');
            exit();
        }
        $rol = $this->rol->getById($id);
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/roles/edit.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function delete($id) {
        $this->checkPermission('eliminar_rol');
        $rol = $this->rol->getById($id);
        if (!$rol) {
            $_SESSION['error'] = 'Rol no encontrado';
            header('Location: roles.php');
            exit();
        }
        if (in_array($rol['nombre'], ['registro', 'adm', 'estudiante'])) {
            $_SESSION['error'] = 'No se puede eliminar este rol del sistema';
            header('Location: roles.php');
            exit();
        }
        $this->rol->delete($id);
        header('Location: roles.php');
        exit();
    }
}
?>
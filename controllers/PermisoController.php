<?php
require_once __DIR__ . '/../config/conexion.php';
class PermisoController {
    private $permiso;
    private $usuario;
    public function __construct() {
        require_once __DIR__ . '/../models/Permiso.php';
        require_once __DIR__ . '/../models/Usuario.php';
        $this->permiso = new Permiso();
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
        $this->checkPermission('ver_permisos');
        $permisos = $this->permiso->getAll();
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/permisos/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function create() {
        $this->checkPermission('ver_permisos');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $modulo = $_POST['modulo'];
            $descripcion = $_POST['descripcion'];
            $this->permiso->create($nombre, $modulo, $descripcion);
            header('Location: permisos.php');
            exit();
        }
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/permisos/create.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function edit($id) {
        $this->checkPermission('ver_permisos');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $modulo = $_POST['modulo'];
            $descripcion = $_POST['descripcion'];
            $this->permiso->update($id, $nombre, $modulo, $descripcion);
            header('Location: permisos.php');
            exit();
        }
        $permiso = $this->permiso->getById($id);
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/permisos/edit.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function delete($id) {
        $this->checkPermission('ver_permisos');
        $this->permiso->delete($id);
        header('Location: permisos.php');
        exit();
    }
}
?>
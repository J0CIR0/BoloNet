<?php
require_once __DIR__ . '/../config/conexion.php';

class RolPermisoController {
    private $rol;
    private $permiso;
    private $rolPermiso;
    private $usuario;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Rol.php';
        require_once __DIR__ . '/../models/Permiso.php';
        require_once __DIR__ . '/../models/RolPermiso.php';
        require_once __DIR__ . '/../models/Usuario.php';
        $this->rol = new Rol();
        $this->permiso = new Permiso();
        $this->rolPermiso = new RolPermiso();
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
        $this->checkPermission('asignar_permisos');
        $roles = $this->rol->getAll();
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/rolpermiso/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    public function manage($rol_id) {
        $this->checkPermission('asignar_permisos');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $permisos = $_POST['permisos'] ?? [];
            $this->rolPermiso->updatePermisos($rol_id, $permisos);
            $_SESSION['success'] = 'Permisos actualizados';
            header("Location: rolpermiso.php?action=manage&id=$rol_id");
            exit();
        }
        
        $rol = $this->rol->getById($rol_id);
        $permisos = $this->permiso->getAll();
        $permisos_asignados = $this->rolPermiso->getPermisoIdsByRol($rol_id);
        
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/rolpermiso/manage.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
?>
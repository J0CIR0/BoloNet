<?php
require_once __DIR__ . '/../config/conexion.php';

class UsuarioController {
    private $usuario;
    private $rol;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Usuario.php';
        require_once __DIR__ . '/../models/Rol.php';
        $this->usuario = new Usuario();
        $this->rol = new Rol();
    }
    
    public function checkPermission($permiso) {
        if (!isset($_SESSION['user_id']) || !$this->usuario->hasPermission($_SESSION['user_id'], $permiso)) {
            $_SESSION['error'] = 'No tienes permisos para esta acción';
            header('Location: dashboard.php');
            exit();
        }
    }
    
    public function index() {
        $this->checkPermission('ver_usuarios');
        $usuarios = $this->usuario->getAll();
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/usuarios/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    public function create() {
        $this->checkPermission('crear_usuario');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'rol_id' => $_POST['rol_id']
            ];
            
            if ($this->usuario->create($data)) {
                $_SESSION['success'] = 'Usuario creado exitosamente';
                header('Location: usuarios.php');
                exit();
            } else {
                $_SESSION['error'] = 'Error al crear usuario';
            }
        }
        
        $roles = $this->rol->getAll();
        $title = 'Nuevo Usuario';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/usuarios/create.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    public function edit($id) {
        $this->checkPermission('editar_usuario');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'rol_id' => $_POST['rol_id']
            ];
            
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }
            
            if ($this->usuario->update($id, $data)) {
                $_SESSION['success'] = 'Usuario actualizado exitosamente';
                header('Location: usuarios.php');
                exit();
            } else {
                $_SESSION['error'] = 'Error al actualizar usuario';
            }
        }
        
        $usuario_data = $this->usuario->getById($id); // Cambiado a $usuario_data
        if (!$usuario_data) {
            $_SESSION['error'] = 'Usuario no encontrado';
            header('Location: usuarios.php');
            exit();
        }
        
        $roles = $this->rol->getAll();
        $title = 'Editar Usuario';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/usuarios/edit.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function delete($id) {
        $this->checkPermission('eliminar_usuario');
        
        $usuario_a_eliminar = $this->usuario->getById($id);
        
        if (!$usuario_a_eliminar) {
            $_SESSION['error'] = 'Usuario no encontrado';
            header('Location: usuarios.php');
            exit();
        }
        
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'No puedes eliminarte a ti mismo';
            header('Location: usuarios.php');
            exit();
        }
        
        if ($usuario_a_eliminar['rol_nombre'] == 'administrador') {
            $_SESSION['error'] = 'No se puede eliminar al administrador';
            header('Location: usuarios.php');
            exit();
        }
        
        if ($this->usuario->delete($id)) {
            $_SESSION['success'] = 'Usuario eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar usuario';
        }
        
        header('Location: usuarios.php');
        exit();
    }
    
}
?>
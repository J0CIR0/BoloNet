<?php
require_once __DIR__ . '/../config/conexion.php';
class UsuarioController
{
    private $usuario;
    private $rol;
    public function __construct()
    {
        require_once __DIR__ . '/../models/Usuario.php';
        require_once __DIR__ . '/../models/Rol.php';
        $this->usuario = new Usuario();
        $this->rol = new Rol();
    }
    public function checkPermission($permiso)
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión';
            header('Location: index.php');
            exit();
        }
        if (!$this->usuario->hasPermission($_SESSION['user_id'], $permiso)) {
            $_SESSION['error'] = 'No tienes permisos para esta acción';
            header('Location: dashboard.php');
            exit();
        }
    }
    public function index()
    {
        $this->checkPermission('ver_usuarios');
        $usuarios = $this->usuario->getAll();
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/usuarios/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    public function create()
    {
        $this->checkPermission('crear_usuario');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'ci' => $_POST['ci'],
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'genero' => $_POST['genero'],
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'rol_id' => $_POST['rol_id']
            ];
            $existing = $this->usuario->findByEmail($data['email']);
            if ($existing) {
                $_SESSION['error'] = 'El email ya está registrado';
                header('Location: usuarios.php?action=create');
                exit();
            }
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
    public function edit($id)
    {
        $this->checkPermission('editar_usuario');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'ci' => $_POST['ci'],
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '2000-01-01',
                'genero' => $_POST['genero'] ?? 'M',
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
        $usuario_data = $this->usuario->getById($id);
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
    public function delete($id)
    {
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
        if ($usuario_a_eliminar['rol_nombre'] == 'registro') {
            $_SESSION['error'] = 'No se puede eliminar al administrador del sistema';
            header('Location: usuarios.php');
            exit();
        }
        if ($this->usuario->delete($id)) {
            header('Location: usuarios.php');
            exit();
        } else {
            $_SESSION['error'] = 'Error al eliminar usuario';
            header('Location: usuarios.php');
            exit();
        }
    }

    public function perfil()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit();
        }

        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $telefono = $_POST['telefono'];
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            $confirm_password = !empty($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

            if ($password && $password !== $confirm_password) {
                $_SESSION['error'] = 'Las contraseñas no coinciden';
            } else {
                $result = $this->usuario->updateProfile($userId, $email, $telefono, $password);
                if (isset($result['success'])) {
                    $_SESSION['success'] = 'Perfil actualizado correctamente. Si cambiaste tu contraseña, úsala en tu próximo inicio de sesión.';
                } else {
                    $_SESSION['error'] = 'Error al actualizar perfil: ' . $result['error'];
                }
            }
            header('Location: index.php?controller=Usuario&action=perfil');
            exit();
        }

        $usuario = $this->usuario->getById($userId);
        $title = 'Mi Perfil';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/usuario/perfil.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
?>
<?php
require_once __DIR__ . '/../config/conexion.php';

class PersonaController {
    private $persona;
    private $usuario;
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Persona.php';
        require_once __DIR__ . '/../models/Usuario.php';
        $this->persona = new Persona();
        $this->usuario = new Usuario();
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }
    
    public function checkPermission($permiso) {
        if (!isset($_SESSION['user_id']) || !$this->usuario->hasPermission($_SESSION['user_id'], $permiso)) {
            $_SESSION['error'] = 'No tienes permisos para esta acción';
            header('Location: dashboard.php');
            exit();
        }
    }
    
    public function index() {
        $this->checkPermission('ver_personas');
        $personas = $this->persona->getAll();
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/personas/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    public function create() {
        $this->checkPermission('crear_persona');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'ci' => $_POST['ci'],
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'genero' => $_POST['genero'],
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? ''
            ];
            
            $existente = $this->persona->getByCi($data['ci']);
            if ($existente) {
                $_SESSION['error'] = 'La CI ya está registrada';
                header('Location: personas.php?action=create');
                exit();
            }
            
            if ($this->persona->create($data)) {
                $_SESSION['success'] = 'Persona creada exitosamente';
                header('Location: personas.php');
                exit();
            } else {
                $_SESSION['error'] = 'Error al crear persona';
            }
        }
        
        $title = 'Nueva Persona';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/personas/create.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    public function edit($id) {
        $this->checkPermission('editar_persona');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'ci' => $_POST['ci'],
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'genero' => $_POST['genero'],
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? ''
            ];
            
            $persona_actual = $this->persona->getById($id);
            if ($persona_actual['ci'] != $data['ci']) {
                $existente = $this->persona->getByCi($data['ci']);
                if ($existente) {
                    $_SESSION['error'] = 'La CI ya está registrada';
                    header("Location: personas.php?action=edit&id=$id");
                    exit();
                }
            }
            
            if ($this->persona->update($id, $data)) {
                $_SESSION['success'] = 'Persona actualizada exitosamente';
                header('Location: personas.php');
                exit();
            } else {
                $_SESSION['error'] = 'Error al actualizar persona';
            }
        }
        
        $persona = $this->persona->getById($id);
        if (!$persona) {
            $_SESSION['error'] = 'Persona no encontrada';
            header('Location: personas.php');
            exit();
        }
        
        $title = 'Editar Persona';
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/personas/edit.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    
    public function delete($id) {
        $this->checkPermission('eliminar_persona');
        
        $persona = $this->persona->getById($id);
        
        if (!$persona) {
            $_SESSION['error'] = 'Persona no encontrada';
            header('Location: personas.php');
            exit();
        }
        
        // Verificar si la persona tiene usuario asociado
        $sql = "SELECT u.id FROM usuario u WHERE u.persona_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            
            // Verificar si es el usuario actual
            if ($usuario['id'] == $_SESSION['user_id']) {
                $_SESSION['error'] = 'No puedes eliminar tu propia persona';
                header('Location: personas.php');
                exit();
            }
            
            // Verificar si el usuario es administrador
            $sql_admin = "SELECT r.nombre FROM usuario u JOIN rol r ON u.rol_id = r.id WHERE u.id = ? AND r.nombre = 'registro'";
            $stmt_admin = $this->db->prepare($sql_admin);
            $stmt_admin->bind_param("i", $usuario['id']);
            $stmt_admin->execute();
            $admin_result = $stmt_admin->get_result();
            
            if ($admin_result->num_rows > 0) {
                $_SESSION['error'] = 'No se puede eliminar la persona del administrador';
                header('Location: personas.php');
                exit();
            }
        }
        
        if ($this->persona->delete($id)) {
            header('Location: personas.php');
            exit();
        } else {
            $_SESSION['error'] = 'Error al eliminar persona';
            header('Location: personas.php');
            exit();
        }
    }
}
?>
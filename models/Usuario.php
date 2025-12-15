<?php
require_once __DIR__ . '/../config/conexion.php';

class Usuario {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/Database.php';
        $this->db = Database::getConnection();
    }
    
    public function getAll() {
        $sql = "SELECT u.*, r.nombre as rol_nombre FROM usuario u JOIN rol r ON u.rol_id = r.id";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $sql = "SELECT u.*, r.nombre as rol_nombre FROM usuario u JOIN rol r ON u.rol_id = r.id WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function findByEmail($email) {
        $sql = "SELECT u.*, r.nombre as rol_nombre FROM usuario u JOIN rol r ON u.rol_id = r.id WHERE u.email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function create($data) {
        $sql = "INSERT INTO usuario (nombre, apellido, email, password, telefono, direccion, rol_id, estado, verification_token, token_expires) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssssiiss", 
            $data['nombre'], 
            $data['apellido'], 
            $data['email'], 
            $data['password'], 
            $data['telefono'], 
            $data['direccion'], 
            $data['rol_id'],
            $data['estado'],
            $data['verification_token'],
            $data['token_expires']
        );
        return $stmt->execute();
    }
    
    public function update($id, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            $sql = "UPDATE usuario SET nombre = ?, apellido = ?, email = ?, password = ?, telefono = ?, direccion = ?, rol_id = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssssssii", 
                $data['nombre'], 
                $data['apellido'], 
                $data['email'], 
                $data['password'], 
                $data['telefono'], 
                $data['direccion'], 
                $data['rol_id'],
                $id
            );
        } else {
            $sql = "UPDATE usuario SET nombre = ?, apellido = ?, email = ?, telefono = ?, direccion = ?, rol_id = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sssssii", 
                $data['nombre'], 
                $data['apellido'], 
                $data['email'], 
                $data['telefono'], 
                $data['direccion'], 
                $data['rol_id'],
                $id
            );
        }
        return $stmt->execute();
    }
    
    public function delete($id) {
        $sql = "DELETE FROM usuario WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function verificarUsuario($token) {
        $sql = "SELECT id, email FROM usuario WHERE verification_token = ? AND token_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            $sql = "UPDATE usuario SET estado = 1, verification_token = NULL, token_expires = NULL WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $result['id']);
            if ($stmt->execute()) {
                return ['success' => true, 'email' => $result['email']];
            }
        }
        return ['success' => false];
    }
    
    public function generarCodigoRecuperacion($email) {
        $sql = "SELECT id, nombre, estado FROM usuario WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            return ['error' => 'Usuario no encontrado'];
        }
        
        if ($user['estado'] == 0) {
            return ['error' => 'Cuenta no verificada'];
        }
        
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expira = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        $sql = "UPDATE usuario SET reset_token = ?, reset_expires = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $codigo, $expira, $user['id']);
        
        if ($stmt->execute()) {
            return [
                'success' => true, 
                'codigo' => $codigo, 
                'user_id' => $user['id'],
                'nombre' => $user['nombre']
            ];
        }
        
        return ['error' => 'Error al generar código'];
    }
    
    public function validarCodigoRecuperacion($email, $codigo) {
        $sql = "SELECT id FROM usuario WHERE email = ? AND reset_token = ? AND reset_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $email, $codigo);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result ? $result['id'] : false;
    }
    
    public function actualizarPassword($user_id, $new_password) {
        $sql = "SELECT password, password_history FROM usuario WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (!$result) {
            return ['error' => 'Usuario no encontrado'];
        }
        
        $history = json_decode($result['password_history'] ?? '[]', true);
        if (in_array($new_password, $history)) {
            return ['error' => 'No puedes usar una contraseña anterior'];
        }
        
        array_unshift($history, $result['password']);
        $history = array_slice($history, 0, defined('MAX_PASSWORD_HISTORY') ? MAX_PASSWORD_HISTORY : 3);
        $history_json = json_encode($history);
        
        $sql = "UPDATE usuario SET password = ?, password_history = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $new_password, $history_json, $user_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Contraseña actualizada'];
        }
        
        return ['error' => 'Error al actualizar contraseña'];
    }
    
    public function hasPermission($usuario_id, $permiso_nombre) {
        $sql = "SELECT COUNT(*) as tiene FROM usuario u 
                JOIN rol_permiso_detalle rpd ON u.rol_id = rpd.rol_id 
                JOIN permiso p ON rpd.permiso_id = p.id 
                WHERE u.id = ? AND p.nombre = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $permiso_nombre);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['tiene'] > 0;
    }
    
    public function getConnection() {
        return $this->db;
    }
}
?>
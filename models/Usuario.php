<?php
require_once __DIR__ . '/../config/conexion.php';
class Usuario
{
    private $db;
    public function __construct()
    {
        require_once __DIR__ . '/Database.php';
        $this->db = Database::getConnection();
    }
    public function getAll()
    {
        $sql = "SELECT u.*, u.plan_type, u.subscription_status, u.subscription_end, 
                       r.nombre as rol_nombre, p.ci, p.nombre as persona_nombre, p.apellido as persona_apellido 
                FROM usuario u 
                JOIN rol r ON u.rol_id = r.id 
                JOIN persona p ON u.persona_id = p.id";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getById($id)
    {
        $sql = "SELECT u.*, u.plan_type, u.subscription_status, u.subscription_end, 
                       r.nombre as rol_nombre, p.ci, p.nombre as persona_nombre, p.apellido as persona_apellido 
                FROM usuario u 
                JOIN rol r ON u.rol_id = r.id 
                JOIN persona p ON u.persona_id = p.id 
                WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function findByEmail($email)
    {
        $sql = "SELECT u.*, u.plan_type, u.subscription_status, u.subscription_end,
                       r.nombre as rol_nombre, p.nombre as persona_nombre, p.apellido as persona_apellido 
                FROM usuario u 
                JOIN rol r ON u.rol_id = r.id 
                JOIN persona p ON u.persona_id = p.id 
                WHERE u.email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function create($data)
    {
        $this->db->begin_transaction();
        try {
            $sql_persona = "INSERT INTO persona (ci, nombre, apellido, fecha_nacimiento, genero, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_persona = $this->db->prepare($sql_persona);
            $ci = $data['ci'];
            $nombre = $data['nombre'];
            $apellido = $data['apellido'];
            $fecha_nacimiento = $data['fecha_nacimiento'] ?? '2000-01-01';
            $genero = $data['genero'] ?? 'M';
            $telefono = $data['telefono'] ?? '';
            $direccion = $data['direccion'] ?? '';
            $stmt_persona->bind_param(
                "sssssss",
                $ci,
                $nombre,
                $apellido,
                $fecha_nacimiento,
                $genero,
                $telefono,
                $direccion
            );
            if (!$stmt_persona->execute()) {
                throw new Exception("Error al crear persona");
            }
            $persona_id = $this->db->insert_id;
            $sql_usuario = "INSERT INTO usuario (persona_id, email, password, rol_id, estado, verification_token, token_expires) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_usuario = $this->db->prepare($sql_usuario);
            $email = $data['email'];
            $password = $data['password'];
            $rol_id = $data['rol_id'];
            $estado = $data['estado'] ?? 0;
            $verification_token = $data['verification_token'] ?? NULL;
            $token_expires = $data['token_expires'] ?? NULL;
            $stmt_usuario->bind_param(
                "issiiss",
                $persona_id,
                $email,
                $password,
                $rol_id,
                $estado,
                $verification_token,
                $token_expires
            );
            if (!$stmt_usuario->execute()) {
                throw new Exception("Error al crear usuario");
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    public function update($id, $data)
    {
        $usuario = $this->getById($id);
        if (!$usuario)
            return false;
        $this->db->begin_transaction();
        try {
            $sql_persona = "UPDATE persona SET ci = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, genero = ?, telefono = ?, direccion = ? WHERE id = ?";
            $stmt_persona = $this->db->prepare($sql_persona);
            $ci = $data['ci'] ?? $usuario['ci'];
            $nombre = $data['nombre'] ?? $usuario['persona_nombre'];
            $apellido = $data['apellido'] ?? $usuario['persona_apellido'];
            $fecha_nacimiento = $data['fecha_nacimiento'] ?? '2000-01-01';
            $genero = $data['genero'] ?? 'M';
            $telefono = $data['telefono'] ?? '';
            $direccion = $data['direccion'] ?? '';
            $persona_id = $usuario['persona_id'];
            $stmt_persona->bind_param(
                "sssssssi",
                $ci,
                $nombre,
                $apellido,
                $fecha_nacimiento,
                $genero,
                $telefono,
                $direccion,
                $persona_id
            );
            $stmt_persona->execute();
            $sql_usuario = "UPDATE usuario SET email = ?, rol_id = ?";
            $params = [];
            $types = "";
            $email = $data['email'] ?? $usuario['email'];
            $rol_id = $data['rol_id'] ?? $usuario['rol_id'];
            $params[] = $email;
            $params[] = $rol_id;
            $types = "si";
            if (isset($data['password']) && !empty($data['password'])) {
                $sql_usuario .= ", password = ?";
                $params[] = $data['password'];
                $types .= "s";
            }
            $sql_usuario .= " WHERE id = ?";
            $params[] = $id;
            $types .= "i";
            $stmt_usuario = $this->db->prepare($sql_usuario);
            $stmt_usuario->bind_param($types, ...$params);
            $stmt_usuario->execute();
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    public function delete($id)
    {
        $usuario = $this->getById($id);
        if (!$usuario)
            return false;
        $this->db->begin_transaction();
        try {
            $sql_usuario = "DELETE FROM usuario WHERE id = ?";
            $stmt_usuario = $this->db->prepare($sql_usuario);
            $stmt_usuario->bind_param("i", $id);
            $stmt_usuario->execute();
            $sql_persona = "DELETE FROM persona WHERE id = ?";
            $stmt_persona = $this->db->prepare($sql_persona);
            $stmt_persona->bind_param("i", $usuario['persona_id']);
            $stmt_persona->execute();
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    public function getProfesores()
    {
        $sql = "SELECT u.id, CONCAT(p.nombre, ' ', p.apellido) as nombre_completo 
                FROM usuario u 
                JOIN persona p ON u.persona_id = p.id 
                JOIN rol r ON u.rol_id = r.id 
                WHERE r.nombre = 'profesor' 
                ORDER BY p.apellido, p.nombre";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getEstudiantes()
    {
        $sql = "SELECT u.id, CONCAT(p.nombre, ' ', p.apellido) as nombre_completo, p.ci 
                FROM usuario u 
                JOIN persona p ON u.persona_id = p.id 
                JOIN rol r ON u.rol_id = r.id 
                WHERE r.nombre = 'estudiante' 
                ORDER BY p.apellido, p.nombre";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function verificarUsuario($token)
    {
        $sql = "SELECT u.id, u.email FROM usuario u WHERE verification_token = ? AND token_expires > NOW()";
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
    public function generarCodigoRecuperacion($email)
    {
        $sql = "SELECT u.id, p.nombre, u.estado FROM usuario u JOIN persona p ON u.persona_id = p.id WHERE u.email = ?";
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
    public function validarCodigoRecuperacion($email, $codigo)
    {
        $sql = "SELECT id FROM usuario WHERE email = ? AND reset_token = ? AND reset_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $email, $codigo);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['id'] : false;
    }
    public function actualizarPassword($user_id, $new_password)
    {
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
    public function hasPermission($usuario_id, $permiso_nombre)
    {
        $sql = "SELECT COUNT(*) as tiene FROM usuario u 
                JOIN rol_permiso_detalle rpd ON u.rol_id = rpd.rol_id 
                JOIN permiso p ON rpd.permiso_id = p.id 
                WHERE u.id = ? AND p.nombre = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $permiso_nombre);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result['tiene'] == 0) {
            $sql_role = "SELECT r.nombre FROM usuario u JOIN rol r ON u.rol_id = r.id WHERE u.id = ?";
            $stmt_role = $this->db->prepare($sql_role);
            $stmt_role->bind_param("i", $usuario_id);
            $stmt_role->execute();
            $role_result = $stmt_role->get_result()->fetch_assoc();
            if ($role_result['nombre'] == 'registro' && strpos($permiso_nombre, 'crear_') === 0) {
                return true;
            }
        }
        return $result['tiene'] > 0;
    }
    public function getConnection()
    {
        return $this->db;
    }

    public function updateSubscription($userId, $planType, $status, $endDate = null)
    {
        $sql = "UPDATE usuario SET plan_type = ?, subscription_status = ?, subscription_end = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $planType, $status, $endDate, $userId);
        return $stmt->execute();
    }

    public function updateProfile($id, $email, $telefono, $password = null)
    {
        $this->db->begin_transaction();
        try {
            // 1. Validar Email único (excluyendo al propio usuario)
            $sql = "SELECT id FROM usuario WHERE email = ? AND id != ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("si", $email, $id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                return ['success' => false, 'error' => 'El email ya está en uso.'];
            }

            // 2. Actualizar Usuario
            $sqlUsuario = "UPDATE usuario SET email = ? WHERE id = ?";
            $types = "si";
            $params = [$email, $id];

            if ($password) {
                // Verificar historial de contraseñas
                $userData = $this->getById($id);
                $history = $userData['password_history'] ? json_decode($userData['password_history'], true) : [];

                foreach ($history as $oldHash) {
                    if (password_verify($password, $oldHash)) {
                        return ['success' => false, 'error' => 'No puedes usar una contraseña reciente.'];
                    }
                }

                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sqlUsuario = "UPDATE usuario SET email = ?, password = ?, password_history = ? WHERE id = ?";

                // Actualizar historial
                array_unshift($history, $hash);
                if (count($history) > 3)
                    array_pop($history);
                $historyJson = json_encode($history);

                $types = "sssi";
                $params = [$email, $hash, $historyJson, $id];
            }

            $stmt = $this->db->prepare($sqlUsuario);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            // 3. Actualizar Persona (Teléfono)
            $sqlPersona = "UPDATE persona SET telefono = ? WHERE id = (SELECT persona_id FROM usuario WHERE id = ?)";
            $stmt = $this->db->prepare($sqlPersona);
            $stmt->bind_param("si", $telefono, $id);
            $stmt->execute();

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function incrementarInterrupciones($user_id)
    {
        $sql = "UPDATE usuario SET conteo_interrupciones = conteo_interrupciones + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    public function getInterrupciones($user_id)
    {
        $sql = "SELECT conteo_interrupciones FROM usuario WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['conteo_interrupciones'];
        }
        return 0;
    }
}
?>
<?php
require_once __DIR__ . '/../config/conexion.php';

class UserSession
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/Database.php';
        $this->db = Database::getConnection();
    }

    public function registerSession($userId, $sessionId, $userAgent = '', $ip = '')
    {
        // Verificar límites antes de registrar
        $plan = $this->getUserPlan($userId);
        $limit = $this->getSessionLimit($plan);
        $activeSessions = $this->getActiveSessions($userId);

        // Si alcanzamos (o superamos) el límite, rotamos sesiones (FIFO)
        // Eliminamos las más antiguas hasta tener espacio para la nueva
        while (count($activeSessions) >= $limit) {
            $this->invalidateOldestSession($userId);
            array_shift($activeSessions); // Actualizamos conteo local
        }

        // Registrar nueva sesión
        $sql = "INSERT INTO user_sessions (user_id, session_id, user_agent, ip_address) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isss", $userId, $sessionId, $userAgent, $ip);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Error al registrar sesión'];
    }

    public function isValid($sessionId)
    {
        $sql = "SELECT id FROM user_sessions WHERE session_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $sessionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function removeSession($sessionId)
    {
        $sql = "DELETE FROM user_sessions WHERE session_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $sessionId);
        return $stmt->execute();
    }

    public function removeAllUserSessions($userId)
    {
        $sql = "DELETE FROM user_sessions WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    private function getActiveSessions($userId)
    {
        $sql = "SELECT * FROM user_sessions WHERE user_id = ? ORDER BY last_activity ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function invalidateOldestSession($userId)
    {
        // Borra la que tenga last_activity más antiguo
        $sql = "DELETE FROM user_sessions WHERE user_id = ? ORDER BY last_activity ASC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }

    private function getUserPlan($userId)
    {
        // Obtener plan directamente
        $sql = "SELECT plan_type FROM usuario WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? $res['plan_type'] : 'basic'; // Default a basic o none
    }

    public function getSessionLimit($planType)
    {
        switch ($planType) {
            case 'premium':
                return 5;
            case 'pro':
                return 3;
            case 'basic':
            default:
                return 1;
        }
    }

    public function getAllActiveSessionsWithUser()
    {
        $sql = "SELECT us.*, 
                       u.email, u.plan_type, u.subscription_status,
                       p.nombre, p.apellido 
                FROM user_sessions us
                JOIN usuario u ON us.user_id = u.id
                JOIN persona p ON u.persona_id = p.id
                ORDER BY u.id, us.last_activity DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
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
        // 1. Limpieza General (Garbage Collection) - 5% de probabilidad o siempre
        // Para asegurar que el monitor esté limpio, lo haremos siempre en el login por ahora
        $this->garbageCollect(120); // 120 segundos = 2 minutos de inactividad

        // 2. Verificar si ya existe una sesión para este mismo navegador/IP
        // Si el usuario cerró la pestaña y volvió a abrir, es el mismo 'dispositivo'
        // pero PHP generó una nueva session_id. Reclamamos el slot.
        // Ojo: session_id cambió, pero las características son iguales.
        // Haremos un "force replace" si encontramos coincidencia exacta de UA + IP + UserID
        $this->reclaimSessionSlot($userId, $userAgent, $ip);

        // 3. Verificar límites
        $plan = $this->getUserPlan($userId);
        $limit = $this->getSessionLimit($plan);
        $activeSessions = $this->getActiveSessions($userId);

        // Si alcanzamos el límite, rotamos (FIFO)
        while (count($activeSessions) >= $limit) {
            $this->invalidateOldestSession($userId);
            $activeSessions = $this->getActiveSessions($userId); // Refrescar lista
        }

        // 4. Registrar nueva sesión
        $sql = "INSERT INTO user_sessions (user_id, session_id, user_agent, ip_address) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isss", $userId, $sessionId, $userAgent, $ip);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Error al registrar sesión'];
    }

    public function updateLastActivity($sessionId)
    {
        $sql = "UPDATE user_sessions SET last_activity = CURRENT_TIMESTAMP WHERE session_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $sessionId);
        return $stmt->execute();
    }

    public function garbageCollect($seconds)
    {
        // Borrar sesiones inactivas por más de X segundos
        $sql = "DELETE FROM user_sessions WHERE last_activity < (NOW() - INTERVAL ? SECOND)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $seconds);
        $stmt->execute();
    }

    private function reclaimSessionSlot($userId, $ua, $ip)
    {
        // Borra sesiones anteriores del mismo usuario en el mismo dispositivo IP/UA
        // Esto evita 'duplicados' cuando el usuario solo cerró el navegador
        $sql = "DELETE FROM user_sessions WHERE user_id = ? AND user_agent = ? AND ip_address = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iss", $userId, $ua, $ip);
        $stmt->execute();
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
        // 1. Obtener datos de la sesión que se va a eliminar
        $sqlSelect = "SELECT * FROM user_sessions WHERE user_id = ? ORDER BY last_activity ASC LIMIT 1";
        $stmtSelect = $this->db->prepare($sqlSelect);
        $stmtSelect->bind_param("i", $userId);
        $stmtSelect->execute();
        $sessionToDelete = $stmtSelect->get_result()->fetch_assoc();

        if ($sessionToDelete) {
            // 2. Registrar en Log
            $logSql = "INSERT INTO session_logs (user_id, action, device, ip_address) VALUES (?, 'session_rotated', ?, ?)";
            $logStmt = $this->db->prepare($logSql);
            // Simplificar User Agent para el log si es muy largo, o guardarlo completo
            $device = $sessionToDelete['user_agent'];
            $ip = $sessionToDelete['ip_address'];

            $logStmt->bind_param("iss", $userId, $device, $ip);
            $logStmt->execute();

            // 3. Eliminar la sesión
            $sqlDelete = "DELETE FROM user_sessions WHERE session_id = ?";
            $stmtDelete = $this->db->prepare($sqlDelete);
            $stmtDelete->bind_param("s", $sessionToDelete['session_id']);
            $stmtDelete->execute();
        }
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
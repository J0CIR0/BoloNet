<?php
require_once __DIR__ . '/../config/conexion.php';

class SessionController
{
    private $userSessionModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar Permisos: Solo Admin (rol_id 1 o 2) o Permiso explicito 'ver_usuarios'
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit();
        }

        require_once __DIR__ . '/../models/UserSession.php';
        $this->userSessionModel = new UserSession();
    }

    public function index()
    {
        // Verificar Rol Admin (1=Registro, 2=Admin)
        // Ojo: Asumiendo roles 1 y 2 son administrativos. Mejor usar permisos si existen.
        $rol = $_SESSION['role_id'] ?? 0;
        if ($rol != 1 && $rol != 2) {
            header('Location: dashboard.php');
            exit();
        }

        $allSessions = $this->userSessionModel->getAllActiveSessionsWithUser();

        // Agrupar por Usuario
        $groupedSessions = [];
        foreach ($allSessions as $s) {
            $userId = $s['user_id'];
            if (!isset($groupedSessions[$userId])) {
                $groupedSessions[$userId] = [
                    'user_data' => [
                        'nombre' => $s['nombre'],
                        'apellido' => $s['apellido'],
                        'email' => $s['email'],
                        'plan_type' => $s['plan_type'],
                        'id' => $userId
                    ],
                    'sessions' => []
                ];
            }
            $groupedSessions[$userId]['sessions'][] = $s;
        }

        $title = "Monitor de Sesiones";
        require_once __DIR__ . '/../views/admin/sessions.php';
    }

    public function revoke()
    {
        // Verificar Rol Admin
        $rol = $_SESSION['role_id'] ?? 0;
        if ($rol != 1 && $rol != 2) {
            header('Location: dashboard.php');
            exit();
        }

        if (isset($_GET['id'])) {
            $sessionId = $_GET['id'];
            if ($this->userSessionModel->removeSession($sessionId)) {
                $_SESSION['success'] = "Sesión revocada correctamente.";
            } else {
                $_SESSION['error'] = "Error al revocar la sesión.";
            }
        }
        header('Location: index.php?controller=Session&action=index');
        exit();
    }

    public function refresh()
    {
        // Verificar Rol Admin
        $rol = $_SESSION['role_id'] ?? 0;
        if ($rol != 1 && $rol != 2) {
            exit();
        }

        $allSessions = $this->userSessionModel->getAllActiveSessionsWithUser();

        // Agrupar por Usuario (misma lógica que index)
        $groupedSessions = [];
        foreach ($allSessions as $s) {
            $userId = $s['user_id'];
            if (!isset($groupedSessions[$userId])) {
                $groupedSessions[$userId] = [
                    'user_data' => [
                        'nombre' => $s['nombre'],
                        'apellido' => $s['apellido'],
                        'email' => $s['email'],
                        'plan_type' => $s['plan_type'],
                        'id' => $userId
                    ],
                    'sessions' => []
                ];
            }
            $groupedSessions[$userId]['sessions'][] = $s;
        }

        require __DIR__ . '/../views/admin/partials/session_list.php';
        exit();
    }
}

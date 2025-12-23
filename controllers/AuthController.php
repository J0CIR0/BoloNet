<?php
require_once __DIR__ . '/../config/conexion.php';
class AuthController
{
    private $usuario;
    public function __construct()
    {
        require_once __DIR__ . '/../models/Usuario.php';
        $this->usuario = new Usuario();
    }
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = $this->usuario->findByEmail($email);
            if ($user) {
                if ($user['estado'] == 0) {
                    $_SESSION['error'] = 'Cuenta no verificada. Revisa tu email.';
                    header('Location: index.php');
                    exit();
                }
                if ($password == $user['password']) {
                    // --- CONTROL DE SESIONES CONCURRENTES ---
                    require_once __DIR__ . '/../models/UserSession.php';
                    $sessionModel = new UserSession();

                    // Regenerar ID de sesión por seguridad
                    session_regenerate_id(true);

                    // Intentar registrar la sesión según el plan
                    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
                    $regResult = $sessionModel->registerSession($user['id'], session_id(), $ua, $ip);

                    if (!$regResult['success']) {
                        $_SESSION['error'] = $regResult['error']; // "Límite de sesiones alcanzado..."
                        header('Location: index.php');
                        exit();
                    }

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['persona_nombre'] . ' ' . $user['persona_apellido'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['rol_nombre'];
                    $_SESSION['role_id'] = $user['rol_id'];

                    // Datos de Suscripción en Sesión
                    $_SESSION['plan_type'] = $user['plan_type'];
                    $_SESSION['subscription_status'] = $user['subscription_status'];

                    header('Location: dashboard.php');
                    exit();
                } else {
                    $_SESSION['error'] = 'Credenciales incorrectas';
                }
            } else {
                $_SESSION['error'] = 'Usuario no encontrado';
            }
        }
        header('Location: index.php');
        exit();
    }
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../models/Email.php';
            $existing = $this->usuario->findByEmail($_POST['email']);
            if ($existing) {
                $_SESSION['error'] = 'El email ya está registrado';
                header('Location: register.php');
                exit();
            }
            $verification_token = bin2hex(random_bytes(32));
            $data = [
                'ci' => $_POST['ci'],
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '2000-01-01',
                'genero' => $_POST['genero'] ?? 'M',
                'rol_id' => 3,
                'estado' => 0,
                'verification_token' => $verification_token,
                'token_expires' => date('Y-m-d H:i:s', strtotime('+24 hours'))
            ];
            if ($this->usuario->create($data)) {
                $email = new Email();
                $nombre_completo = $data['nombre'] . ' ' . $data['apellido'];
                if ($email->enviarVerificacion($data['email'], $nombre_completo, $verification_token)) {
                    $_SESSION['success'] = 'Registro exitoso. Revisa tu email para verificar tu cuenta.';
                } else {
                    $_SESSION['warning'] = 'Registro exitoso, pero hubo un problema con el email.';
                }
                header('Location: index.php');
                exit();
            } else {
                $_SESSION['error'] = 'Error al registrar usuario';
                header('Location: register.php');
                exit();
            }
        }
        require_once __DIR__ . '/../views/auth/register.php';
    }
    public function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Eliminar sesión de la base de datos
        require_once __DIR__ . '/../models/UserSession.php';
        $sessionModel = new UserSession();
        $sessionModel->removeSession(session_id());

        session_destroy();
        header('Location: index.php');
        exit();
    }
    public function solicitarRecuperacion()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../models/Email.php';
            $email = trim($_POST['email']);
            if (empty($email)) {
                $_SESSION['error'] = 'Ingresa tu email';
                header('Location: forgot_password.php');
                exit();
            }
            $result = $this->usuario->generarCodigoRecuperacion($email);
            if (isset($result['success']) && $result['success']) {
                $emailSender = new Email();
                if ($emailSender->enviarCodigoRecuperacion($email, $result['nombre'], $result['codigo'])) {
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['success'] = 'Código enviado a tu email.';
                    header('Location: reset_password.php');
                    exit();
                } else {
                    $_SESSION['error'] = 'Error al enviar el código.';
                }
            } else {
                $_SESSION['error'] = $result['error'] ?? 'Error al procesar la solicitud';
            }
        }
        require_once __DIR__ . '/../views/auth/forgot_password.php';
    }
    public function resetPassword()
    {
        if (!isset($_SESSION['reset_email'])) {
            header('Location: forgot_password.php');
            exit();
        }
        $email = $_SESSION['reset_email'];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['codigo'])) {
                $codigo = trim($_POST['codigo']);
                if (empty($codigo) || strlen($codigo) !== 6) {
                    $_SESSION['error'] = 'Código inválido';
                    header('Location: reset_password.php');
                    exit();
                }
                $user_id = $this->usuario->validarCodigoRecuperacion($email, $codigo);
                if ($user_id) {
                    $_SESSION['reset_valid'] = true;
                    $_SESSION['reset_user_id'] = $user_id;
                } else {
                    $_SESSION['error'] = 'Código incorrecto o expirado';
                }
            }
            if (isset($_POST['new_password']) && isset($_SESSION['reset_valid'])) {
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                if ($new_password !== $confirm_password) {
                    $_SESSION['error'] = 'Las contraseñas no coinciden';
                } else {
                    $result = $this->usuario->actualizarPassword($_SESSION['reset_user_id'], $new_password);
                    if (isset($result['success']) && $result['success']) {
                        unset($_SESSION['reset_email']);
                        unset($_SESSION['reset_valid']);
                        unset($_SESSION['reset_user_id']);
                        $_SESSION['success'] = 'Contraseña actualizada. Ya puedes iniciar sesión.';
                        header('Location: index.php');
                        exit();
                    } else {
                        $_SESSION['error'] = $result['error'] ?? 'Error al actualizar contraseña';
                    }
                }
            }
        }
        require_once __DIR__ . '/../views/auth/reset_password.php';
    }

    public function checkSessionStatus()
    {
        header('Content-Type: application/json');

        // Si no hay sesión PHP iniciada, claramente no es inválido (o ya expiró)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['valid' => false, 'reason' => 'no_session']);
            exit;
        }

        require_once __DIR__ . '/../models/UserSession.php';
        $sessionModel = new UserSession();

        // Verificar si la sesión actual existe en BD
        $isValid = $sessionModel->isValid(session_id());

        if (!$isValid) {
            // Si no es válida en BD, destruir sesión PHP local
            session_destroy();
            echo json_encode(['valid' => false, 'reason' => 'concurrent_login']);
            exit;
        }

        echo json_encode(['valid' => true]);
        exit;
    }
}
?>
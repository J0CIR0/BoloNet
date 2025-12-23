<?php
// Incluimos los modelos necesarios
require_once 'models/Curso.php';
require_once 'models/Pago.php';
require_once 'models/Inscripcion.php';

class PagoController
{

    public function planes()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        require_once 'views/pagos/planes.php';
    }

    public function checkout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            if (isset($_GET['plan'])) {
                $_SESSION['redirect_plan'] = $_GET['plan'];
            }
            header("Location: index.php?controller=Auth&action=login");
            exit();
        }

        if (!isset($_GET['plan'])) {
            header("Location: index.php?controller=Pago&action=planes");
            exit();
        }

        $planStr = $_GET['plan'];

        $precios = [
            'basic' => ['nombre' => 'Plan Básico', 'precio' => 9.99],
            'pro' => ['nombre' => 'Plan Pro', 'precio' => 19.99],
            'premium' => ['nombre' => 'Plan Premium', 'precio' => 29.99]
        ];

        if (!array_key_exists($planStr, $precios)) {
            die("Error: Plan no válido.");
        }

        $plan_seleccionado = $precios[$planStr];
        $nombre_plan = $plan_seleccionado['nombre'];
        $precio = $plan_seleccionado['precio'];
        $plan_type = $planStr; // Para JS

        require_once 'views/pagos/checkout.php';
    }

    public function procesarPagoExitoso()
    {
        ob_start();

        header('Content-Type: application/json');
        $response = ["status" => "error", "mensaje" => "Error desconocido"];

        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $input = file_get_contents("php://input");
            $data = json_decode($input);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Error decodificando JSON: " . json_last_error_msg());
            }

            if (!isset($data->orderID) || !isset($data->plan_type) || !isset($data->usuario_id)) {
                throw new Exception("Datos incompletos recibidos. Se requiere plan_type.");
            }

            if ($data->estado === 'COMPLETED') {

                $pagoModel = new Pago();
                $pagoModel = new Pago();

                $usuario_id = (int) $data->usuario_id;
                $planType = $data->plan_type; // basic, pro, premium
                $transaccion_id = $data->orderID;

                $precios = [
                    'basic' => 9.99,
                    'pro' => 19.99,
                    'premium' => 29.99
                ];

                if (!array_key_exists($planType, $precios)) {
                    throw new Exception("Tipo de plan no válido.");
                }

                $precioEsperado = $precios[$planType];
                $montoPagado = (float) $data->monto;

                if ($montoPagado < ($precioEsperado - 0.50)) {
                    error_log("FRAUDE SUBSCRIPCIÓN: Usuario $usuario_id pagó $montoPagado por plan $planType ($precioEsperado)");
                    throw new Exception("El monto pagado no coincide con el precio del plan.");
                }

                $pagoRegistrado = $pagoModel->registrar(
                    $usuario_id,
                    0,
                    $transaccion_id,
                    $montoPagado,
                    'PayPal_Subscription',
                    'completado'
                );

                if ($pagoRegistrado) {
                    require_once 'models/Usuario.php';
                    $usuarioModel = new Usuario();

                    $fechaFin = date('Y-m-d H:i:s', strtotime('+30 days'));

                    if ($usuarioModel->updateSubscription($usuario_id, $planType, 'active', $fechaFin)) {

                        if (session_status() == PHP_SESSION_NONE) {
                            session_start();
                        }
                        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $usuario_id) {
                            $_SESSION['plan_type'] = $planType;
                            $_SESSION['subscription_status'] = 'active';
                        }

                        require_once 'models/Email.php';
                        $usuarioData = $usuarioModel->getById($usuario_id);

                        if ($usuarioData && !empty($usuarioData['email'])) {
                            $mailer = new Email();
                            $nombreCompleto = $usuarioData['persona_nombre'] . ' ' . $usuarioData['persona_apellido'];

                            $emailEnviado = $mailer->enviarFactura(
                                $usuarioData['email'],
                                $nombreCompleto,
                                $planType,
                                $montoPagado,
                                $transaccion_id
                            );

                            if ($emailEnviado) {
                                error_log("Factura enviada a: " . $usuarioData['email']);
                            }
                        }

                        $response = ["status" => "success", "mensaje" => "Suscripción $planType activada exitosamente."];
                    } else {
                        throw new Exception("Pago registrado, pero error al activar suscripción en BD.");
                    }
                } else {
                    throw new Exception("No se pudo registrar el pago en la BD.");
                }

            } else {
                throw new Exception("El estado del pago no es COMPLETED.");
            }

        } catch (Exception $e) {
            $response = ["status" => "error", "mensaje" => $e->getMessage()];
            error_log("Error PagoController: " . $e->getMessage());
        }

        ob_end_clean();

        echo json_encode($response);
        exit();
    }
}
?>
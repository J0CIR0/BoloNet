<?php
// Incluimos los modelos necesarios
require_once 'models/Curso.php';
require_once 'models/Pago.php';
require_once 'models/Inscripcion.php';

class PagoController
{

    // 1. Mostrar la página de selección de pago (Checkout)
    public function checkout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=Auth&action=login");
            exit();
        }

        if (!isset($_GET['id_curso'])) {
            die("Error: Curso no especificado.");
        }

        $id_curso = (int) $_GET['id_curso'];

        $cursoModel = new Curso();
        // Nota: Asumimos que obtenerPorId devuelve un OBJETO según tu código anterior
        $curso = $cursoModel->obtenerPorId($id_curso);

        if (!$curso) {
            die("Error: El curso no existe.");
        }

        // Variables para la vista
        $nombre_curso = $curso->nombre;
        $precio = $curso->precio;

        require_once 'views/pagos/checkout.php';
    }

    // 2. Método que procesa la respuesta de PayPal (AJAX)
    public function procesarPagoExitoso()
    {
        // --- 1. INICIAR BUFFER: Atrapa cualquier error/warning de PHP ---
        ob_start();

        header('Content-Type: application/json');
        $response = ["status" => "error", "mensaje" => "Error desconocido"];

        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Recibir y decodificar JSON
            $input = file_get_contents("php://input");
            $data = json_decode($input);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Error decodificando JSON: " . json_last_error_msg());
            }

            // Validar datos mínimos
            if (!isset($data->orderID) || !isset($data->plan_type) || !isset($data->usuario_id)) {
                throw new Exception("Datos incompletos recibidos. Se requiere plan_type.");
            }

            if ($data->estado === 'COMPLETED') {

                $pagoModel = new Pago();
                // $inscripcionModel ya no es necesario para suscripciones

                $usuario_id = (int) $data->usuario_id;
                $planType = $data->plan_type; // basic, pro, premium
                $transaccion_id = $data->orderID;

                // --- VALIDACIÓN DE PRECIOS DE SUSCRIPCIÓN ---
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

                // Validación anti-fraude
                if ($montoPagado < ($precioEsperado - 0.50)) {
                    error_log("FRAUDE SUBSCRIPCIÓN: Usuario $usuario_id pagó $montoPagado por plan $planType ($precioEsperado)");
                    throw new Exception("El monto pagado no coincide con el precio del plan.");
                }

                // PASO A: Registrar el pago (curso_id = 0 para indicar suscripción general)
                $pagoRegistrado = $pagoModel->registrar(
                    $usuario_id,
                    0,
                    $transaccion_id,
                    $montoPagado,
                    'PayPal_Subscription',
                    'completado'
                );

                if ($pagoRegistrado) {
                    // PASO B: Actualizar Estado del Usuario
                    require_once 'models/Usuario.php';
                    $usuarioModel = new Usuario();

                    // Calcular fecha fin (1 mes desde hoy)
                    $fechaFin = date('Y-m-d H:i:s', strtotime('+30 days'));

                    if ($usuarioModel->updateSubscription($usuario_id, $planType, 'active', $fechaFin)) {

                        // Actualizar sesión actual si corresponde
                        if (session_status() == PHP_SESSION_NONE) {
                            session_start();
                        }
                        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $usuario_id) {
                            $_SESSION['plan_type'] = $planType;
                            $_SESSION['subscription_status'] = 'active';
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

        // --- 2. LIMPIAR BUFFER: Asegura que solo salga JSON limpio ---
        ob_end_clean();

        echo json_encode($response);
        exit();
    }
}
?>
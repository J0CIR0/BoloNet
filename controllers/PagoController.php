<?php
// Incluimos los modelos necesarios
require_once 'models/Curso.php';
require_once 'models/Pago.php';
require_once 'models/Inscripcion.php';

class PagoController {

    // 1. Mostrar la página de selección de pago (Checkout)
    public function checkout() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=Auth&action=login");
            exit();
        }

        if (!isset($_GET['id_curso'])) {
            die("Error: Curso no especificado.");
        }

        $id_curso = (int)$_GET['id_curso'];
        
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
    public function procesarPagoExitoso() {
        // --- 1. INICIAR BUFFER: Atrapa cualquier error/warning de PHP ---
        ob_start(); 

        header('Content-Type: application/json');
        $response = ["status" => "error", "mensaje" => "Error desconocido"];

        try {
            if (session_status() == PHP_SESSION_NONE) { session_start(); }

            // Recibir y decodificar JSON
            $input = file_get_contents("php://input");
            $data = json_decode($input);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Error decodificando JSON: " . json_last_error_msg());
            }

            // Validar datos mínimos
            if (!isset($data->orderID) || !isset($data->curso_id) || !isset($data->usuario_id)) {
                throw new Exception("Datos incompletos recibidos.");
            }

            if ($data->estado === 'COMPLETED') {
                
                $pagoModel = new Pago();
                $inscripcionModel = new Inscripcion();
                $cursoModel = new Curso(); // Instanciamos el modelo para verificar precio

                $usuario_id = (int)$data->usuario_id;
                $curso_id = (int)$data->curso_id;
                $transaccion_id = $data->orderID;
                
                // --- SEGURIDAD CRÍTICA: VALIDACIÓN DE PRECIO ---
                // 1. Buscamos el curso real en la BD
                $cursoReal = $cursoModel->obtenerPorId($curso_id);
                
                if (!$cursoReal) {
                    throw new Exception("El curso solicitado no existe en la base de datos.");
                }

                // 2. Obtenemos los montos y aseguramos que sean números flotantes
                // Manejamos si viene como objeto ($curso->precio) o array ($curso['precio'])
                $precioRealBD = is_object($cursoReal) ? (float)$cursoReal->precio : (float)$cursoReal['precio'];
                $montoPagado = (float)$data->monto;

                // 3. Comparamos (Permitimos una diferencia de 0.50 por temas de redondeo o tasas)
                // Si el precio real es 50.00 y pagaron 0.01, esto saltará.
                if ($montoPagado < ($precioRealBD - 0.50)) {
                    error_log("FRAUDE DETECTADO: Usuario $usuario_id intentó pagar $montoPagado por curso de $precioRealBD");
                    throw new Exception("Error de Validación: El monto pagado ($montoPagado) no coincide con el precio del curso.");
                }

                // Si pasa la validación, usamos el monto pagado para el registro
                
                // PASO A: Registrar el pago
                $pagoRegistrado = $pagoModel->registrar(
                    $usuario_id, 
                    $curso_id, 
                    $transaccion_id, 
                    $montoPagado, 
                    'PayPal', 
                    'completado'
                );

                if ($pagoRegistrado) {
                    // PASO B: Inscribir
                    if (!$inscripcionModel->verificarInscripcion($usuario_id, $curso_id)) {
                        $inscrito = $inscripcionModel->registrar($usuario_id, $curso_id);
                        
                        if ($inscrito) {
                            $response = ["status" => "success", "mensaje" => "Inscripción exitosa"];
                        } else {
                            throw new Exception("Pago registrado, pero falló la inscripción en BD.");
                        }
                    } else {
                        $response = ["status" => "success", "mensaje" => "Usuario ya estaba inscrito."];
                    }
                } else {
                    throw new Exception("No se pudo registrar el pago en la BD (posible duplicado).");
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
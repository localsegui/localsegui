<?php
header('Content-Type: application/json');

// Importar las clases de PHPMailer al inicio del archivo
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanear y recibir entradas
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $disponibilidad = isset($_POST['disponibilidad']) ? trim($_POST['disponibilidad']) : '';

    // Validación Alfabética en PHP
    if (!preg_match("/^[A-Za-z\x{00C0}-\x{00FF}\s]+$/u", $nombre)) {
        echo json_encode(['status' => 'error', 'message' => 'El campo Nombre solo acepta caracteres alfabéticos.']);
        exit;
    }

    // Validación Numérica en PHP
    $telefonoLimpio = str_replace([' ', '-'], '', $telefono);
    if (!ctype_digit($telefonoLimpio)) {
        echo json_encode(['status' => 'error', 'message' => 'El campo Teléfono sólo acepta números.']);
        exit;
    }

    // Validar campo obligatorio restante
    if (empty($disponibilidad)) {
        echo json_encode(['status' => 'error', 'message' => 'El campo de disponibilidad es requerido.']);
        exit;
    }

    // Cargar el autoloader de Composer
    require 'vendor/autoload.php';

    // Instanciar PHPMailer
    $mail = new PHPMailer(true);

    try {
        // CONFIGURACIÓN DEL SERVIDOR SMTP PARA YAHOO
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.mail.yahoo.com';                  // Servidor SMTP oficial de Yahoo
        $mail->SMTPAuth   = true;                                   // Activar autenticación
        
        // ================= CAMBIA ESTOS 2 VALORES =================
        $mail->Username   = local_segui@yahoo.com';         // Tu dirección de correo de Yahoo
        $mail->Password   = 'bqvohrrovdksxzdu';                  // LA CONTRASEÑA DE 16 CARACTERES GENERADA EN EL PASO 1
        // ==========================================================
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Cifrado SSL/TLS (Recomendado para el puerto 465)
        $mail->Port       = 465;                                    // Puerto SMTP seguro de Yahoo
        $mail->CharSet    = 'UTF-8';                                // Soporte para acentos y eñes

        // Configuración de Remitente y Destinatario
        // NOTA: Yahoo exige que el "setFrom" sea tu misma cuenta de Yahoo para evitar bloqueos por suplantación
        $mail->setFrom('local_segui@yahoo.com', 'Formulario Web Contacto'); 
        $mail->addAddress('local_segui@yahoo.com');                 // Tu correo de destino donde recibes las alertas
        $mail->addReplyTo('local_segui@yahoo.com');                 // Dirección para responder

        // Formato de contenido en HTML
        $subject = 'Nueva solicitud de visita al inmueble - ' . $nombre;
        $mail->isHTML(true);                                        
        $mail->Subject = $subject;
        
        // Diseño básico en HTML para el cuerpo del mensaje
        $mail->Body    = "<h3>Detalles de la solicitud de visita:</h3>" .
                         "<p><b>Nombre y Apellido:</b> " . htmlspecialchars($nombre) . "</p>" .
                         "<p><b>Teléfono de contacto:</b> " . htmlspecialchars($telefono) . "</p>" .
                         "<p><b>Preferencia de días y horarios:</b><br>" . nl2br(htmlspecialchars($disponibilidad)) . "</p>";
        
        // Texto sin HTML de respaldo
        $mail->AltBody = "Detalles de la solicitud de visita:\n\n" .
                         "Nombre y Apellido: " . $nombre . "\n" .
                         "Teléfono de contacto: " . $telefono . "\n" .
                         "Preferencia de días y horarios:\n" . $disponibilidad;

        // Procesar envío
        $mail->send();
        echo json_encode(['status' => 'success', 'message' => '¡Solicitud enviada con éxito! Nos comunicaremos a la brevedad.']);

    } catch (Exception $e) {
        // Captura errores específicos de PHPMailer sin romper la respuesta JSON
        echo json_encode(['status' => 'error', 'message' => 'Error al enviar el correo a través de Yahoo. Detalle: ' . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido.']);
}


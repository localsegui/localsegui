<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanear y recibir entradas
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $disponibilidad = isset($_POST['disponibilidad']) ? trim($_POST['disponibilidad']) : '';

    // 1. Validación Alfabética en PHP
    if (!preg_match("/^[A-Za-z\x{00C0}-\x{00FF}\s]+$/u", $nombre)) {
        echo json_encode(['status' => 'error', 'message' => 'El campo Nombre solo acepta caracteres alfabéticos.']);
        exit;
    }

    // 2. Validación Numérica en PHP
    // Elimina espacios y guiones temporalmente para validar que sean dígitos
    $telefonoLimpio = str_replace([' ', '-'], '', $telefono);
    if (!ctype_digit($telefonoLimpio)) {
        echo json_encode(['status' => 'error', 'message' => 'El campo Teléfono sólo acepta números.']);
        exit;
    }

    // 3. Validar campo obligatorio restante
    if (empty($disponibilidad)) {
        echo json_encode(['status' => 'error', 'message' => 'El campo de disponibilidad es requerido.']);
        exit;
    }

    // Configuración del correo
    $para = 'local_segui@yahoo.com';
    $asunto = 'Nueva solicitud de visita al inmueble - ' . $nombre;
    
    // Cuerpo del mensaje
    $cuerpo = "Detalles de la solicitud de visita:\n\n";
    $cuerpo .= "Nombre y Apellido: " . $nombre . "\n";
    $cuerpo .= "Teléfono de contacto: " . $telefono . "\n";
    $cuerpo .= "Preferencia de días y horarios:\n" . $disponibilidad . "\n";

    // Cabeceras de correo estándar
    $cabeceras = "From: webmaster@tudominio.com\r\n"; // Cambia por un correo de tu dominio
    $cabeceras .= "Reply-To: webmaster@tudominio.com\r\n";
    $cabeceras .= "X-Mailer: PHP/" . phpversion();

    // Enviar el correo electrónico
    if (mail($para, $asunto, $cuerpo, $cabeceras)) {
        echo json_encode(['status' => 'success', 'message' => '¡Solicitud enviada con éxito! Nos comunicaremos a la brevedad.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'El servidor no pudo procesar el envío del correo.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido.']);
}

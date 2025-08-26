<?php
include 'gameManager.php';
include 'messages.php';
header('Content-Type: text/plain');


// Token de verificación que definimos
$token = 'ElDeLata';

// WhatsApp envía estos parámetros para verificar el webhook
if (isset($_GET['hub_mode']) && $_GET['hub_mode'] === 'subscribe') {
    $palabraReto = $_GET['hub_challenge'] ?? '';
    $tokenVerificacion = $_GET['hub_verify_token'] ?? '';

    // Compara los tokens
    if ($token === $tokenVerificacion) {
        echo $palabraReto; // WhatsApp necesita esta respuesta para verificar el webhook
        exit;
    } else {
        http_response_code(403);
        echo "Token inválido";
        exit;
    }
}

// Si no es una verificación, procesa los mensajes normalmente
$respuesta = file_get_contents("php://input");
$respuesta = json_decode($respuesta, true);

if ($respuesta) {
    $mensaje = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'] ?? null;
    $telefonoCliente = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['from'] ?? null;

    if ($mensaje) {
        test($telefonoCliente);
    }
}

function test($telefonoCliente)
{
    $telefonoCliente = trim($telefonoCliente);

    // Si el número empieza con '549' (con '9' después de '54'), lo eliminamos
    if (substr($telefonoCliente, 0, 3) == '549') {
        $telefonoCliente = substr($telefonoCliente, 0, 2) . substr($telefonoCliente, 3); // Elimina el '9' después de '54'
        error_log("Número con el '9' eliminado: $telefonoCliente");
    }

    // Si el número está vacío, no enviamos el mensaje
    if (empty($telefonoCliente)) {
        error_log("Error: El número de teléfono no es válido.");
        return false;
    }
    error_log("antes del send text");

    sendText($telefonoCliente, "Este es un test desde el sendText");
}

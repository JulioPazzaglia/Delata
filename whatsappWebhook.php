<?php
include 'messageManager.php'; 
include 'messages.php';       
include 'DBconfig.php';       
header('Content-Type: text/plain');

// Validacion Meta API de WhatsApp
$token = 'ElDeLata';
if (isset($_GET['hub_mode']) && $_GET['hub_mode'] === 'subscribe') {
    $palabraReto = $_GET['hub_challenge'] ?? '';
    $tokenVerificacion = $_GET['hub_verify_token'] ?? '';

    if ($token === $tokenVerificacion) {
        echo $palabraReto;
        exit;
    } else {
        http_response_code(403);
        echo "Token inválido";
        exit;
    }
}

// Decodificacion del mensaje
$body = file_get_contents("php://input");
$data = json_decode($body, true);

// Extraer mensaje y número si existen
$mensaje = $data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'] ?? null;
$telefonoCliente = $data['entry'][0]['changes'][0]['value']['messages'][0]['from'] ?? null;

// Ejecutar lógica del juego
if ($mensaje && $telefonoCliente) {
    manageMessage($conn, $telefonoCliente, $mensaje);
}

<?php
include('clearDB.php');
/*
aca va a manejarse el game loop.
*/

//Esto es un Test
function recibido($telefonoCliente)
{
    // Asegurar que el número esté en el formato correcto
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

    /*
    $data = [
        "messaging_product" => "whatsapp",
        "to" => "$telefonoCliente",
        "type" => "text",
        "text" => ["body" => "test"]
    ];
    
    error_log("Datos enviados a la API: " . json_encode($data));
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        error_log("Error en la petición cURL: " . curl_error($ch));
    }
    
    curl_close($ch);
    
    // Log de la respuesta de la API
    error_log("Respuesta de la API: " . $response);
    
    return $response;
    */
}

/**
 * Recibimos un mensaje del creador de la partida
 * creamos un 'game', introducimos el jugador y le damos un id
 */

/**
 * recibimos otros mensajes para unirse a un 'game'
 * los metemos en la tabla.
 */

/**
 * recibimos el ok del creador y empieza la partida
 * se elige un mentiroso
 */

//empieza el loop

/**
 * se le envia las preguntas a todos menos el mentiroso
 */

/**
 * se reciben los votos y revisamos si es el mentiroso
 */

//si no se descubre sigue asi por X rondas

/**
 * se descubre el mentiroso
 */

/**
 * nueva partida?
 */

/**
 * se borran los usuarios y game
 */
function endGame($conn, $game_id)
{
    deletePlayers($conn, $game_id);
    deleteId($conn, $game_id);
}

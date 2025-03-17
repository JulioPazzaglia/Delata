<?php
// Message send handle

function sendText($num, $message)
{

    $url = "https://graph.facebook.com/v22.0/125021817354805/messages";
    $token = trim("EAAzI3Uuu9ZA4BOwMAedCR2NYZCR7M94fTDaxKpxDsnrxTI6ThAazmTbeKZAJZBZAulguwqeMHvUeo1udEwc5VpEbzzZCsYqwONYD0kbLMwAO9OZBHb81mwfEZAYqVKm4sZBQyIRV6EV8U8a4n8Dwm04BAafmabbun2PVADhHfKl7DjK1Nzgm87rDErhyoEFYRKnXlENGiG04ijHah4wuWWYTtkoUNsvht");

    $data = [
        "messaging_product" => "whatsapp",
        "to" => "$num",
        "type" => "text",
        "text" => ["body" => "$message"]
    ];

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

    error_log("post Curls");

    curl_close($ch);

    // Log de la respuesta de la API
    error_log("Respuesta de la API: " . $response);

    return $response;
}

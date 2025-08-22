<?php
// whatsappConnection.php
// Message send handles for WhatsApp Cloud API
// - Debug/logs in English
// - Player-visible text is the $message you pass (Spanish in your app)

/**
 * Send a plain text message to a wa_id (E.164 without '+').
 * Auto-splits long texts (>4096 chars) into multiple messages.
 */
function sendText(string $wa_id, string $message): bool
{
    $token          = trim(getenv('WA_TOKEN') ?: '');
    $phoneNumberId  = trim(getenv('WA_PHONE_NUMBER_ID') ?: ''); // e.g. 1250...805

    if ($token === '' || $phoneNumberId === '') {
        error_log("[WA] Missing WA_TOKEN or WA_PHONE_NUMBER_ID env vars.");
        return false;
    }

    $endpoint = "https://graph.facebook.com/v22.0/{$phoneNumberId}/messages";

    // WhatsApp Cloud API text limit guideline (~4096). We chunk if needed.
    $MAX = 4096;
    $chunks = ($message === '') ? [''] : str_split($message, $MAX);

    $ok = true;
    foreach ($chunks as $idx => $part) {
        $payload = [
            "messaging_product" => "whatsapp",
            "to"   => $wa_id,
            "type" => "text",
            "text" => [
                "body"        => $part,
                "preview_url" => false
            ]
        ];
        $res = waPostJson($endpoint, $token, $payload);
        $ok = $ok && $res;
        // Small delay to avoid throttling when sending multiple parts
        if ($idx < count($chunks) - 1) usleep(200000); // 200ms
    }
    return $ok;
}

/**
 * Send a Flow (interactive) to open your “Crear/Unirse” flow.
 * $prefillName: optional prefill for the Name field.
 */
function sendFlow(string $wa_id, string $flowId, string $flowToken, string $prefillName = ""): bool
{
    $token          = trim(getenv('WA_TOKEN') ?: '');
    $phoneNumberId  = trim(getenv('WA_PHONE_NUMBER_ID') ?: '');

    if ($token === '' || $phoneNumberId === '') {
        error_log("[WA] Missing WA_TOKEN or WA_PHONE_NUMBER_ID env vars.");
        return false;
    }
    if ($flowId === '' || $flowToken === '') {
        error_log("[WA] Missing flowId or flowToken when calling sendFlow().");
        return false;
    }

    $endpoint = "https://graph.facebook.com/v22.0/{$phoneNumberId}/messages";

    $payload = [
        "messaging_product" => "whatsapp",
        "to"   => $wa_id,
        "type" => "interactive",
        "interactive" => [
            "type" => "flow",
            "header" => ["type" => "text", "text" => "Delata"],
            "body"   => ["text" => "¡Bienvenido! Creá un grupo o unite a uno existente."],
            "footer" => ["text" => "WhatsApp Flow"],
            "action" => [
                "name" => "flow",
                "parameters" => [
                    "flow_message_version" => "3",
                    "flow_token" => $flowToken,
                    "flow_id"    => $flowId,
                    "flow_cta"   => "Abrir",
                    "flow_action" => "navigate",
                    "flow_action_data" => [
                        "screen" => "INICIO",
                        "data"   => [
                            "Name" => $prefillName
                        ]
                    ]
                ]
            ]
        ]
    ];

    return waPostJson($endpoint, $token, $payload);
}

/**
 * Low-level POST helper for Graph API with logging.
 */
function waPostJson(string $url, string $token, array $payload): bool
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$token}",
            "Content-Type: application/json"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
    ]);

    $response = curl_exec($ch);
    $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        error_log("[WA] cURL error: " . curl_error($ch));
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    if ($http >= 200 && $http < 300) {
        error_log("[WA] POST OK ({$http}): {$response}");
        return true;
    } else {
        error_log("[WA] POST FAIL ({$http}): {$response}");
        return false;
    }
}

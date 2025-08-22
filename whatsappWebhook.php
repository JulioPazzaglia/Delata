<?php
// whatsappWebhook.php
// Webhook for WhatsApp Cloud API
// - Debug logs in English
// - Player-facing messages are sent via sendText(...) in upper layers

header('Content-Type: text/plain');

include_once 'DBconfig.php';
include_once 'messageManager.php';     // manageMessage(...)
include_once 'whatsappConnection.php'; // sendText(...), sendFlow(...)
include_once 'players.php';            // playerExists(...)

// ---- Verification (Webhook subscription) ----
$verifyTokenEnv = getenv('WA_VERIFY_TOKEN');
$verifyToken    = $verifyTokenEnv !== false ? $verifyTokenEnv : 'ElDeLata';

if (isset($_GET['hub_mode']) && $_GET['hub_mode'] === 'subscribe') {
    $challenge = $_GET['hub_challenge'] ?? '';
    $tokenRecv = $_GET['hub_verify_token'] ?? '';

    if (hash_equals($verifyToken, $tokenRecv)) {
        echo $challenge;
        exit;
    } else {
        http_response_code(403);
        echo "Token inválido";
        exit;
    }
}

// ---- Incoming message handling ----
$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || empty($data['entry'][0]['changes'][0]['value'])) {
    http_response_code(200);
    echo "ok";
    exit;
}

$value = $data['entry'][0]['changes'][0]['value'];

// No messages? Acknowledge.
if (empty($value['messages'])) {
    http_response_code(200);
    echo "ok";
    exit;
}

$contacts        = $value['contacts'][0] ?? [];
$userProfileName = $contacts['profile']['name'] ?? null;

// Handle each message (defensive: multiple in one delivery)
foreach ($value['messages'] as $msg) {
    $from = $msg['from'] ?? null;               // wa_id (E.164 without '+')
    $type = $msg['type'] ?? null;

    if (!$from) {
        continue;
    }

    // 1) Flow completion → route by payload (action/join_id/player_name)
    if ($type === 'interactive' && ($msg['interactive']['type'] ?? '') === 'flow_completion') {
        $flow = $msg['interactive']['flow'] ?? [];
        $resp = $flow['response'] ?? [];

        $action = $resp['action'] ?? null;     // create | join
        $name   = $resp['player_name'] ?? ($userProfileName ?: 'Jugador');
        $gid    = isset($resp['join_id']) ? intval($resp['join_id']) : 0;

        // Delegate to message manager helpers (they will send user-facing text)
        if ($action === 'create') {
            // createGroupWithAdmin() is called inside manageMessage flow or directly here if you prefer.
            // For consistency, we can just call the underlying helpers:
            if (function_exists('createGroupWithAdmin')) {
                createGroupWithAdmin($conn, $from, $name);
            } else {
                // Fallback: route via manageMessage (not ideal for flow completion)
                manageMessage($conn, $from, "crear {$name}");
            }
        } elseif ($action === 'join') {
            if (function_exists('joinGroup')) {
                joinGroup($conn, $from, $name, $gid);
            } else {
                manageMessage($conn, $from, "unirme {$gid} {$name}");
            }
        } else {
            error_log("[Webhook] Unknown flow action for {$from}");
        }
        continue;
    }

    // 2) Text messages
    if ($type === 'text') {
        $text = $msg['text']['body'] ?? '';

        // If you want to auto-send Flow to non-registered users:
        if (!playerExists($conn, $from)) {
            // Optional: send Flow “Crear/Unirse” right away
            $flowId    = getenv('DELATA_FLOW_ID')    ?: '';
            $flowToken = getenv('DELATA_FLOW_TOKEN') ?: '';
            if ($flowId && $flowToken) {
                sendFlow($from, $flowId, $flowToken, $userProfileName ?: "");
                // Do not send anything else; let the user complete the Flow.
                continue;
            }
        }

        // Default: route to your message manager (it will decide and reply)
        manageMessage($conn, $from, $text);
        continue;
    }

    // 3) Other types (image/audio/buttons/etc.) — optional handling
    error_log("[Webhook] Unhandled message type '{$type}' from {$from}");
}

http_response_code(200);
echo "ok";

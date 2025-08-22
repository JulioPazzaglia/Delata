<?php
// Entry point: delegates incoming messages to the message manager (group logic) through endPoints, not whatsapp

include("../messageManager.php");
include("../DBconfig.php");

// Read params (accept GET or POST)
$phone_number = $_GET['phone_number'] ?? $_POST['phone_number'] ?? null;
$messageText  = $_GET['messageText']  ?? $_POST['messageText']  ?? '';

$phone_number = $phone_number ? trim($phone_number) : null;
$messageText  = strtolower(trim($messageText));

// Basic guard: wa_id is required
if (!$phone_number) {
    // Debug only (English). Do not send user-facing text here.
    error_log("Missing phone_number (wa_id) in request.");
    http_response_code(400);
    echo "missing phone_number";
    exit;
}

// Route to message manager (this will send Spanish messages to the user as needed)
manageMessage($conn, $phone_number, $messageText);

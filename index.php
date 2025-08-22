<?php
// index.php
// Bootstraps the app: DB, schema (debug), WhatsApp helpers, managers.
// Debug logs are in English. Do not emit user-facing messages here.

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 1) DB config & connection
    include_once __DIR__ . '/DBconfig.php'; // sets $conn (mysqli)

    // 2) (Optional) create/update schema for debug/dev
    // Comment this out in production to avoid accidental schema changes.
    include_once __DIR__ . '/db.php';

    // 3) WhatsApp connection helpers (sendText, sendFlow, etc.)
    include_once __DIR__ . '/whatsappConnection.php';

    // 4) Domain managers (now group-oriented, even if filename still says "game")
    include_once __DIR__ . '/gameManager.php';   // contains createGroup, groupExists, selectLiar, etc.
    include_once __DIR__ . '/players.php';       // if you have player helpers here (createPlayer, etc.)
    include_once __DIR__ . '/messages.php';      // messageManager / routing logic if applicable

    // Nothing else to run at index time; webhook or CLI scripts will do the work.

} catch (Throwable $e) {
    // Keep errors away from end users; log for developers.
    error_log("[BOOT] Fatal error on index.php: " . $e->getMessage());
    http_response_code(500);
    echo "Internal server error";
} finally {
    // 5) Close DB connection safely
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}

<?php
// Message manager (group-first). 
// - Debug/comments in English
// - Player-facing messages in Spanish

include_once("clearDB.php");
include_once("groupsManager.php");        // now provides createGroup, groupExists, selectLiar, etc.
include_once("playersManager.php");     // playerExists, createPlayer, joinGroup, createGroupWithAdmin, ...
include_once("questionsHandler.php");
include_once("whatsappConnection.php"); // sendText($wa_id, $message)

/**
 * Entry point for plain-text commands (fallback to Flow).
 * $phone_number is the wa_id (E.164 without '+').
 */
function manageMessage($conn, $phone_number, $messageText)
{
    $messageText = strtolower(trim((string)$messageText));

    if (playerExists($conn, $phone_number)) {
        // Existing player - you can route in-game commands here later.
        // Debug in English; user-facing in Spanish.
        error_log("Player {$phone_number} already registered. Skipping new-player flow.");
        sendText($phone_number, "📍 Ya estás registrado. Próximamente agregaremos comandos para jugadores existentes.");
        return;
    }

    handleNewPlayerMessage($conn, $phone_number, $messageText);
}

/**
 * Handle first-contact commands for non-registered players.
 * Supported:
 *  - crear [nombre]
 *  - unirme [ID_grupo] [nombre]
 */
function handleNewPlayerMessage($conn, $phone_number, $messageText)
{
    $messageText = strtolower(trim((string)$messageText));
    $parts = preg_split('/\s+/', $messageText);
    $parts = array_values(array_filter($parts, fn($p) => $p !== ''));

    if (empty($parts)) {
        sendHelp($phone_number);
        return;
    }

    $cmd = $parts[0] ?? '';

    // CREATE: create group and add creator as admin
    if ($cmd === "crear" && count($parts) >= 2) {
        $name = ucfirst($parts[1]);
        if (!checkName($phone_number, $name)) return;

        sendText($phone_number, "🛠 Creando un grupo y agregándote como administrador: {$name}");
        $group_id = createGroup($conn);
        if (!$group_id) {
            error_log("createGroup returned null/false");
            sendText($phone_number, "❌ No se pudo crear el grupo. Intentá de nuevo.");
            return;
        }
        createPlayer($conn, $phone_number, $name, (int)$group_id, true);
        return;
    }

    // JOIN: join existing group as non-admin
    if ($cmd === "unirme" && count($parts) >= 3) {
        $group_id = intval($parts[1]);
        $name     = ucfirst($parts[2]);
        if (!checkName($phone_number, $name)) return;

        createPlayer($conn, $phone_number, $name, $group_id, false);
        return;
    }

    // fallback
    sendHelp($phone_number);
}

/**
 * Validate display name.
 */
function checkName($phone_number, $name): bool
{
    if (strlen($name) < 2) {
        sendText($phone_number, "⚠️ El nombre es demasiado corto. Por favor escribí un nombre más claro.");
        return false;
    }
    return true;
}

/**
 * Help message for new players.
 */
function sendHelp($phone_number): void
{
    sendText(
        $phone_number,
        "👋 Para comenzar, escribí:\n" .
            "• *crear [tu nombre]* para iniciar un grupo\n" .
            "• *unirme [ID del grupo] [tu nombre]* para sumarte a uno existente"
    );
}

/**
 * End a group and cascade delete players (admin-only guard should be enforced elsewhere).
 * Kept for compatibility with previous naming; now uses group helpers.
 */
function endGroup($conn, $group_id)
{
    // Prefer FK ON DELETE CASCADE; this is a manual cleanup fallback.
    deletePlayersByGroupId($conn, (int)$group_id);
    deleteGroup($conn, (int)$group_id);
}

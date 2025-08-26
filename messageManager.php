<?php
include_once("clearDB.php");
include_once("groupsManager.php");
include_once("playersManager.php");
include_once("questionsHandler.php");


function manageMessage($conn, $phone_number, $messageText)
{
    $messageText = strtolower(trim($messageText));

    if (playerExists($conn, $phone_number)) {
        echo "📍 Player $phone_number is already registered. (Further actions for existing players not implemented yet).";
        return;
    }

    handleNewPlayerMessage($conn, $phone_number, $messageText);
}

function handleNewPlayerMessage($conn, $phone_number, $messageText)
{
    $messageText = strtolower(trim($messageText));
    $parts = preg_split('/\s+/', $messageText);

    if ($parts[0] === "crear" && count($parts) >= 2) {
        $name = ucfirst($parts[1]);
        echo "🛠 Creating a group and adding you as the first player: $name<br>";
        createGroupWithAdmin($conn, $phone_number, $name);
        return;
    }

    if ($parts[0] === "unirme" && count($parts) >= 3) {
        $group_id = intval($parts[1]);
        $name = ucfirst($parts[2]);
        joinGrup($conn, $phone_number, $name, $group_id);
        return;
    }

    echo "👋 Para comenzar, escribí *crear [tu nombre]* para iniciar una partida o *unirme [ID del juego] [tu nombre]* para sumarte a una existente.";

}

function checkName($name)
{
    if (strlen($name) < 2) {
        echo "⚠️ El nombre es demasiado corto. Por favor escribí un nombre más claro.";
        return false;
    }
    return true;
}


function createGroupWithAdmin($conn, $phone_number, $name)
{
    if (!checkName($name)) return;
    $group_id = createGroup($conn);
    if ($group_id) {
        createPlayer($conn, $phone_number, $name, $group_id, true);
        echo "✅ Group created with ID $grup_id. You are now the admin.";
    } else {
        echo "❌ Failed to create the group.";
    }
}

function joinGroup($conn, $phone_number, $name, $group_id)
{
    if (!checkName($name)) return;
    if (!groupExists($conn, $group_id)) {
        echo "❌ El juego con ID $group_id no existe.";
        return;
    }
    createPlayer($conn, $phone_number, $name, $group_id, false);
    echo "✅ Te uniste correctamente al juego $group_id como $name.";
}



function endGroup($conn, $group_id)
{
    deletePlayers($conn, $group_id);
    deleteId($conn, $group_id);
}

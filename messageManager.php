<?php
include_once("clearDB.php");
include_once("gameManager.php");
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
        echo "🛠 Creating a game and adding you as the first player: $name<br>";
        createGameWithAdmin($conn, $phone_number, $name);
        return;
    }

    if ($parts[0] === "unirme" && count($parts) >= 3) {
        $game_id = intval($parts[1]);
        $name = ucfirst($parts[2]);
        joinGame($conn, $phone_number, $name, $game_id);
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


function createGameWithAdmin($conn, $phone_number, $name)
{
    if (!checkName($name)) return;
    $game_id = createGame($conn);
    if ($game_id) {
        createPlayer($conn, $phone_number, $name, $game_id, true);
        echo "✅ Game created with ID $game_id. You are now the admin.";
    } else {
        echo "❌ Failed to create the game.";
    }
}

function joinGame($conn, $phone_number, $name, $game_id)
{
    if (!checkName($name)) return;
    if (!gameExists($conn, $game_id)) {
        echo "❌ El juego con ID $game_id no existe.";
        return;
    }
    createPlayer($conn, $phone_number, $name, $game_id, false);
    echo "✅ Te uniste correctamente al juego $game_id como $name.";
}



function endGame($conn, $game_id)
{
    deletePlayers($conn, $game_id);
    deleteId($conn, $game_id);
}

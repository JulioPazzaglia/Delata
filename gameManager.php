<?php
include_once("clearDB.php");
include_once("gameCreation.php");
include_once("players.php");
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
    // Normalize input
    $messageText = strtolower(trim($messageText));
    $parts = preg_split('/\s+/', $messageText);

    if ($parts[0] === "crear" && count($parts) >= 2) {
        $name = ucfirst($parts[1]);
        echo "🛠 Creating a game and adding you as the first player: $name<br>";

        $game_id = createGame($conn);
        if ($game_id) {
            createPlayer($conn, $phone_number, $name, $game_id, true); // true = is_admin
            echo "✅ Game created with ID $game_id. You are now the admin.";
        } else {
            echo "❌ Failed to create the game.";
        }
        return;
    }

    if ($parts[0] === "unirme" && count($parts) >= 3) {
        $game_id = intval($parts[1]);
        $name = ucfirst($parts[2]);
        echo "🤝 Adding you ($name) to game ID $game_id...";
        // TODO: handleJoinCommand()
        return;
    }

    echo "👋 Hello! To get started, type *create [your name]* to start a new game, or *join [game ID] [your name]* to join an existing one.";
}



function endGame($conn, $game_id)
{
    deletePlayers($conn, $game_id);
    deleteId($conn, $game_id);
}

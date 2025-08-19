<?php
include("../db.php");
include("../gameCreation.php");
include("../players.php");
include("../gameManager.php");
include("../questionsHandler.php");

echo "<pre>";

$accion = $_GET['accion'] ?? null;
$gameId = $_GET['game_id'] ?? null;
$telefono = $_GET['telefono'] ?? null;
$nombre = $_GET['nombre'] ?? null;

switch ($accion) {
    case 'createGame':
        $newGameId = createGame($conn);
        echo "✅ Juego creado con ID: $newGameId";
        break;

    case 'createPlayer':
        if (!$gameId || !$telefono || !$nombre) {
            echo "❌ Faltan datos: teléfono, nombre o game_id.";
            break;
        }
        createPlayer($conn, $telefono, $nombre, $gameId);
        echo "✅ Jugador $nombre ($telefono) agregado al juego $gameId";
        break;

    case 'selectLiar':
        if (!$gameId) {
            echo "❌ Faltó el game_id.";
            break;
        }
        selectLiar($conn, $gameId);
        echo "✅ Mentiroso seleccionado para el juego $gameId";
        break;

    case 'insertQuestions':
        if (!$gameId) {
            echo "❌ Faltó el game_id.";
            break;
        }
        insertQuestions($conn, $gameId);
        echo "✅ Pregunta insertada en el juego $gameId";
        break;

    case 'fetchQuestions':
        if (!$gameId) {
            echo "❌ Faltó el game_id.";
            break;
        }
        $questions = fetchGameQuestions($conn, $gameId);
        $extracted = extractQuestions($questions);
        echo "📋 Preguntas activas para el juego $gameId:\n";
        foreach ($extracted as $q) {
            echo "• $q\n";
        }
        break;

    case 'endGame':
        if (!$gameId) {
            echo "❌ Faltó el game_id.";
            break;
        }
        endGame($conn, $gameId);
        echo "✅ Juego $gameId finalizado y eliminado";
        break;

    default:
        echo "❓ Acción no reconocida o faltante.";
        break;
}

echo "</pre>";
$conn->close();


// esto deberia ser un comentario
/*
<?php
include("../db.php");
include("../gameCreation.php");
include("../players.php");
include("../gameManager.php");
include("../questionsHandler.php");


$newGameId = createGame($conn);

createPlayer($conn, "+541", "More", $newGameId);
createPlayer($conn, "+542", "Julio", $newGameId);
createPlayer($conn, "+543", "Angeles", $newGameId);

selectLiar($conn, $newGameId);

$questions = fetchGameQuestions($conn, $newGameId);
echo extractQuestions($questions)[0] . "<br>";
insertQuestions($conn, $newGameId);

$questions = fetchGameQuestions($conn, $newGameId);
echo extractQuestions($questions)[0] . "<br>";
insertQuestions($conn, $newGameId);

$questions = fetchGameQuestions($conn, $newGameId);
echo extractQuestions($questions)[0] . "<br>";
insertQuestions($conn, $newGameId);

endGame($conn, $newGameId);

$conn->close();
*/
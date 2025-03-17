<?php
include("../DBconfig.php");
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

//endGame($conn, $newGameId);

/*
Este archivo va a tener hardcodeado un ejemplo de trabajo.
Va a crear los jugadores, meterlos en el juego, hacer un par de rondas.
*/

$conn->close();

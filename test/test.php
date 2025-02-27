<?php
include("../DBconfig.php");
include("../gameCreation.php");
include("../players.php");
include("../gameManager.php");

$newGameId = createGame($conn);
//createPlayer($conn, "+5492325423315", "Julio", 1); original
//createPlayer($conn, "+5492325423315", "Juli", 1); repetir numero = error
//createPlayer($conn, "+54923254233", "Julio", 1); repetir nombre = error
createPlayer($conn, "+541", "More", $newGameId);
createPlayer($conn, "+542", "Julio", $newGameId);
createPlayer($conn, "+543", "Angeles", $newGameId);

endGame($conn, $newGameId);

/*
Este archivo va a tener hardcodeado un ejemplo de trabajo.
Va a crear los jugadores, meterlos en el juego, hacer un par de rondas.
*/

$conn->close();

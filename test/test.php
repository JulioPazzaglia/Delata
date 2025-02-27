<?php
include("../DBconfig.php");
include("../gameCreation.php");
include("../players.php");

//createGame($conn);
//createPlayer($conn, "+5492325423315", "Julio", 1); original
//createPlayer($conn, "+5492325423315", "Juli", 1); repetir numero = error
//createPlayer($conn, "+54923254233", "Julio", 1); repetir nombre = error
createPlayer($conn, "+5492326423074", "More", 1);

/*
Este archivo va a tener hardcodeado un ejemplo de trabajo.
Va a crear los jugadores, meterlos en el juego, hacer un par de rondas.
*/

$conn->close();

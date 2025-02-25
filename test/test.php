<?php
include("../gameCreation.php");
include("../users.php");

createGame($conn);
//createUser($conn, 2325423315, "Julio", 1);

/*
Este archivo va a tener hardcodeado un ejemplo de trabajo.
Va a crear los jugadores, meterlos en el juego, hacer un par de rondas.
*/

$conn->close();

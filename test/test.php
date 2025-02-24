<?php
include("../gameCreation.php");

createGame($conn);
/*
Ete archivo va a tener hardcodeado un ejemplo de trabajo.
Va a crear los jugadores, meterlos en el juego, hacer un par de rondas.
*/
$conn->close();

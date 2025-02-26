<?php
include("../DBconfig.php");
include("../gameCreation.php");
include("../users.php");

//createGame($conn);
//createUser($conn, "+5492325423315", "Julio", 1);
//createUser($conn, "+5492325423315", "Juli", 1);
//createUser($conn, "+54923254233", "Julio", 1);
createUser($conn, "+5492326423074", "More", 1);
//createUser($conn, "+5492325422074", "Angeles", 1);

/*
Este archivo va a tener hardcodeado un ejemplo de trabajo.
Va a crear los jugadores, meterlos en el juego, hacer un par de rondas.
*/

$conn->close();

<?php
//DB config 
include 'DBconfig.php';

//conectamos a whatsapp
include 'whatsappConnection.php';

//creamos la DB
include 'db.php';

//enviamos los mensajes a gameManager
include 'gameManager.php';

//cerramos la conneccion con la DB
$conn->close();

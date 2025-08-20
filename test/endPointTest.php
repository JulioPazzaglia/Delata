<?php
include("../messageManager.php");
include("../DBconfig.php");

// Parámetros del link
$phone_number = $_GET['phone_number'] ?? null;
$messageText = strtolower(trim($_GET['messageText'] ?? ''));

// Lógica central delegada a gameManager
manageMessage($conn, $phone_number, $messageText);


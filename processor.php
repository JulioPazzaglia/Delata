<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "game_database";

// Crear conexión
$conn = new mysqli($servername, $username, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Crear base de datos
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn->select_db($dbname);

// Crear tabla Players
$sql = "CREATE TABLE IF NOT EXISTS Players (
    number INT AUTO_INCREMENT PRIMARY KEY,
    name CHAR(255) NOT NULL,
    votes INT NOT NULL,
    hasVoted BOOLEAN NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Players created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}

// Crear tabla Game con relación a Players
$sql = "CREATE TABLE IF NOT EXISTS Game (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questions TEXT NOT NULL, -- Usar TEXT para almacenar arrays serializados pero puedes usar serialize() y unserialize() 
    player_id INT,
    FOREIGN KEY (player_id) REFERENCES Players(number)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Game created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "delataDB";

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
    number INT PRIMARY KEY,
    name CHAR(255) NOT NULL,
    votes INT NOT NULL,
    hasVoted BOOLEAN NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Players created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}


// Crear tabla Questions
$sql = "CREATE TABLE IF NOT EXISTS Questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questions TEXT NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Game created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}

// Crear tabla Game con relación a Players
$sql = "CREATE TABLE IF NOT EXISTS Game (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questionsId TEXT NOT NULL, -- Usar TEXT para almacenar arrays serializados pero puedes usar serialize() y unserialize() 
    player_id INT,
    FOREIGN KEY (player_id) REFERENCES Players(number)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Game created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}

// Seediamos las preguntas
$questionSeed = array('quien?', 'como?', 'cuando?', 'donde?'); // Placeholders

// Revision de la tabla
$result = $conn->query("SELECT COUNT(*) as total FROM Questions");
$row = $result->fetch_assoc();

if ($row['total'] == 0) {
    // Insercion de preguntas
    foreach ($questionSeed as $question) {
        $sql = "INSERT INTO Questions (questions) VALUES ('$question')";
        if ($conn->query($sql) === TRUE) {
            echo "Pregunta '$question' insertada correctamente.<br>";
        } else {
            echo "Error insertando '$question': " . $conn->error . "<br>";
        }
    }
} else {
    echo "la tabla tiene algo";
}


$conn->close();

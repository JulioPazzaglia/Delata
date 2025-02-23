<?php
include("DBconfig.php");

// creacion la tabla Game
$sql = "CREATE TABLE IF NOT EXISTS Game (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questions TEXT NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Game created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}

// crecion tabla Questions
$sql = "CREATE TABLE IF NOT EXISTS Questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questions TEXT NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Questions created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}

// creacion tabla Players
$sql = "CREATE TABLE IF NOT EXISTS Players (
    number INT PRIMARY KEY,
    name CHAR(255) NOT NULL,
    game_id INT,
    votes INT NOT NULL,
    hasVoted TINYINT(1) NOT NULL DEFAULT 0
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Players created successfully\n";
} else {
    echo "Error creating table: " . $conn->error;
}

// Seediamos las preguntas
$questionSeed = array('quien?', 'como?', 'cuando?', 'donde?'); // Placeholders

// Revisamos si la tabla está vacía
$result = $conn->query("SELECT COUNT(*) as total FROM Questions");
$row = $result->fetch_assoc();

if ($row['total'] == 0) {
    foreach ($questionSeed as $question) {
        $sql = "INSERT INTO Questions (questions) VALUES ('$question')";
        if ($conn->query($sql) === TRUE) {
            echo "Pregunta '$question' insertada correctamente.<br>";
        } else {
            echo "Error insertando '$question': " . $conn->error . "<br>";
        }
    }
} else {
    echo "La tabla Questions ya tiene datos.";
}

$conn->close();

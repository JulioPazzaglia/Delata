<?php
include("DBconfig.php");

try {
    // Creating the Game table
    $sql = "CREATE TABLE IF NOT EXISTS Game (
        game_id INT AUTO_INCREMENT PRIMARY KEY,
        questions TEXT NOT NULL
    )";
    $conn->query($sql);
    echo "Table Game created successfully <br>";
} catch (mysqli_sql_exception $e) {
    echo "Error creating table: " . $e->getMessage() . "<br>";
}

try {
    // Creating the Questions table
    $sql = "CREATE TABLE IF NOT EXISTS Questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        questions TEXT NOT NULL
    )";
    $conn->query($sql);
    echo "Table Questions created successfully <br>";
} catch (mysqli_sql_exception $e) {
    echo "Error creating table: " . $e->getMessage() . "<br>";
}

try {
    // Creating the Players table
    $sql = "CREATE TABLE IF NOT EXISTS Players (
        id INT AUTO_INCREMENT PRIMARY KEY,
        number CHAR(40) NOT NULL UNIQUE,
        name CHAR(255) NOT NULL UNIQUE,
        game_id INT NOT NULL,
        FOREIGN KEY (game_id) REFERENCES Game(game_id),
        votes INT,
        hasVoted TINYINT(1) NOT NULL DEFAULT 0
    )";
    $conn->query($sql);
    echo "Table Players created successfully <br>";
} catch (mysqli_sql_exception $e) {
    echo "Error creating table: " . $e->getMessage() . "<br>";
}

// Seeding questions
$questionSeed = array('who?', 'how?', 'when?', 'where?'); // Placeholders

try {
    // Checking if the table is empty
    $result = $conn->query("SELECT COUNT(*) as total FROM Questions");
    $row = $result->fetch_assoc();

    if ($row['total'] == 0) {
        foreach ($questionSeed as $question) {
            $sql = "INSERT INTO Questions (questions) VALUES ('$question')";
            $conn->query($sql);
            echo "Question '$question' inserted successfully. <br>";
        }
    } else {
        echo "The Questions table already contains data.";
    }
} catch (mysqli_sql_exception $e) {
    echo "Error inserting questions: " . $e->getMessage() . "<br>";
}

$conn->close();

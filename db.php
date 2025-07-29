<?php
include("DBconfig.php");

try {
    // Create Game table – stores the current state of a game and remaining questions
    $sql = "CREATE TABLE IF NOT EXISTS Game (
        game_id INT AUTO_INCREMENT PRIMARY KEY,
        questions TEXT NOT NULL
    )";
    $conn->query($sql);
    echo "[✔] Table 'Game' created.<br>";
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error creating 'Game' table: " . $e->getMessage() . "<br>";
}

try {
    // Create Questions table – original pool of all possible questions
    $sql = "CREATE TABLE IF NOT EXISTS Questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_text TEXT NOT NULL
    )";
    $conn->query($sql);
    echo "[✔] Table 'Questions' created.<br>";
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error creating 'Questions' table: " . $e->getMessage() . "<br>";
}

try {
    // Create Players table – stores who's in the game and their state
    $sql = "CREATE TABLE IF NOT EXISTS Players (
        id INT AUTO_INCREMENT PRIMARY KEY,
        phone_number VARCHAR(40) NOT NULL UNIQUE,
        name VARCHAR(100) NOT NULL,
        game_id INT NOT NULL,
        votes INT DEFAULT 0,
        has_voted TINYINT(1) NOT NULL DEFAULT 0,
        is_liar TINYINT(1) NOT NULL DEFAULT 0,
        FOREIGN KEY (game_id) REFERENCES Game(game_id)
    )";
    $conn->query($sql);
    echo "[✔] Table 'Players' created.<br>";
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error creating 'Players' table: " . $e->getMessage() . "<br>";
}

// Placeholder questions – just to populate the table if empty
$questionSeed = ['who?', 'how?', 'when?', 'where?'];

try {
    $result = $conn->query("SELECT COUNT(*) AS total FROM Questions");
    $row = $result->fetch_assoc();

    if ($row['total'] == 0) {
        $stmt = $conn->prepare("INSERT INTO Questions (question_text) VALUES (?)");

        foreach ($questionSeed as $q) {
            $stmt->bind_param("s", $q);
            $stmt->execute();
            echo "[✔] Inserted question: '$q' <br>";
        }
    } else {
        echo "[i] Questions table already has data.<br>";
    }
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error seeding questions: " . $e->getMessage() . "<br>";
}

$conn->close();

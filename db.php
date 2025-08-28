<?php
include("DBconfig.php");

try {
    // Create Game table
    $sql = "CREATE TABLE IF NOT EXISTS Game (
        game_id INT AUTO_INCREMENT PRIMARY KEY,
        questions TEXT NOT NULL
    )";
    $conn->query($sql);
    error_log("[DB][schema] Table 'Game' created.");
} catch (mysqli_sql_exception $e) {
    error_log("[DB][schema][ERROR] Error creating 'Game' table: " . $e->getMessage());
}

try {
    // Create Questions table
    $sql = "CREATE TABLE IF NOT EXISTS Questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_text TEXT NOT NULL
    )";
    $conn->query($sql);
    error_log("[DB][schema] Table 'Questions' created.");
} catch (mysqli_sql_exception $e) {
    error_log("[DB][schema][ERROR] Error creating 'Questions' table: " . $e->getMessage());
}

try {
    // Create Players table
    $sql = "CREATE TABLE IF NOT EXISTS Players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone_number VARCHAR(40) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    game_id INT NOT NULL,
    is_liar TINYINT(1) NOT NULL DEFAULT 0,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (game_id) REFERENCES Game(game_id)
    );";
    $conn->query($sql);
    error_log("[DB][schema] Table 'Players' created.");
} catch (mysqli_sql_exception $e) {
    error_log("[DB][schema][ERROR] Error creating 'Players' table: " . $e->getMessage());
}

// Seed questions if table is empty
$questionSeed = ['who?', 'how?', 'when?', 'where?'];

try {
    $result = $conn->query("SELECT COUNT(*) AS total FROM Questions");
    $row = $result->fetch_assoc();

    if ($row['total'] == 0) {
        $stmt = $conn->prepare("INSERT INTO Questions (question_text) VALUES (?)");
        foreach ($questionSeed as $q) {
            $stmt->bind_param("s", $q);
            $stmt->execute();
            error_log("[DB][seed] Inserted question: '$q'");
        }
    } else {
        error_log("[DB][seed] Questions table already has data.");
    }
} catch (mysqli_sql_exception $e) {
    error_log("[DB][seed][ERROR] Error seeding questions: " . $e->getMessage());
}

// ⛔ NO CERRAR LA CONEXIÓN AQUÍ
// $conn->close();  <-- ELIMINADO

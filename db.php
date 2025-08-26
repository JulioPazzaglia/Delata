<?php
include("DBconfig.php");

try {
    // Create Group table
    $sql = "CREATE TABLE IF NOT EXISTS Groups (
        group_id INT AUTO_INCREMENT PRIMARY KEY,
        questions TEXT NOT NULL
    )";
    $conn->query($sql);
    echo "[✔] Table 'Groups' created.<br>";
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error creating 'Groups' table: " . $e->getMessage() . "<br>";
}

try {
    // Create Questions table
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
    // Create Players table
    $sql = "CREATE TABLE IF NOT EXISTS Players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone_number VARCHAR(40) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    group_id INT NOT NULL,
    is_liar TINYINT(1) NOT NULL DEFAULT 0,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (group_id) REFERENCES Groups(group_id)
    );";
    $conn->query($sql);
    echo "[✔] Table 'Players' created.<br>";
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error creating 'Players' table: " . $e->getMessage() . "<br>";
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
            echo "[✔] Inserted question: '$q' <br>";
        }
    } else {
        echo "[i] Questions table already has data.<br>";
    }
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error seeding questions: " . $e->getMessage() . "<br>";
}

// ⛔ NO CERRAR LA CONEXIÓN AQUÍ
// $conn->close();  <-- ELIMINADO

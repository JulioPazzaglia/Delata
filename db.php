<?php
include("DBconfig.php");

// Ensure InnoDB + UTF8MB4 for FK & emojis if needed
$conn->query("SET NAMES utf8mb4");

// ---- Create `groups` table ----
try {
    $sql = "CREATE TABLE IF NOT EXISTS groups (
        group_id     INT AUTO_INCREMENT PRIMARY KEY,
        admin_wa_id  VARCHAR(20) NOT NULL,
        created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($sql);
    echo "[✔] Table 'groups' created.<br>";
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error creating 'groups' table: " . $e->getMessage() . "<br>";
}

// ---- Create `questions` table ----
try {
    $sql = "CREATE TABLE IF NOT EXISTS questions (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        question_text TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($sql);
    echo "[✔] Table 'questions' created.<br>";
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error creating 'questions' table: " . $e->getMessage() . "<br>";
}

// ---- Create `players` table ----
// player_id = wa_id (E.164 without '+') as PRIMARY KEY to avoid duplicates
try {
    $sql = "CREATE TABLE IF NOT EXISTS players (
        player_id   VARCHAR(20) PRIMARY KEY,   -- wa_id
        group_id    INT NULL,
        name        VARCHAR(80) NOT NULL,
        is_liar     TINYINT(1) NOT NULL DEFAULT 0,
        is_admin    TINYINT(1) NOT NULL DEFAULT 0,
        active      TINYINT(1) NOT NULL DEFAULT 1,
        joined_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_players_group
          FOREIGN KEY (group_id) REFERENCES groups(group_id)
          ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($sql);

    // Useful index for lookups by group
    $conn->query("CREATE INDEX IF NOT EXISTS idx_players_group ON players(group_id);");
    echo "[✔] Table 'players' created (with FK & index).<br>";
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error creating 'players' table: " . $e->getMessage() . "<br>";
}

// ---- Seed questions if empty ----
$questionSeed = ['who?', 'how?', 'when?', 'where?'];

try {
    $result = $conn->query("SELECT COUNT(*) AS total FROM questions");
    $row = $result->fetch_assoc();

    if ((int)$row['total'] === 0) {
        $stmt = $conn->prepare("INSERT INTO questions (question_text) VALUES (?)");
        foreach ($questionSeed as $q) {
            $stmt->bind_param("s", $q);
            $stmt->execute();
            echo "[✔] Inserted question: '{$q}'<br>";
        }
        $stmt->close();
    } else {
        echo "[i] 'questions' table already has data.<br>";
    }
} catch (mysqli_sql_exception $e) {
    echo "[✖] Error seeding 'questions': " . $e->getMessage() . "<br>";
}

// ⛔ Do NOT close the connection here; other scripts may keep using $conn
// $conn->close();

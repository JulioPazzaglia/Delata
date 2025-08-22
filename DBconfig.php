<?php
// DBconfig.php
// Creates (if not exists) and connects to the MySQL database using mysqli
// - Debug/logs in English
// - UTF8MB4 charset
// - Strict error handling (exceptions)

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'delataDB';

// Throw exceptions on mysqli errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Connect without selecting DB first (so we can CREATE DATABASE IF NOT EXISTS)
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
    // Set charset early (for safety on connections that may run statements pre-select_db)
    $conn->set_charset('utf8mb4');

    // Optional: enforce strict sql_mode
    $conn->query("SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

    // Create database if it doesn't exist (guard name with backticks)
    $dbNameSafe = preg_replace('/[^a-zA-Z0-9_]/', '', $DB_NAME); // basic hardening
    if ($dbNameSafe !== $DB_NAME) {
        throw new RuntimeException("Unsafe database name provided.");
    }
    $conn->query("CREATE DATABASE IF NOT EXISTS `{$DB_NAME}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

    // Select database
    $conn->select_db($DB_NAME);

    // Ensure connection charset (again) after selecting DB
    $conn->set_charset('utf8mb4');

    // Optional: you can log a short success (server logs)
    error_log("[DB] Connected to database '{$DB_NAME}' on {$DB_HOST}");
} catch (Throwable $e) {
    // Do not echo sensitive details to users; log instead.
    error_log("[DB] Connection/creation error: " . $e->getMessage());
    http_response_code(500);
    die("Database connection error"); // keep generic
}

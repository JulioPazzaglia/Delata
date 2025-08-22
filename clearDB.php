<?php
// Maintenance helpers for groups & players (debug/admin use)
// - Debug & comments in English
// - Player-facing messages should NOT be emitted here

/**
 * Truncate an allowed table (safe whitelist).
 * NOTE: Use only for debug/reset. This is a destructive operation.
 */
function truncateTable(mysqli $conn, string $tableName): void
{
    try {
        $allowed = ['groups', 'players'];
        if (!in_array($tableName, $allowed, true)) {
            echo "Refused to truncate unknown table: {$tableName}<br>";
            return;
        }
        $sql = "TRUNCATE TABLE `{$tableName}`";
        $conn->query($sql);
        echo "Table {$tableName} truncated successfully.<br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error truncating table {$tableName}: " . $e->getMessage() . "<br>";
    }
}

/**
 * Delete a specific group by group_id.
 * Players will be removed via FK ON DELETE CASCADE if configured.
 */
function deleteGroup(mysqli $conn, int $group_id): void
{
    try {
        $sql = "DELETE FROM `groups` WHERE `group_id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        echo "Group {$group_id} deleted. Affected rows: {$affected}.<br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error deleting group {$group_id}: " . $e->getMessage() . "<br>";
    }
}

/**
 * Delete all players by group_id (useful if you don't rely on FK cascade).
 */
function deletePlayersByGroupId(mysqli $conn, int $group_id): void
{
    try {
        $sql = "DELETE FROM `players` WHERE `group_id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        echo "Players from group {$group_id} deleted. Affected rows: {$affected}.<br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error deleting players for group {$group_id}: " . $e->getMessage() . "<br>";
    }
}

/**
 * Drop a database (extremely destructive).
 * Guarded by a confirmation flag to avoid accidents.
 */
function dropDB(mysqli $conn, string $dbname, bool $confirm = false): void
{
    try {
        if (!$confirm) {
            echo "Refused to drop database {$dbname}: confirmation flag is false.<br>";
            return;
        }
        // Minimal safety guard: refuse dropping system databases
        $protected = ['mysql', 'information_schema', 'performance_schema', 'sys'];
        if (in_array($dbname, $protected, true)) {
            echo "Refused to drop protected database: {$dbname}.<br>";
            return;
        }

        $sql = "DROP DATABASE `{$dbname}`";
        $conn->query($sql);
        echo "Database {$dbname} dropped successfully.<br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error dropping database {$dbname}: " . $e->getMessage() . "<br>";
    }
}

// ---- Usage examples (keep commented in production) ----
// truncateTable($conn, 'players');
// truncateTable($conn, 'groups');
// deletePlayersByGroupId($conn, 123);
// deleteGroup($conn, 123);
// dropDB($conn, 'delata_debug', true);

<?php
// players.php (data access layer for players)
// - Debug/logs in English (error_log)
// - No user-facing echoes here
// - player_id is the wa_id (E.164 without '+')

/**
 * Create a player inside a group.
 * Idempotent on duplicate player_id (silently ignores duplicate).
 */
function createPlayer(mysqli $conn, string $wa_id, string $name, int $group_id, bool $is_admin): bool
{
    try {
        $sql = "INSERT INTO `players` (player_id, group_id, name, is_admin)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $admin = $is_admin ? 1 : 0;
        $stmt->bind_param("sisi", $wa_id, $group_id, $name, $admin);
        $stmt->execute();
        $stmt->close();
        return true;
    } catch (mysqli_sql_exception $e) {
        // Duplicate primary key -> already exists; treat as idempotent success
        if ((int)$e->getCode() === 1062) {
            error_log("createPlayer: duplicate player_id {$wa_id}, treated as ok.");
            return true;
        }
        error_log("createPlayer error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check whether a player exists by wa_id.
 */
function playerExists(mysqli $conn, string $wa_id): bool
{
    try {
        $sql = "SELECT 1 FROM `players` WHERE `player_id` = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $wa_id);
        $stmt->execute();
        $stmt->bind_result($one);
        $exists = $stmt->fetch() ? true : false;
        $stmt->close();
        return $exists;
    } catch (mysqli_sql_exception $e) {
        error_log("playerExists error: " . $e->getMessage());
        return false;
    }
}

/**
 * Fetch all player_ids (wa_id) for a given group_id.
 * Returns array<string> or empty array if none.
 */
function fetchPlayers(mysqli $conn, int $group_id): array
{
    try {
        $sql = "SELECT `player_id` FROM `players` WHERE `group_id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $ids = [];
        while ($row = $res->fetch_assoc()) {
            $ids[] = $row['player_id'];
        }
        $stmt->close();
        return $ids;
    } catch (mysqli_sql_exception $e) {
        error_log("fetchPlayers error: " . $e->getMessage());
        return [];
    }
}

/**
 * (Optional) Update player's group and name (simple reassignment).
 */
function updatePlayerGroupAndName(mysqli $conn, string $wa_id, int $group_id, string $name): bool
{
    try {
        $sql = "UPDATE `players` SET `group_id` = ?, `name` = ? WHERE `player_id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $group_id, $name, $wa_id);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0; // true even if same values
        $stmt->close();
        return $ok;
    } catch (mysqli_sql_exception $e) {
        error_log("updatePlayerGroupAndName error: " . $e->getMessage());
        return false;
    }
}

/**
 * (Optional) Set/unset admin flag for a player.
 */
function setPlayerAdmin(mysqli $conn, string $wa_id, bool $is_admin): bool
{
    try {
        $sql = "UPDATE `players` SET `is_admin` = ? WHERE `player_id` = ?";
        $stmt = $conn->prepare($sql);
        $admin = $is_admin ? 1 : 0;
        $stmt->bind_param("is", $admin, $wa_id);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    } catch (mysqli_sql_exception $e) {
        error_log("setPlayerAdmin error: " . $e->getMessage());
        return false;
    }
}

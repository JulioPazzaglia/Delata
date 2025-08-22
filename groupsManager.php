<?php

/**
 * Create a new group and return its group_id.
 * Admin will be set later (e.g., when calling createGroupWithAdmin).
 * Debug messages in English; do not send user-facing text here.
 */
function createGroup(mysqli $conn): ?int
{
    try {
        // If you want to attach questions here, fetch them and store in a separate table or new column.
        // $questions = fetchQuestions($conn); // <- keep if you still need it elsewhere

        $sql  = "INSERT INTO `groups` (`admin_wa_id`) VALUES ('pending')";
        $conn->query($sql);

        $newId = (int)$conn->insert_id;
        echo "Group created successfully. ID: {$newId}<br>";
        return $newId;
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error while creating group: " . $e->getMessage() . "<br>";
        return null;
    }
}

/**
 * Check whether a group exists by group_id.
 */
function groupExists(mysqli $conn, int $group_id): bool
{
    $stmt = $conn->prepare("SELECT 1 FROM `groups` WHERE `group_id` = ? LIMIT 1");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $stmt->bind_result($one);
    $exists = $stmt->fetch() ? true : false;
    $stmt->close();
    return $exists;
}

/**
 * Randomly select a liar within a group.
 * - Resets all players' is_liar to 0 for that group
 * - Picks one random player_id and sets is_liar = 1
 *
 * Assumes schema:
 *   players(player_id VARCHAR PK, group_id INT, is_liar TINYINT)
 */
function selectLiar(mysqli $conn, int $group_id): void
{
    try {
        // Fetch all player_ids for the given group
        $stmt = $conn->prepare("SELECT `player_id` FROM `players` WHERE `group_id` = ?");
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $playerIds = [];
        while ($row = $res->fetch_assoc()) {
            $playerIds[] = $row['player_id']; // wa_id strings
        }
        $stmt->close();

        if (count($playerIds) === 0) {
            echo "No players found in group {$group_id}. Cannot select liar.<br>";
            return;
        }

        // Reset all to not liar
        $stmt = $conn->prepare("UPDATE `players` SET `is_liar` = 0 WHERE `group_id` = ?");
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $stmt->close();

        // Pick one at random
        shuffle($playerIds);
        $chosen = $playerIds[0];

        // Set chosen player as liar
        $stmt = $conn->prepare("UPDATE `players` SET `is_liar` = 1 WHERE `player_id` = ? AND `group_id` = ?");
        $stmt->bind_param("si", $chosen, $group_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected > 0) {
            echo "Liar selected successfully for group {$group_id}. Player: {$chosen}<br>";
        } else {
            echo "No player updated as liar for group {$group_id}.<br>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error while selecting liar: " . $e->getMessage() . "<br>";
    }
}

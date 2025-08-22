<?php
// questionsHandler.php
// Data access for questions
// - Debug/logs in English (error_log)
// - No user-facing echoes here

/**
 * Fetch all questions from the `questions` table.
 * @return string[] list of question_text
 */
function fetchQuestions(mysqli $conn): array
{
    try {
        $sql = "SELECT `question_text` FROM `questions`";
        $res = $conn->query($sql);

        $out = [];
        if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $out[] = $row['question_text'];
            }
        }
        return $out;
    } catch (mysqli_sql_exception $e) {
        error_log("fetchQuestions error: " . $e->getMessage());
        return [];
    }
}

/**
 * (Optional) Fetch a comma-separated questions string stored in `groups.questions`.
 * Returns the raw string or null if not found / column missing.
 *
 * NOTE: This requires a `questions` TEXT column in `groups`.
 * If you didn't add it, this will log an error and return null gracefully.
 */
function fetchGroupQuestions(mysqli $conn, int $group_id): ?string
{
    try {
        $stmt = $conn->prepare("SELECT `questions` FROM `groups` WHERE `group_id` = ? LIMIT 1");
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        return $row ? ($row['questions'] ?? null) : null;
    } catch (mysqli_sql_exception $e) {
        // If column doesn't exist (e.g., Error 1054), just log and return null
        error_log("fetchGroupQuestions error: " . $e->getMessage());
        return null;
    }
}

/**
 * Split a comma-separated questions string into a trimmed array.
 * Accepts null and returns [].
 * @param ?string $questionsCSV
 * @return string[]
 */
function extractQuestions(?string $questionsCSV): array
{
    if ($questionsCSV === null || $questionsCSV === '') return [];
    $parts = array_map('trim', explode(',', $questionsCSV));
    // Remove empties
    return array_values(array_filter($parts, fn($s) => $s !== ''));
}

/**
 * (Optional) Consume the first question from `groups.questions` (comma-separated)
 * and persist the remainder back to the group.
 *
 * Returns:
 *  - string|null The consumed question text (first item), or null if none/failed.
 *
 * NOTE: Requires `groups.questions` column. If you don't have it, this will log and return null.
 */
function consumeFirstGroupQuestion(mysqli $conn, int $group_id): ?string
{
    try {
        $current = fetchGroupQuestions($conn, $group_id);
        $arr = extractQuestions($current);
        if (count($arr) === 0) {
            return null; // nothing to consume
        }

        $first = array_shift($arr);
        $newCSV = implode(', ', $arr);

        $stmt = $conn->prepare("UPDATE `groups` SET `questions` = ? WHERE `group_id` = ?");
        $stmt->bind_param("si", $newCSV, $group_id);
        $stmt->execute();
        $stmt->close();

        return $first;
    } catch (mysqli_sql_exception $e) {
        error_log("consumeFirstGroupQuestion error: " . $e->getMessage());
        return null;
    }
}

/**
 * Utility: pick N random questions from the master list (no persistence).
 * For small datasets RAND() is fine; for large, consider better sampling.
 * @return string[]
 */
function pickRandomQuestions(mysqli $conn, int $count = 3): array
{
    try {
        // Simple approach with ORDER BY RAND() for debug / small tables
        $stmt = $conn->prepare("SELECT `question_text` FROM `questions` ORDER BY RAND() LIMIT ?");
        $stmt->bind_param("i", $count);
        $stmt->execute();
        $res = $stmt->get_result();

        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[] = $row['question_text'];
        }
        $stmt->close();
        return $out;
    } catch (mysqli_sql_exception $e) {
        error_log("pickRandomQuestions error: " . $e->getMessage());
        return [];
    }
}

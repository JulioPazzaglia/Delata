<?php

// Function to Clear all rows from table
function clearTable(mysqli $conn, string $tableName) {
    $allowed = ['Players', 'Questions', 'Groups'];
    if (!in_array($tableName, $allowed, true)) {
        error_log("[DB][clearTable] Forbidden table name $tableName");
        return;
    }
    $sql = sprintf("DELETE FROM `%s`;", $tableName);
    $conn->query($sql);

    error_log("[DB][clearTable] Table $tableName cleared");
}


// Function to delete a specific ID from a table group
function deleteGroupById(mysqli $conn, int $group_id)
{
    try {
        $sql  = "DELETE FROM `Groups` WHERE `group_id` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_id);
$stmt->execute();
error_log("[DB][deleteGroupById] Group $group_id deleted");

        echo "$group_id was deleted from Groups <br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error deleting ID: " . $e->getMessage() . "<br>";
    }
}

// Function to delete players by group_id
function deletePlayersByGroupId(mysqli $conn, int $group_id)
{
    try {
        $sql  = "DELETE FROM `Groups` WHERE `group_id` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_id);
$stmt->execute();

        echo "Players from group: $group_id were deleted <br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error deleting players: " . $e->getMessage() . "<br>";
    }
}

// Function to drop a database
function dropDB(mysqli $conn, string $dbname)
{
    try {
        $sql = "DROP DATABASE $dbname";
        $conn->query($sql);
        echo "Database dropped successfully <br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error dropping database: " . $e->getMessage() . "<br>";
    }
}

//dropDB($conn, $dbname);

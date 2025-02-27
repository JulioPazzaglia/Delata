<?php
include("../DBconfig.php");

// Function to delete a table
function deleteTable($conn, $tableName)
{
    try {
        $sql = "DELETE FROM $tableName;";
        $conn->query($sql);
        echo "Table $tableName deleted successfully <br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error deleting table: " . $e->getMessage() . "<br>";
    }
}

// Function to delete a specific ID from a table game
function deleteId($conn, $table, $id)
{
    try {
        $sql = "DELETE FROM $table WHERE $table.game_id = $id";
        $conn->query($sql);
        echo "$id was deleted from $table <br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error deleting ID: " . $e->getMessage() . "<br>";
    }
}

// Function to delete players by game_id
function deletePlayers($conn, $game_id)
{
    try {
        $sql = "DELETE FROM players WHERE players.game_id = $game_id";
        $conn->query($sql);
        echo "Players from game: $game_id were deleted <br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error deleting players: " . $e->getMessage() . "<br>";
    }
}

// Function to drop a database
function dropDB($conn, $dbname)
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

<?php
include("../DBconfig.php");

/*
Este archivo va a limpiar la DB
*/

// delete tables
function deleteTable($conn, $tableName)
{
    $sql = "DELETE FROM $tableName;";

    if ($conn->query($sql) === TRUE) {
        echo "table $tableName deleted succesfully <br>";
    } else {
        echo "table $tableName deletion error : $conn->error <br>";
    }
}

//Delete specific id (Questions or Games) from table
function deleteId($conn, $table, $id)
{
    $sql = "DELETE FROM $table WHERE $table.id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "$id was deleted from $table <br>";
    } else {
        echo "error deleting $id from $table: $conn->error <br>";
    }
}

function deletePlayers($conn, $game_id)
{
    $sql = "DELETE FROM players WHERE players.game_id = $game_id";

    if ($conn->query($sql) === TRUE) {
        echo "players from game: $game_id where deleted <br>";
    } else {
        echo "error deleting players from table $game_id: $conn->error <br>";
    }
}

//drop DB
function dropDB($conn, $dbname)
{
    $sql = "DROP DATABASE $dbname";

    if ($conn->query($sql) === TRUE) {
        echo "DB droped <br>";
    } else {
        echo "error dropping DB <br>";
    }
}

//deleteTable($conn, "game");
//deleteTable($conn, "players");
//deleteTable($conn, "questions");
//deletePlayers($conn, 1);
dropDB($conn, $dbname);

$conn->close();

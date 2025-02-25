<?php
include("../gameCreation.php");

/*
Este archivo va a limpiar la DB
*/

// delete tables
function deleteTable($conn, $tableName)
{
    $sql = "DELETE FROM $tableName;";

    if ($conn->query($sql) === TRUE) {
        echo "table $tableName deleted succesfully";
    } else {
        echo "table $tableName deletion error : " . $conn->error;
    }
}

deleteTable($conn, "game");
// deleteTable($conn, "players");
// deleteTable($conn, "questions");
$conn->close();

<?php
/*
creacion de los usuarios
*/

function createPlayer($conn, $num, $name, $game)
{
    try {
        $sql = "INSERT INTO `players`(`number`, `name`, `game_id`, `votes`, `hasVoted`) 
                VALUES ('$num','$name','$game',0,0)";

        if ($conn->query($sql) === TRUE) {
            echo "User $name created successfully <br>";
        } else {
            echo "Error creating user: " . $conn->error . "<br>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error: " . $e->getMessage() . "<br>";
    }
}

<?php
/*
creacion de los usuarios
*/

function createUser($conn, $num, $name, $game)
{
    $sql = "INSERT INTO `players`(`number`, `name`, `game_id`, `votes`, `hasVoted`) VALUES ('$num','$name','$game',0,0)";
    if ($conn->query($sql) === TRUE) {
        echo "User $name created succesfully <br>";
    } else {
        echo "Error creating user: $conn->error <br>";
    }
}

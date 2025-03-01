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

function hasVoted($conn, $num)
{
    try {
        $sql = "SELECT hasVoted FROM Players WHERE num = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $num);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['hasVoted'];
        }
        return null;
    } catch (mysqli_sql_exception $e) {
        echo "Error fetching hasVoted: " . $e->getMessage() . "<br>";
        return null;
    }
}

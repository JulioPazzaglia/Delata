<?php
/*
manejo de las preguntas
*/

function fetchGameQuestions($conn, $game_id)
{
    try {
        $sql = "SELECT questions FROM game WHERE game.game_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $game_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row["questions"];
        }
    } catch (mysqli_sql_exception $e) {
        echo "Error fetching hasVoted: " . $e->getMessage() . "<br>";
        return null;
    }
}

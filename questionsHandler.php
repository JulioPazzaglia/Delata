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

function extractQuestions($questions)
{
    $array = explode(",", $questions);
    return $array;
}

function insertQuestions($conn, $game_id)
{
    try {
        $questions = fetchGameQuestions($conn, $game_id);
        $questionsArray = extractQuestions($questions);
        $newQuestionsArray = array_slice($questionsArray, 1);
        $questionsStr = implode(", ", $newQuestionsArray);

        $sql = "UPDATE game SET questions = '$questionsStr' WHERE game_id = $game_id;";
        if ($conn->query($sql) === TRUE) {
            echo "\n questions updated <br>";
        } else {
            echo "\nError updating questions: " . $conn->error . "<br>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error while InsertQuestions: " . $e->getMessage() . "<br>";
    }
};

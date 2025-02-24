<?php
include("DBconfig.php");


/*
Creacion de la tabla game
*/

function fetchQuestions($conn)
{
    $sql = "SELECT questions FROM Questions";
    $result = $conn->query($sql);
    $questionsArray = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $questionsArray[] = $row['questions'];
        }
    }
    return $questionsArray;
}
function createGame($conn)
{
    $questions = fetchQuestions($conn);
    shuffle($questions);
    $questions = array_slice($questions, 0, 3);
    $questions = implode(", ", $questions);

    $sql = "INSERT INTO `game`(`questions`) VALUES ('[$questions]');";
    if ($conn->query($sql) === TRUE) {
        echo "Game created succesfully";
    } else {
        echo "Error creating game: " . $conn->error;
    }
}

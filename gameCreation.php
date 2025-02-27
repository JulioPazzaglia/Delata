<?php


/*
Tabel create game
*/

// Function to fetch questions
function fetchQuestions($conn)
{
    try {
        $sql = "SELECT questions FROM Questions";
        $result = $conn->query($sql);
        $questionsArray = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $questionsArray[] = $row['questions'];
            }
        }
        return $questionsArray;
    } catch (mysqli_sql_exception $e) {
        echo "Error fetching questions: " . $e->getMessage() . "<br>";
        return [];
    }
}

// Function to create a game
function createGame($conn)
{
    try {
        $questions = fetchQuestions($conn);
        shuffle($questions);
        $questions = array_slice($questions, 0, 3);
        $questionsStr = implode(", ", $questions);

        $sql = "INSERT INTO `Game`(`questions`) VALUES ('$questionsStr');";
        if ($conn->query($sql) === TRUE) {
            echo "\n Game created successfully, ID: " . $conn->insert_id . "<br>";
        } else {
            echo "\nError creating game: " . $conn->error . "<br>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error while creating game: " . $e->getMessage() . "<br>";
    }
}

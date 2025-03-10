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
            return  $conn->insert_id;
        } else {
            echo "\nError creating game: " . $conn->error . "<br>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error while creating game: " . $e->getMessage() . "<br>";
    }
}

function fetchPlayers($conn, $game_id)
{
    try {
        $sql = "SELECT id FROM players WHERE players.game_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $game_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ids = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $ids[] = $row['id'];
            }
        }
        return $ids;
    } catch (mysqli_sql_exception $e) {
        echo "Error fetching hasVoted: " . $e->getMessage() . "<br>";
        return null;
    }
}


function selectLiar($conn, $game_id)
{
    try {
        $players = fetchPlayers($conn, $game_id);
        shuffle($players);

        $sql = "UPDATE `players` SET `isLiar`='1' WHERE `id` = $players[0]";
        if ($conn->query($sql) === TRUE) {
            echo "\n Liar selected successfully <br>";
        } else {
            echo "\nError creating game: " . $conn->error . "<br>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error while creating game: " . $e->getMessage() . "<br>";
    }
}

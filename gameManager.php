<?php
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

function gameExists($conn, $game_id)
{
    $stmt = $conn->prepare("SELECT game_id FROM Game WHERE game_id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
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
            echo "\nError seelcting liar: " . $conn->error . "<br>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error selectingLiar: " . $e->getMessage() . "<br>";
    }
}

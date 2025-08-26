<?php
function createGroup($conn)
{
    try {
        $questions = fetchQuestions($conn);
        shuffle($questions);
        $questions = array_slice($questions, 0, 3);
        $questionsStr = implode(", ", $questions);

        $sql = "INSERT INTO `Group`(`questions`) VALUES ('$questionsStr');";
        if ($conn->query($sql) === TRUE) {
            echo "\n Group created successfully, ID: " . $conn->insert_id . "<br>";
            return  $conn->insert_id;
        } else {
            echo "\nError creating group: " . $conn->error . "<br>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error while creating group: " . $e->getMessage() . "<br>";
    }
}

function groupExists($conn, $group_id)
{
    $stmt = $conn->prepare("SELECT group_id FROM Group WHERE group_id = ?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

function selectLiar($conn, $group_id)
{
    try {
        $players = fetchPlayers($conn, $group_id);
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

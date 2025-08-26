<?php
/*
manejo de las preguntas
*/

function fetchQuestions($conn)
{
    try {
        $sql = "SELECT question_text FROM Questions";
        $result = $conn->query($sql);
        $questionsArray = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $questionsArray[] = $row['question_text'];
            }
        }
        return $questionsArray;
    } catch (mysqli_sql_exception $e) {
        echo "Error fetching questions: " . $e->getMessage() . "<br>";
        return [];
    }
}


function fetchGroupQuestions($conn, $group_id)
{
    try {
        $sql = "SELECT questions FROM group WHERE group.group_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $group_id);
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

function insertQuestions($conn, $group_id)
{
    try {
        $questions = fetchGroupQuestions($conn, $group_id);
        $questionsArray = extractQuestions($questions);
        $newQuestionsArray = array_slice($questionsArray, 1);
        $questionsStr = implode(", ", $newQuestionsArray);

        $sql = "UPDATE group SET questions = '$questionsStr' WHERE ggroup_id = $group_id;";
        if ($conn->query($sql) === TRUE) {
            echo "\n questions updated <br>";
        } else {
            echo "\nError updating questions: " . $conn->error . "<br>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error while InsertQuestions: " . $e->getMessage() . "<br>";
    }
};

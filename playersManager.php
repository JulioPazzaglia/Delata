<?php
/*
creacion de los usuarios
*/

function createPlayer($conn, $phoneNumber, $name, $game_id, $is_admin = false)
{
    try {
        $stmt = $conn->prepare("INSERT INTO Players (phone_number, name, game_id, is_admin) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $phoneNumber, $name, $game_id, $is_admin);
        $stmt->execute();
        echo "<br>✅ Player $name added to game $game_id (admin: " . ($is_admin ? "yes" : "no") . ")";
    } catch (mysqli_sql_exception $e) {
        echo "SQL Error adding player: " . $e->getMessage() . "<br>";
    }
}


function hasVoted($conn, $num)
{
    try {
        $sql = "SELECT hasVoted FROM players WHERE players.number = ?";
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

function playerExists($conn, $phoneNumber)
{
    try {
        $sql = "SELECT 1 FROM players WHERE phone_number = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $phoneNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    } catch (mysqli_sql_exception $e) {
        echo "Error checking player existence: " . $e->getMessage() . "<br>";
        return false;
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
        echo "Error fetching players: " . $e->getMessage() . "<br>";
        return null;
    }
}
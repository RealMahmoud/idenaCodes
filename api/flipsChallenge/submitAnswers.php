

<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');
//todo : prevent sumbitting twice
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $banned = $data[1];
    if ($banned) {
        $result = (object) array();
        $result->error = true;
        die(json_encode($result));
    }
} else {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}

$answers = json_decode(file_get_contents('php://input'), true);

$resultSQL = $conn->query("SELECT flips FROM `test_flips` WHERE userID = '" . $loggedUserID . "' LIMIT 1;")->fetch_assoc()["flips"];

$rightAnswers = 0;
$totalFlips = 0;
foreach (json_decode($resultSQL) as $flipID) {
    $answer = $conn->query("SELECT answer FROM `flips` WHERE id = '" . $flipID . "' LIMIT 1;")->fetch_assoc()['answer'];

    if (!is_bool(array_search($flipID, array_column($answers, 'id')))) {
        if ($answer == $answers[array_search($flipID, array_column($answers, 'id'))]["answer"]) {
            $rightAnswers += 1;
        }
    }

    $totalFlips += 1;
}

(int) $score = (float) ($rightAnswers / $totalFlips) * 100;

$conn->query("UPDATE `test_flips` SET `score`= '" . $score . "' WHERE `userID` = '" . $loggedUserID . "' LIMIT 1;");

$result = (object) array();
$result->error = false;
$result->score = $score;

echo json_encode($result);



<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');
$result = (object) array();
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $banned = $data[1];
    if ($banned) {
        
        $result->error = true;
        $result->reason = "Banned";
        die(json_encode($result));
    }
} else {
    
    $result->error = true;
    $result->reason = "Not logged in";
    die(json_encode($result));
}

$answers = json_decode(file_get_contents('php://input'), true);

$oldFlips = $conn->query("SELECT `flips`,`score` FROM `test_flips` where `userID` = '" . $loggedUserID . "' LIMIT 1;")->fetch_assoc();
if ($oldFlips) {
    if (isset($oldFlips[1])) {
        
        $result->error = true;
        $result->reason = "Already submitted";
        die(json_encode($result));
    } else {
        $rightAnswers = 0;
        $totalFlips = 0;
        foreach (json_decode($oldFlips[0]) as $flipID) {
            $answer = $conn->query("SELECT `answer` FROM `flips` where `id` = '" . $flipID . "' LIMIT 1;")->fetch_assoc()['answer'];
            if (!is_bool(array_search($flipID, array_column($answers, 'id')))) {
                if ($answer == $answers[array_search($flipID, array_column($answers, 'id'))]["answer"]) {
                    $rightAnswers += 1;
                }
            }
            $totalFlips += 1;
        }
        (int) $score = (float) ($rightAnswers / $totalFlips) * 100;
        $conn->query("UPDATE `test_flips` SET `score`= '" . $score . "' WHERE `userID` = '" . $loggedUserID . "' LIMIT 1;");
        
        $result->error = false;
        $result->score = $score;
        die(json_encode($result));
    }
} else {
    
    $result->error = true;
    $result->reason = "NULL";
    die(json_encode($result));
}

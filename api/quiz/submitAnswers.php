<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');
$result = (object) array();
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $conn->query("UPDATE `users` SET `lastseen` = CURDATE() WHERE `id` = '" . $loggedUserID . "';");
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
$oldQuestions = $conn->query("SELECT `questions`,`score` FROM `test_questions` where `userID` = '" . $loggedUserID . "' LIMIT 1;")->fetch_assoc();
if ($oldQuestions) {
    if (isset($oldQuestions["score"])) {

        $result->error = true;
        $result->reason = "Already submitted";
        die(json_encode($result));
    } else {
        $rightAnswers = 0;
        $totalQuestions = 0;
        foreach (json_decode($oldQuestions["questions"]) as $questionID) {
            $answer = $conn->query("SELECT `answer` FROM `questions` where `id` = '" . $questionID . "' LIMIT 1;")->fetch_assoc()['answer'];

            if (!is_bool(array_search($questionID, array_column($answers, 'id')))) {
                if ($answer == $answers[array_search($questionID, array_column($answers, 'id'))]["answer"]) {
                    $rightAnswers += 1;
                }
            }
            $totalQuestions += 1;
        }
        (int) $score = (float) ($rightAnswers / $totalQuestions) * 100;
        $conn->query("UPDATE `test_questions` SET `score`= '" . $score . "' WHERE `userID`");

        $result->error = false;
        $result->score = $score;
        die(json_encode($result));
    }
} else {

    $result->error = true;
    $result->reason = "NULL";
    die(json_encode($result));
}

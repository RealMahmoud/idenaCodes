<?php
session_start();
include(dirname(__FILE__) . "/../../common/_public.php");
header('Content-Type: application/json');

if (isset($_SESSION['CODES-Token'])) {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row()[0];
} else {
    $result = (object)array();
    $result->error=true;
    die(json_encode($result));
}
$answers = json_decode(file_get_contents('php://input'), true);
$oldQuestions = $conn->query("SELECT questions,score FROM `test_questions` WHERE userID = '" . $loggedUserID . "' LIMIT 1;")->fetch_assoc();
if ($oldQuestions) {
    if (isset($oldQuestions["score"])) {
        $result        = (object) array();
        $result->error = true;
        $result->reason = "Already submitted";
        die(json_encode($result));
    } else {
        $rightAnswers = 0;
        $totalQuestions = 0;
        foreach (json_decode($oldQuestions["questions"]) as $questionID) {
            $answer = $conn->query("SELECT answer FROM `questions` WHERE id = '" . $questionID . "' LIMIT 1;")->fetch_assoc()['answer'];
    
            if (!is_bool(array_search($questionID, array_column($answers, 'id')))) {
                if ($answer == $answers[array_search($questionID, array_column($answers, 'id'))]["answer"]) {
                    $rightAnswers +=1;
                }
            }
            $totalQuestions +=1;
        }
        (int)$score = (float)($rightAnswers/$totalQuestions)*100;
        $conn->query("UPDATE `test_questions` SET `score`= '".$score."' WHERE `userID`");
        $result = (object)array();
        $result->error=false;
        $result->score=$score;
        die(json_encode($result));
    }
} else {
    $result = (object)array();
    $result->error=true;
    die(json_encode($result));
}

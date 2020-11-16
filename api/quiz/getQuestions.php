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
$oldQuestions = $conn->query("SELECT `questions`,`score` FROM `test_questions` where `userID` = '" . $loggedUserID . "' LIMIT 1;")->fetch_assoc();

if ($oldQuestions) {
    if (isset($oldQuestions["score"])) {
        
        $result->error = true;
        $result->reason = "Already submitted";
        die(json_encode($result));
    } else {
        $questionsArray = array();
        
        foreach (json_decode($oldQuestions["questions"]) as $qID) {
            $resultSQL = $conn->query("SELECT * FROM questions where `id` = '" . $qID . "';");

            while ($row = $resultSQL->fetch_assoc()) {
                $question = (object) array();
                $question->id = (int) $row['id'];
                $question->question = $row['question'];
                $question->options = json_decode($row['options']);
                array_push($questionsArray, $question);
            }
        }

        if (count($questionsArray) == 0) {
            $result->error = true;
            $result->reason = "0 Questions error";
        } else {
            $result->error = false;
            $result->questions = $questionsArray;
        }

        die(json_encode($result));
    }
}
;


$resultSQL = $conn->query("SELECT * FROM `questions`  WHERE `enabled` = '1' ORDER BY RAND() LIMIT 15;");
$questionsArray = array();
$questionsIDArray = array();
while ($row = $resultSQL->fetch_assoc()) {
    $question = (object) array();
    $question->id = (int) $row['id'];
    $question->question = $row['question'];
    $question->options = json_decode($row['options']);
    array_push($questionsArray, $question);
    array_push($questionsIDArray, (int) $row['id']);
}

$conn->query("INSERT INTO `test_questions`( `userID`, `questions`) VALUES ('" . $loggedUserID . "','" . json_encode($questionsIDArray) . "')");

if (count($questionsArray) == 0) {
    $result->error = true;
    $result->reason = "0 Questions error";
} else {
    $result->error = false;
    $result->questions = $questionsArray;
}
die(json_encode($result));

<?php
session_start();
include(dirname(__FILE__) . "/../../common/_public.php");
header('Content-Type: application/json');

 $userID = $conn->query("SELECT id FROM `users` WHERE address = (SELECT address FROM `auth_idena` WHERE token = '".$_SESSION['CODES-Token']."');")->fetch_row()[0];




$answers = json_decode(file_get_contents('php://input'), true);



$resultSQL = $conn->query("SELECT questions FROM `test_questions` WHERE userID = '" . $userID . "' LIMIT 1;")->fetch_assoc()["questions"];


$rightAnswers = 0;
$totalQuestions = 0;
foreach (json_decode($resultSQL) as $questionID) {
    $answer = $conn->query("SELECT answer FROM `questions` WHERE id = '" . $questionID . "' LIMIT 1;")->fetch_assoc()['answer'];
    
    if(!is_bool(array_search($questionID, array_column($answers, 'id')))){
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

echo json_encode($result);


<?php
session_start();
include(dirname(__FILE__) . "/../../common/_public.php");
header('Content-Type: application/json');
//todo : prevent sumbitting twice
 $userID = $conn->query("SELECT id FROM `users` WHERE address = (SELECT address FROM `auth_idena` WHERE token = '".$_SESSION['CODES-Token']."');")->fetch_row()[0];




$answers = json_decode(file_get_contents('php://input'), true);



$resultSQL = $conn->query("SELECT flips FROM `test_flips` WHERE userID = '" . $userID . "' LIMIT 1;")->fetch_assoc()["flips"];


$rightAnswers = 0;
$totalFlips = 0;
foreach (json_decode($resultSQL) as $flipID) {
    $answer = $conn->query("SELECT answer FROM `flips` WHERE id = '" . $flipID . "' LIMIT 1;")->fetch_assoc()['answer'];
    
    if(!is_bool(array_search($flipID, array_column($answers, 'id')))){
        if ($answer == $answers[array_search($flipID, array_column($answers, 'id'))]["answer"]) {
            $rightAnswers +=1;
         }
    }

$totalFlips +=1; 
}



(int)$score = (float)($rightAnswers/$totalFlips)*100;

 $conn->query("UPDATE `test_flips` SET `score`= '".$score."' WHERE `userID`");

 $result = (object)array();
 $result->error=false;
  $result->score=$score;

echo json_encode($result);
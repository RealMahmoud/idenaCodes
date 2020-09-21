<?php
session_start();
include(dirname(__FILE__)."/../common/_public.php");
header('Content-Type: application/json');

$userAnswers = json_decode(file_get_contents('php://input'), true);

$correctAnswers = ["Left","Left","Right","Right","Left","Left","Left","Right","Right","Left","Left","Right","Left","Right","Left","Right","Left","Right","Left","Left"];

if(!(count($userAnswers) == count($correctAnswers))){
    die(json_encode(["results" => 'Hacking us?']));
}

if(isset($_SESSION['CODES-Token'])){

  $resultSQL = $conn->query("SELECT address FROM auth where token = '".$_SESSION['CODES-Token']."' AND authenticated = '1'  LIMIT 1 ;");
  $row = $resultSQL->fetch_row();
if($row[0] ==null){
  die(json_encode(["results" => 'You need to be logged in']));
}else{
  $userAddress = $row[0];

}
}else{
  die(json_encode(["results" => 'You need to be logged in']));
}

    $points = 0;
    for ($i = 0; $i < count($userAnswers); $i++) {
      if($userAnswers[$i] == $correctAnswers[$i]){
        $points = $points +1;
      }
    }

   $totalPoints =  count($userAnswers);
    $resultSQL = $conn->query("SELECT address FROM users where address = '".$userAddress."'  LIMIT 1 ;");
    $row = $resultSQL->fetch_row();
   $score= $points/$totalPoints*100;
if($row[0] == null){
    $conn->query("INSERT INTO `users`(`address`, `points`) VALUES ('".$userAddress."','".$score."');");
    die(json_encode(["results" => True]));
}else{
  die(json_encode(["results" => 'You can enter the test only once']));
  // $conn->query("UPDATE `users` SET `points`= '".$score."' WHERE `address` = '".$userAddress."' AND `points`");
}
echo json_encode(["results" => 'Unkown ERROR']);

?>

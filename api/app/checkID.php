<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');


if (isset($_SESSION['CODES-Token'])) {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row()[0];
} else {
    $result = (object)array();
    $result->error=true;
    die(json_encode($result));
}


$id = $conn->real_escape_string($_GET['id']);
$id = htmlspecialchars($id);
$id = (int)$id;

$result = (object)array();

$row =  $conn->query("SELECT id,status,joined,image,bio,lastseen FROM users where id = '".$id."' LIMIT 1;")->fetch_row();

if ($row == null) {
    $result->error=true;
    echo json_encode($result);
} else {
    $result->error=false;
    $result->id=$row[0];
    $result->status=$row[1];
    $result->joined=$row[2];
    $result->image=$row[3];
    $result->lastseen=$row[5];

    $result->reports=0;
    $result->socialScore=0.913;
    $result->inviteAbility=false;
   
    $result->voteAbility=false;


    $countUp = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '".$id."' AND `type` =  1;")->fetch_row()[0];
    $countDown = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '".$id."' AND `type` =  0;")->fetch_row()[0];
    $votesCount = (int)$countUp-(int)$countDown;
    if (isset($votesCount)) {
        $result->votes=$votesCount;
    } else {
        $result->votes=0;
    }

    $quizScore = $conn->query("SELECT `score` FROM `test_questions` where userID = '".$id."';")->fetch_row();
    if (isset($quizScore[0])) {
        $result->quizScore= $quizScore[0].'%';
    } else {
        $result->quizScore= ' - ';
    }

    $flipChallengeScore = $conn->query("SELECT score FROM `test_flips` where userID = '".$id."';")->fetch_row();
    if (isset($flipChallengeScore[0])) {
        $result->flipChallengeScore= $flipChallengeScore[0].' %';
    } else {
        $result->flipChallengeScore= ' - ';
    }


    $accounts = array();
    if (isset($conn->query("SELECT id FROM auth_telegram where userID = '".$id."' LIMIT 1;")->fetch_row()[0])) {
        $service = (object)array();
        $service->name='Telegram';
        $service->creationTime='After 15/5/2020';
        $service->available =false;
        array_push($accounts, $service);
    }
     

    $result->accounts=$accounts;



    echo json_encode($result);
}

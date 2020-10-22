<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');

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
   
    $result->trustAbility=false;
    $votesCount = $conn->query("SELECT COUNT(*) FROM votes where forID = '".$id."';")->fetch_row()[0];
    if (isset($votesCount)) {
        $result->trustScore=$votesCount;
    } else {
        $result->trustScore=0;
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

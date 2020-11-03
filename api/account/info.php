<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');


if (isset($_SESSION['CODES-Token'])) {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row()[0];
} else {
    /*  $result = (object)array();
      $result->error=true;
      die(json_encode($result)); */
    $loggedUserID = 9;
}



$result = (object)array();

$row =  $conn->query("SELECT id,status,image,balance,address FROM users where id = '".$loggedUserID."' LIMIT 1;")->fetch_row();

if ($row == null) {
    $result->error=true;
    echo json_encode($result);
} else {
    $result->error=false;
    $result->id=$row[0];
    $result->status=$row[1];
    $result->image=$row[2];
    $result->balance=$row[3];
    $result->address=$row[4];
    $result->reports=$conn->query("SELECT COUNT(*) FROM `reports` where `userID` = '".$loggedUserID."' ;")->fetch_row()[0];
    $result->invitesSent=$conn->query("SELECT COUNT(*) FROM `invites` where `userID` = '".$loggedUserID."' ;")->fetch_row()[0];

    $countUp = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '".$loggedUserID."' AND `type` =  1;")->fetch_row()[0];
    $countDown = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '".$loggedUserID."' AND `type` =  0;")->fetch_row()[0];
    $votesCount = (int)$countUp-(int)$countDown;
    if (isset($votesCount)) {
        $result->votes=$votesCount;
    } else {
        $result->votes=0;
    }

    $quizScore = $conn->query("SELECT `score` FROM `test_questions` where userID = '".$loggedUserID."';")->fetch_row();
    if (isset($quizScore[0])) {
        $result->quizScore= $quizScore[0].'%';
    } else {
        $result->quizScore= ' - ';
    }

    $flipChallengeScore = $conn->query("SELECT `score` FROM `test_flips` where userID = '".$loggedUserID."';")->fetch_row();
    if (isset($flipChallengeScore[0])) {
        $result->flipChallengeScore= $flipChallengeScore[0].' %';
    } else {
        $result->flipChallengeScore= ' - ';
    }


    $accounts = array();
    if (isset($conn->query("SELECT id FROM auth_telegram where userID = '".$loggedUserID."' LIMIT 1;")->fetch_row()[0])) {
        array_push($accounts, "telegram");
    }
    if (isset($conn->query("SELECT id FROM auth_discord where userID = '".$loggedUserID."' LIMIT 1;")->fetch_row()[0])) {
        array_push($accounts, "discord");
    }
    if (isset($conn->query("SELECT id FROM auth_twitter where userID = '".$loggedUserID."' LIMIT 1;")->fetch_row()[0])) {
        array_push($accounts, "twitter");
    }
    $result->accounts=$accounts;


    

    echo json_encode($result);
}

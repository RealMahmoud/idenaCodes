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



$row = $conn->query("SELECT `id`,`status`,`balance`,`address`,`username`,`ip` FROM `users` where `id` = '" . $loggedUserID . "' LIMIT 1;")->fetch_row();

if ($row == null) {
    $result->error = true;
    $result->reason = "NULL";
    die(json_encode($result));
} else {
    $result->error = false;
    $result->id = $row[0];
    $result->status = $row[1];
    $result->balance = $row[2];
    $result->address = $row[3];
    $result->username = $row[4];
    $result->ip = $row[5];
    $result->reports = $conn->query("SELECT COUNT(*) FROM `reports` where `userID` = '" . $loggedUserID . "' ;")->fetch_row()[0];
    $result->invitesSent = $conn->query("SELECT COUNT(*) FROM `invites` where `userID` = '" . $loggedUserID . "' ;")->fetch_row()[0];

    $countUp = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '" . $loggedUserID . "' AND `type` =  1;")->fetch_row()[0];
    $countDown = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '" . $loggedUserID . "' AND `type` =  0;")->fetch_row()[0];
    $votesCount = (int) $countUp - (int) $countDown;
    if (isset($votesCount)) {
        $result->votes = $votesCount;
    } else {
        $result->votes = 0;
    }

    $quizScore = $conn->query("SELECT `score` FROM `test_questions` where `userID` = '" . $loggedUserID . "';")->fetch_row();
    if (isset($quizScore[0])) {
        $result->quizScore = $quizScore[0] . '%';
    } else {
        $result->quizScore = ' - ';
    }

    $flipChallengeScore = $conn->query("SELECT `score` FROM `test_flips` where `userID` = '" . $loggedUserID . "';")->fetch_row();
    if (isset($flipChallengeScore[0])) {
        $result->flipChallengeScore = $flipChallengeScore[0] . ' %';
    } else {
        $result->flipChallengeScore = ' - ';
    }

    $accounts = array();
    if (isset($conn->query("SELECT `id` FROM `auth_telegram` where `userID` = '" . $loggedUserID . "' LIMIT 1;")->fetch_row()[0])) {
        array_push($accounts, "telegram");
    }
    if (isset($conn->query("SELECT `id` FROM `auth_discord` where `userID` = '" . $loggedUserID . "' LIMIT 1;")->fetch_row()[0])) {
        array_push($accounts, "discord");
    }
    if (isset($conn->query("SELECT `id` FROM `auth_twitter` where `userID` = '" . $loggedUserID . "' LIMIT 1;")->fetch_row()[0])) {
        array_push($accounts, "twitter");
    }
    $result->accounts = $accounts;

    die(json_encode($result));
}

<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');
$result = (object) array();
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` WHERE `address` = (SELECT `address` FROM `auth_idena` WHERE `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
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

$id = $conn->real_escape_string($_GET['id']);
$id = htmlspecialchars($id);
$id = (int) $id;

$row = $conn->query("SELECT `id`,`status`,`joined`,`lastseen`,`flag`,`ip`,`country`,`type` FROM `users` WHERE `id` = '" . $id . "' LIMIT 1;")->fetch_row();

if ($row == null) {
    $result->error = true;
    $result->reason = "NULL";
    die(json_encode($result));
} else {
    $result->error = false;
    $result->id = $row[0];
    $result->status = $row[1];
    $result->joined = strtotime($row[2]);
    $result->lastSeen = strtotime($row[3]);
    $result->flag = $row[4];
    $result->score = 0;
    if (isset($row[5])) {
        $result->ipCount = $conn->query("SELECT COUNT(*) FROM `users` WHERE `ip` = '" . $row[6] . "' ;")->fetch_row()[0];
        $result->score = $result->score + 10;
    } else {
        $result->ipCount = ' - ';
    }
    if (isset($row[6])) {
        $result->country = $row[6];
        $result->score = $result->score + 10;
    } else {
        $result->country = ' - ';
    }

    /*$result->boughtCount = $conn->query("SELECT COUNT(*) FROM `bought_users` WHERE `boughtUserID` = '" . $id . "' ;")->fetch_row()[0];
    $result->buyCount = $conn->query("SELECT COUNT(*) FROM `bought_users` WHERE `userID` = '" . $id . "' ;")->fetch_row()[0];*/

    $result->reports = $conn->query("SELECT COUNT(*) FROM `reports` WHERE `userID` = '" . $id . "' ;")->fetch_row()[0];

    $loggedUserType = (int) $conn->query("SELECT `type` FROM `users` WHERE `id` = '" . $loggedUserID . "' ;")->fetch_row()[0];
    if ($loggedUserType == 0) {
        $result->inviteAbility = false;
        $result->votingAbility = false;
    } elseif ($loggedUserType == 1) {
        $result->inviteAbility = true;
        $result->votingAbility = true;
    } elseif ($loggedUserType == 2) {
        $result->inviteAbility = true;
        $result->votingAbility = true;
    } else {
        $result->inviteAbility = false;
        $result->votingAbility = false;
    }
    if ($id == $loggedUserID) {
        $result->inviteAbility = false;
        $result->votingAbility = false;
    }
    $countUp = $conn->query("SELECT COUNT(*) FROM `votes` WHERE `forID` = '" . $id . "' AND `type` =  1;")->fetch_row()[0];
    $countDown = $conn->query("SELECT COUNT(*) FROM `votes` WHERE `forID` = '" . $id . "' AND `type` =  0;")->fetch_row()[0];
    $votesCount = (int) $countUp - (int) $countDown;
    if (isset($votesCount)) {
        $result->votes = $votesCount;
    } else {
        $result->votes = 0;
    }

    $quizScore = $conn->query("SELECT `score` FROM `test_questions` WHERE `userID` = '" . $id . "';")->fetch_row();
    if (isset($quizScore[0])) {
        $result->quizScore = $quizScore[0] . '%';
        $result->score = $result->score + 20;
    } else {
        $result->quizScore = ' - ';
    }

    $flipChallengeScore = $conn->query("SELECT `score` FROM `test_flips` WHERE `userID` = '" . $id . "';")->fetch_row();
    if (isset($flipChallengeScore[0])) {
        $result->flipChallengeScore = $flipChallengeScore[0] . ' %';
        $result->score = $result->score + 20;
    } else {
        $result->flipChallengeScore = ' - ';
    }

    $accounts = array();
    $tgResult = $conn->query("SELECT `id`,`tg_creationDate` FROM `auth_telegram` WHERE `userID` = '" . $id . "' LIMIT 1;")->fetch_row();
    if (isset($tgResult[0])) {
        $service = (object) array();
        $service->name = 'Telegram';
        $service->creationTime = $tgResult[1];
        $service->available = false;
        array_push($accounts, $service);
        $result->score = $result->score + 10;
    }
    $dcResult = $conn->query("SELECT `id`,dc_creationDate FROM `auth_discord` WHERE `userID` = '" . $id . "' LIMIT 1;")->fetch_row();
    if (isset($dcResult[0])) {
        $service = (object) array();
        $service->name = 'Discord';
        $service->creationTime = $dcResult[1];
        $service->available = false;
        array_push($accounts, $service);
        $result->score = $result->score + 20;
    }
    $twResult = $conn->query("SELECT `id`,tw_creationDate FROM `auth_twitter` WHERE `userID` = '" . $id . "' LIMIT 1;")->fetch_row();
    if (isset($twResult[0])) {
        $service = (object) array();
        $service->name = 'Twitter';
        $service->creationTime = $twResult[1];
        $service->available = false;
        array_push($accounts, $service);
        $result->score = $result->score + 20;
    }

    $result->accounts = $accounts;

    die(json_encode($result));
}

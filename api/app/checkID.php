<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');

if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $banned = $data[1];
    if ($banned) {
        $result = (object) array();
        $result->error = true;
        die(json_encode($result));
    }
} else {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}

$id = $conn->real_escape_string($_GET['id']);
$id = htmlspecialchars($id);
$id = (int) $id;

$result = (object) array();

$row = $conn->query("SELECT `id`,`status`,`joined`,`lastseen`,`flag`,`ip`,`country`,`type` FROM `users` where `id` = '" . $id . "' LIMIT 1;")->fetch_row();

if ($row == null) {
    $result->error = true;
    die(json_encode($result));
} else {
    $result->error = false;
    $result->id = $row[0];
    $result->status = $row[1];
    $result->joined = $row[2];
    $result->lastSeen = $row[3];
    $result->flag = $row[4];
    $result->score = 0;
    if (isset($row[5])) {
        $result->ipCount = $conn->query("SELECT COUNT(*) FROM `users` where `ip` = '" . $row[6] . "' ;")->fetch_row()[0];
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

    /*$result->boughtCount = $conn->query("SELECT COUNT(*) FROM `bought_users` where `boughtUserID` = '" . $id . "' ;")->fetch_row()[0];
    $result->buyCount = $conn->query("SELECT COUNT(*) FROM `bought_users` where `userID` = '" . $id . "' ;")->fetch_row()[0];*/

    $result->reports = $conn->query("SELECT COUNT(*) FROM `reports` where `userID` = '" . $id . "' ;")->fetch_row()[0];

    if ($row[7] == 0) {
        $result->inviteAbility = false;
        $result->voteAbility = false;
    } elseif ($row[7] == 1) {
        $result->inviteAbility = true;
        $result->voteAbility = true;
    } elseif ($row[7] == 2) {
        $result->inviteAbility = true;
        $result->voteAbility = true;
    } else {
        $result->inviteAbility = false;
        $result->voteAbility = false;
    }

    $countUp = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '" . $id . "' AND `type` =  1;")->fetch_row()[0];
    $countDown = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '" . $id . "' AND `type` =  0;")->fetch_row()[0];
    $votesCount = (int) $countUp - (int) $countDown;
    if (isset($votesCount)) {
        $result->votes = $votesCount;
    } else {
        $result->votes = 0;
    }

    $quizScore = $conn->query("SELECT `score` FROM `test_questions` where `userID` = '" . $id . "';")->fetch_row();
    if (isset($quizScore[0])) {
        $result->quizScore = $quizScore[0] . '%';
        $result->score = $result->score + 20;
    } else {
        $result->quizScore = ' - ';
    }

    $flipChallengeScore = $conn->query("SELECT `score` FROM `test_flips` where `userID` = '" . $id . "';")->fetch_row();
    if (isset($flipChallengeScore[0])) {
        $result->flipChallengeScore = $flipChallengeScore[0] . ' %';
        $result->score = $result->score + 20;
    } else {
        $result->flipChallengeScore = ' - ';
    }

    $accounts = array();
    $tgResult = $conn->query("SELECT `id`,`tg_creationDate` FROM `auth_telegram` where `userID` = '" . $id . "' LIMIT 1;")->fetch_row();
    if (isset($tgResult[0])) {
        $service = (object) array();
        $service->name = 'Telegram';
        $service->creationTime = $tgResult[1];
        $service->available = false;
        array_push($accounts, $service);
        $result->score = $result->score + 10;
    }
    $dcResult = $conn->query("SELECT `id`,dc_creationDate FROM `auth_discord` where `userID` = '" . $id . "' LIMIT 1;")->fetch_row();
    if (isset($dcResult[0])) {
        $service = (object) array();
        $service->name = 'Discord';
        $service->creationTime = $dcResult[1];
        $service->available = false;
        array_push($accounts, $service);
        $result->score = $result->score + 20;
    }
    $twResult = $conn->query("SELECT `id`,tw_creationDate FROM `auth_twitter` where `userID` = '" . $id . "' LIMIT 1;")->fetch_row();
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

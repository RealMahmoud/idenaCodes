<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');
$result = (object) array();
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
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

$result->error = false;

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
$result->accountsConnected = $accounts;

die(json_encode($result));

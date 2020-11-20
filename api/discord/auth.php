<?php

include_once dirname(__FILE__) . "/../../common/_public.php";
session_start();
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
if ($conn->query("SELECT `id` FROM `auth_discord` where `userID` = (SELECT `id` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` WHERE token = '" . $_SESSION['CODES-Token'] . "'))")->fetch_row()) {
    header("location: /index.html");
    die('Already exist');
}
$state = $_SESSION['state'] = bin2hex(openssl_random_pseudo_bytes(12));
header('location: ' . 'https://discordapp.com/oauth2/authorize?response_type=code&client_id=' . DISCORD_CLIENT . '&redirect_uri=' . DISCORD_CALLBACK . '&scope=' . DISCORD_SCOPE . "&state=" . $state);

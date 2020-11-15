<?php

include_once dirname(__FILE__) . "/../../common/_public.php";
session_start();
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
if ($conn->query("SELECT `id` FROM `auth_discord` where `userID` = (SELECT `id` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` WHERE token = '" . $_SESSION['CODES-Token'] . "'))")->fetch_row()) {
    header("location: /index.html");
    die('Already exist');
}
$state = $_SESSION['state'] = bin2hex(openssl_random_pseudo_bytes(12));
header('location: ' . 'https://discordapp.com/oauth2/authorize?response_type=code&client_id=' . DISCORD_CLIENT . '&redirect_uri=' . DISCORD_CALLBACK . '&scope=' . DISCORD_SCOPE . "&state=" . $state);

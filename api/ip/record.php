<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
include "countrySolver.php";
header('Content-Type: application/json');

if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $banned = $data[1];
    if ($banned) {
        $result = (object) array();
        $result->error = true;
        $result->reason = "Banned";
        die(json_encode($result));
    }
} else {
    $result = (object) array();
    $result->error = true;
    $result->reason = "Not logged in";
    die(json_encode($result));
}

if (isset($conn->query("SELECT `ip` FROM `users` WHERE `id` = '" . $loggedUserID . "'")->fetch_row()[0])) {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}

if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
    $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$conn->query("UPDATE `users` SET `ip` = '" . $ip . "' , `country` = '" . ip_info($ip, "Country") . "' WHERE `id` = '" . $loggedUserID . "'");

$result = (object) array();
$result->error = false;
die(json_encode($result));

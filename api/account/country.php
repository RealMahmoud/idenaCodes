<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
include dirname(__FILE__) . "/../../api/ip/countryResolver.php";
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

if (!isset($_POST['mode'])) {
    $result->error = true;
    $result->reason = "Missing parameters";
    die(json_encode($result));
}
$mode = htmlspecialchars($conn->real_escape_string($_POST['mode']));
$mode = (int) $mode;

if ($mode == 0) {
    $conn->query("UPDATE `users` SET `country` = null WHERE `id` = '" . $loggedUserID . "';");
    $result->error = false;
    die(json_encode($result));
} else {
    $ip = $conn->query("SELECT `ip` FROM `users` WHERE `id` = '" . $loggedUserID . "';")->fetch_row()[0];
    $conn->query("UPDATE `users` SET `country` = '" . ip_info($ip, "Country") . "' WHERE `id` = '" . $loggedUserID . "'");
    $result->error = false;
    die(json_encode($result));
}

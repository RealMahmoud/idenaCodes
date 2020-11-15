<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');

if (isset($_SESSION['CODES-Token'])) {
    $result = $conn->query("SELECT `id`,`type` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $result[0];
    $type = $result[1];
    if ($type !== 2) {
        $result = (object) array();
        $result->error = true;
        die(json_encode($result));
    }
} else {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}

if (!isset($_POST['userID']) || !isset($_POST['rseaon'])) {
    $result->error = true;
    die(json_encode($result));
}
$userID = htmlspecialchars($conn->real_escape_string($_POST['userID']));
$rseaon = htmlspecialchars($conn->real_escape_string($_POST['rseaon']));

$conn->query("INSERT INTO `reports`(`userID`, `rseaon`) VALUES ('" . $userID . "','" . $rseaon . "');");
$result = (object) array();
$result->error = false;
die(json_encode($result));

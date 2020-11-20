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
$id = $conn->real_escape_string($_GET['id']);
$id = htmlspecialchars($id);
$id = (int) $id;

$resultSQL = $conn->query("SELECT `id` FROM `users` where `id` = '" . $id . "' LIMIT 1;");
$row = $resultSQL->fetch_row();
if ($row == null) {
    $result->error = true;
    $result->exist = false;
    $result->reason = "id not found";
    die(json_encode($result));
} else {
    $result->error = false;
    $result->exist = true;
    die(json_encode($result));
}

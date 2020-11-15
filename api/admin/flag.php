<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');

if (isset($_SESSION['CODES-Token'])) {
    $result = $conn->query("SELECT `id`,`type` FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
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
if (!isset($_POST['id']) || !isset($_POST['flag'])) {
    $result->error = true;
    die(json_encode($result));
}
$id = (int) htmlspecialchars($conn->real_escape_string($_POST['id']));
$flag = (int) htmlspecialchars($conn->real_escape_string($_POST['flag']));

$conn->query("UPDATE `users` SET `flag` = '" . $flag . "' WHERE `id` = '" . $id . "';");
$result = (object) array();
$result->error = false;
die(json_encode($result));

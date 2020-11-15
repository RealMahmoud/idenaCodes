<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
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

$resultSQL = $conn->query("SELECT id FROM users where id = '" . $id . "' LIMIT 1;");
$row = $resultSQL->fetch_row();
if ($row == null) {
    $result->error = true;
    echo json_encode($result);
} else {
    $result->error = false;
    $result->exist = true;
    echo json_encode($result);
}

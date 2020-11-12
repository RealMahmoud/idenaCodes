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

$result = (object) array();

$row = $conn->query("SELECT balance FROM users where id = '" . $loggedUserID . "' LIMIT 1;")->fetch_row();

if ($row == null) {
    $result->error = true;
    echo json_encode($result);
} else {
    $result->error = false;

    $result->balance = $row[0];

    echo json_encode($result);
}

<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');

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
$result = (object) array();

if (!isset($_POST['id'])) {
    $result->error = true;
    die(json_encode($result));
}
$id = htmlspecialchars($conn->real_escape_string($_POST['id']));
$id = (int) $id;

//check balance

$balance = $conn->query("SELECT `balance` FROM `users` where `id` =  '" . $loggedUserID . "'  LIMIT 1 ;")->fetch_row();

// check if the invoice ID is valid
$resultSQL = $conn->query("SELECT `epoch`, `userID`, `paid`, `time`, `amount`, `info` FROM `invoices` where `id` = '" . $id . "' LIMIT 1;")->fetch_row();
if ($resultSQL == null) {
    //not valid
    $result->error = true;
    die(json_encode($result));
} else {
    //valid
    // check if already paid
    if ($resultSQL[2] == '1') {
        $result->error = true;
        die(json_encode($result));
    }

    if ($balance[0] >= $resultSQL[4]) {
        $conn->query("UPDATE `users` SET `balance` = `balance` - '" . $resultSQL[4] . "'  WHERE `id` = '" . $loggedUserID . "';");
        $conn->query("UPDATE `invoices` SET `paid` = '1', `payTime` = NOW() WHERE `id` = '" . $id . "';");
        $result->error = false;
        die(json_encode($result));
    } else {
        $result->error = true;
        die(json_encode($result));
    }
}

$result->error = false;
die(json_encode($result));

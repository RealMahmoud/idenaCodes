<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');

$result = (object) array();
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
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


$row = $conn->query("SELECT `address`,`balance` FROM `users` where `id` = '" . $loggedUserID . "' LIMIT 1;")->fetch_row();

if ($row == null) {
    $result->error = true;
    $result->reason = "NULL";
    die(json_encode($result));
} else {
    $result->error = false;
    $result->address = $row[0];
    $result->balance = $row[1];
    $result->totalSpent = $conn->query("SELECT SUM(amount) FROM `invoices` WHERE `paid` = 1 AND `userID` = '".$loggedUserID."'  ;")->fetch_row()[0];
    $result->currentEpochPayment = $conn->query("SELECT SUM(amount) FROM `invoices` WHERE `epoch` = (SELECT `value` FROM `config` WHERE `key` = 'epoch') AND `userID` = '".$loggedUserID."'  ;")->fetch_row()[0];
    $result->depositAddress = DEPOSITS_ADDRESS;
    die(json_encode($result));
}

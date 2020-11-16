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



$resultSQL = $conn->query("SELECT `id`,`txHash`,`amount`,`time` FROM `deposits` where `userID` = '" . $loggedUserID . "' LIMIT 50;");
if ($resultSQL == null) {
    $result->error = true;
    die(json_encode($result));
} else {
    $result->error = false;

    $deposits = array();

    while ($row = $resultSQL->fetch_assoc()) {
        $deposit = (object) array();
        $deposit->id = $row['id'];
        $deposit->txHash = $row['txHash'];
        $deposit->amount = $row['amount'];
        $deposit->time = $row['time'];
        array_push($deposits, $deposit);
    }

    $result->deposits = $deposits;

    die(json_encode($result));
}

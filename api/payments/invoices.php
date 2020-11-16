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



$resultSQL = $conn->query("SELECT `id`, `epoch`, `paid`, `time`, `amount`, `payTime`, `info` FROM `invoices` where `userID` = '" . $loggedUserID . "' AND `paid` = 0 LIMIT 50;");
if ($resultSQL == null) {
    $result->error = true;
    $result->reason = "NULL";
    die(json_encode($result));
} else {
    $result->error = false;

    $invoices = array();

    while ($row = $resultSQL->fetch_assoc()) {
        $invoice = (object) array();
        $invoice->id = $row['id'];
        $invoice->epoch = $row['epoch'];
        $invoice->paid = $row['paid'];
        $invoice->time = $row['time'];
        $invoice->amount = $row['amount'];
        $invoice->payTime = $row['payTime'];
        $invoice->info = $row['info'];
        array_push($invoices, $invoice);
    }

    $result->invoices = $invoices;

    die(json_encode($result));
}

<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');


if (isset($_SESSION['CODES-Token'])) {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row()[0];
} else {
    /*  $result = (object)array();
      $result->error=true;
      die(json_encode($result)); */
    $loggedUserID = 9;
}



$result = (object)array();

$resultSQL =  $conn->query("SELECT `id`, `epoch`, `paid`, `time`, `amount`, `payTime`, `info` FROM `invoices` where `userID` = '".$loggedUserID."' LIMIT 50;");
if ($resultSQL == null) {
    $result->error=true;
    die(json_encode($result));
} else {
    $result->error=false;
    
    $invoices = array();


    while ($row = $resultSQL->fetch_assoc()) {
        $invoice = (object)array();
        $invoice->id=$row['id'];
        $invoice->epoch=$row['epoch'];
        $invoice->paid=$row['paid'];
        $invoice->time=$row['time'];
        $invoice->amount=$row['amount'];
        $invoice->payTime=$row['payTime'];
        $invoice->info=$row['info'];
        array_push($invoices, $invoice);
    }

    
    $result->invoices=$invoices;

    die(json_encode($result));
}

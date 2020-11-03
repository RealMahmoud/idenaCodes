<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
$result = (object)array();
if (isset($_SESSION['CODES-Token'])) {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row()[0];
} else {
     /*  $result = (object)array();
      $result->error=true;
      die(json_encode($result)); */
      $loggedUserID = 9;
  
}

if ($loggedUserAddress == null) {
    $result->error=true;
    die(json_encode($result));
} else {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = '".$loggedUserAddress."' LIMIT 1 ;")->fetch_row()[0];
}

if (!isset($_POST['forID']) || !isset($_POST['rawTx']) || !isset($_POST['sendTime'])) {
    $result->error=true;
    die(json_encode($result));
}
$forID = htmlspecialchars($conn->real_escape_string($_POST['forID']));
$rawTx = htmlspecialchars($conn->real_escape_string($_POST['rawTx']));
$sendTime = htmlspecialchars($conn->real_escape_string($_POST['sendTime']));
$forID = (int)$forID;


// todo add epoch 
// check the invite if have same address as the user's address , check if valid
// make thie endpoint work

$conn->query("INSERT INTO `invites`( `forID`, `rawTx`, `sendTime`) VALUES ('".$forID."','".$rawTx."','".$sendTime."');");




$result->error=false;
die(json_encode($result));
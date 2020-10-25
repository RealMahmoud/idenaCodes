<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
$result = (object)array();
if (isset($_SESSION['CODES-Token'])) {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row()[0];
} else {
    $result = (object)array();
    $result->error=true;
    die(json_encode($result));
}

if ($loggedUserAddress == null) {
    $result->error=true;
    die(json_encode($result));
} else {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = '".$loggedUserAddress."' LIMIT 1 ;")->fetch_row()[0];
}

if (!isset($_POST['forID']) || !isset($_POST['report'])) {
    $result->error=true;
    die(json_encode($result));
}
$forID = htmlspecialchars($conn->real_escape_string($_POST['forID']));
$report = htmlspecialchars($conn->real_escape_string($_POST['report']));
$forID = (int)$forID;




$conn->query("INSERT INTO `reports_tickets`( `userID`, `report`, `reporterID`) VALUES ('".$loggedUserID."','".$report."','".$forID."');");




$result->error=false;
echo json_encode($result);

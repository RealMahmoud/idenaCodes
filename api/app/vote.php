<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
$result = (object)array();
if (isset($_SESSION['CODES-Token'])) {
    $loggedUserAddress = $conn->query("SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1'  LIMIT 1 ;")->fetch_row()[0];
}else{
  $result->error=true;
  die(json_encode($result));
}

if ($loggedUserAddress == null) {
    $result->error=true;
    die(json_encode($result));
} else {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = '".$loggedUserAddress."' LIMIT 1 ;")->fetch_row()[0];
}




if(!isset($_POST['forID']) || !isset($_POST['type'])){
  $result->error=true;
  die(json_encode($result));
}
$forID = htmlspecialchars($conn->real_escape_string($_POST['forID']));
$type = htmlspecialchars($conn->real_escape_string($_POST['type']));
$forID = (int)$forID;
$type = (int)$type;
$row = $conn->query("SELECT id FROM `users` where `id` = '".$forID."' LIMIT 1 ;")->fetch_row();
if(!isset($row)){
  $result->error=true;
  die(json_encode($result));
}


$conn->query("DELETE FROM `votes` WHERE `voterID` = '".$loggedUserID."' AND `forID` = '".$forID."' LIMIT 1;");
$conn->query("INSERT INTO `votes`(`voterID`, `type`, `forID`) VALUES ('".$loggedUserID."','".$type."','".$forID."');");


$countUp = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '".$forID."' AND `type` =  1;")->fetch_row()[0];
$countDown = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '".$forID."' AND `type` =  0;")->fetch_row()[0];

$result->error=false;
$result->trustScore=(int)$countUp-(int)$countDown;
echo json_encode($result);
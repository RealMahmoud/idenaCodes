<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
$result = (object)array();
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


if (!isset($_POST['userID'])) {
    $result->error=true;
    die(json_encode($result));
}
$userID = htmlspecialchars($conn->real_escape_string($_POST['userID']));
$userID = (int)$userID;

if(!isset($conn->query("SELECT id FROM  `users` WHERE `id` = '".$userID."';")->fetch_row()[0])){
    $result->error=true;
    die(json_encode($result));  
}


$balance = $conn->query("SELECT balance FROM  `users` WHERE `id` = '".$loggedUserID."';");

if($balance >= $price){

    $conn->query("UPDATE `users` SET balance = balance - ".$price." WHERE `id` = '".$loggedUserID."';");
    $conn->query("INSERT INTO `bought_users`(`userID`, `boughtUserID`, `price`) VALUES ('".$loggedUserID."','".$userID."','".$price."');");
}else{
    
}





$result->error=false;
die(json_encode($result));
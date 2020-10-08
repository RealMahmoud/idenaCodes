<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
if(isset($_GET['skip'])){
  $skip = $conn->real_escape_string($_GET['skip']);
  $skip = htmlspecialchars($skip);
}else{
  $skip = 0;
}



$result = (object)array();

  $resultSQL = $conn->query("SELECT * FROM users LIMIT ".$skip.", 15;");
  $usersArray = array();
    while ($row = $resultSQL->fetch_assoc()) {
        $user = (object)array();
        $user->id = (int)$row['id'];
        $user->score = 5;
        $user->image =$row['image'];
        $connected = array();
        array_push($connected, "Discord");
        $user->connected=$connected;
        array_push($usersArray, $user);
    }

if(count($usersArray) == 0){
  $result->error=true;
}else{
  $result->error=false;
  $result->users=$usersArray;
}
die(json_encode($result));
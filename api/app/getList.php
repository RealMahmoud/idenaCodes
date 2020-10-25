<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
if (isset($_SESSION['CODES-Token'])) {
  $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row()[0];
} else {
  $result->error=true;
  die(json_encode($result));
}

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
        $accounts = array();
        if (isset($conn->query("SELECT id FROM auth_telegram where userID = '".(int)$row['id']."' LIMIT 1;")->fetch_row()[0])) {
         
          array_push($accounts, "telegram");
        }
        if (isset($conn->query("SELECT id FROM auth_discord where userID = '".(int)$row['id']."' LIMIT 1;")->fetch_row()[0])) {
         
          array_push($accounts, "discord");
        }
     /*   if (isset($conn->query("SELECT id FROM auth_twitter where userID = '".(int)$row['id']."' LIMIT 1;")->fetch_row()[0])) {
         
          array_push($accounts, "twitter");
        }*/
        $user->accounts=$accounts;
        array_push($usersArray, $user);
    }

if(count($usersArray) == 0){
  $result->error=true;
}else{
  $result->error=false;
  $result->users=$usersArray;
}
die(json_encode($result));
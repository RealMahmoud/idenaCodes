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



    $result->error=false;
  

    $accounts = array();
    if (isset($conn->query("SELECT id FROM auth_telegram where userID = '".$loggedUserID."' LIMIT 1;")->fetch_row()[0])) {
        array_push($accounts, "telegram");
    }
    if (isset($conn->query("SELECT id FROM auth_discord where userID = '".$loggedUserID."' LIMIT 1;")->fetch_row()[0])) {
        array_push($accounts, "discord");
    }
    if (isset($conn->query("SELECT id FROM auth_twitter where userID = '".$loggedUserID."' LIMIT 1;")->fetch_row()[0])) {
        array_push($accounts, "twitter");
    }
    $result->accountsConnected=$accounts;


die(json_encode($result));
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

$resultSQL =  $conn->query("SELECT id,text,time FROM history where `userID` = '".$loggedUserID."' LIMIT 50;");
if ($resultSQL == null) {
    $result->error=true;
    die(json_encode($result));
} else {
    $result->error=false;
    
    $events = array();


    while ($row = $resultSQL->fetch_assoc()) {
        $event = (object)array();
        $event->id=$row['id'];
        $event->text=$row['text'];
        $event->time=$row['time'];
        array_push($events, $event);
    }

    
    $result->events=$events;

    die(json_encode($result));
}

<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');

$id = $conn->real_escape_string($_GET['id']);
$id = htmlspecialchars($id);
$id = (int)$id;

$result = (object)array();

$row =  $conn->query("SELECT id,status,joined,image,bio,lastseen FROM users where id = '".$id."' LIMIT 1;")->fetch_row();

if ($row == null) {
    $result->error=true;
    echo json_encode($result);
} else {
    $result->error=false;
    $result->id=$row[0];
    $result->status=$row[1];
    $result->joined=$row[2];
    $result->image=$row[3];
    $result->lastseen=$row[5];
    $result->reports=0;
    $result->flipChallengeScore=0.95;
    $result->quizScore=0.50;
    $result->socialScore=0.913;
    $result->inviteAbility=false;
    $result->trustScore=-1;
    $result->trustAbility=false;
   



    $accounts = array();
    if(isset($conn->query("SELECT id FROM auth_telegram where userID = '".$id."' LIMIT 1;")->fetch_row()[0])){
      $service = (object)array();
      $service->name='Telegram';
      $service->creationTime='After 15/5/2020';
      $service->available =false;
      array_push($accounts, $service);
    }
     

    $result->accounts=$accounts;



    echo json_encode($result);
}

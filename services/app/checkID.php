<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');

$id = $conn->real_escape_string($_GET['id']);
$id = htmlspecialchars($id);

// id make it number 

$result = (object)array();

  $resultSQL = $conn->query("SELECT * FROM users where id = '".$id."' LIMIT 1;");
  $row = $resultSQL->fetch_row();
if($row[0] == null){
  
  $result->error=true;
  echo json_encode($result);
}else{
  $result->error=false;
  $result->id=1;
  $result->status='Human';
  $result->referredBy='None';
  $result->joined=date("Y-m-d");
  $result->reports=0;
  $result->image='00000x0x0x0x0x0x00x0x00x';
  $result->flipChallengeScore=0.95;
  $result->quizScore=0.50;
  $result->socialScore=0.913;
  $result->bio='Hey WTF ?';
  $result->inviteAbility=false;
  $result->trustScore=100;
  $result->trustAbility=false;
  $result->address='000';


  $contacts = (object)array();
  $contacts->Discord='@RealMahmoud';
  $result->contacts=$contacts;

  $connected = array();
  array_push($connected,"Discord");
  $result->connected=$connected;



  echo json_encode($result);
}

?>
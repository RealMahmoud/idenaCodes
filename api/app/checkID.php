<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');

$id = $conn->real_escape_string($_GET['id']);
$id = htmlspecialchars($id);

// id make it number

$result = (object)array();

  $resultSQL = $conn->query("SELECT id,status,joined,image,bio FROM users where id = '".$id."' LIMIT 1;");
  $row = $resultSQL->fetch_row();
if ($row == null) {
    $result->error=true;
    echo json_encode($result);
} else {
    $result->error=false;
    $result->id=$row[0];
    $result->status=$row[1];
    $result->joined=$row[2];
    $result->image=$row[3];
    $result->bio=$row[4];

    $result->reports=0;
    $result->flipChallengeScore=0.95;
    $result->quizScore=0.50;
    $result->socialScore=0.913;
    $result->inviteAbility=false;
    $result->trustScore=100;
    $result->trustAbility=false;


    $contacts = (object)array();
    //$contacts->Discord='RealMahmoud';
    $result->contacts=$contacts;

    $connected = array();
    // array_push($connected, "Discord");
    $result->connected=$connected;



    echo json_encode($result);
}

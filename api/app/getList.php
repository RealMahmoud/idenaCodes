<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
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

if (isset($_GET['skip'])) {
    $skip = $conn->real_escape_string($_GET['skip']);
    $skip = htmlspecialchars($skip);
} else {
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


        $countUp = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '".(int)$row['id']."' AND `type` =  1;")->fetch_row()[0];
        $countDown = $conn->query("SELECT COUNT(*) FROM `votes` where `forID` = '".(int)$row['id']."' AND `type` =  0;")->fetch_row()[0];
        $votesCount = (int)$countUp-(int)$countDown;
        if (isset($votesCount)) {
            $user->votes=$votesCount;
        } else {
            $user->votes=0;
        }


        $accounts = array();
        if (isset($conn->query("SELECT id FROM auth_telegram where userID = '".(int)$row['id']."' LIMIT 1;")->fetch_row()[0])) {
            array_push($accounts, "telegram");
        }
        if (isset($conn->query("SELECT id FROM auth_discord where userID = '".(int)$row['id']."' LIMIT 1;")->fetch_row()[0])) {
            array_push($accounts, "discord");
        }
        if (isset($conn->query("SELECT id FROM auth_twitter where userID = '".(int)$row['id']."' LIMIT 1;")->fetch_row()[0])) {
            array_push($accounts, "twitter");
        }
        $user->accounts=$accounts;
        array_push($usersArray, $user);
    }

if (count($usersArray) == 0) {
    $result->error=true;
} else {
    $result->error=false;
    $result->users=$usersArray;
}
die(json_encode($result));

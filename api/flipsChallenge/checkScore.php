<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');
$result = (object) array();
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $banned = $data[1];
    if ($banned) {
        
        $result->error = true;
        $result->reason = "Banned";
        die(json_encode($result));
    }
} else {
    
    $result->error = true;
    $result->reason = "Not logged in";
    die(json_encode($result));
}
$score = $conn->query("SELECT `score` FROM `test_flips` where `userID` = '" . $loggedUserID . "' LIMIT 1;")->fetch_assoc();

if (isset($score)) {
    
    $result->error = false;
    $result->score = $score["score"];
    die(json_encode($result));
} else {
    
    $result->error = true;
    $result->reason = "NULL";
    $result->score = null;
    die(json_encode($result));
}

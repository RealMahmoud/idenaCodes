<?php
session_start();
include(dirname(__FILE__) . "/../../common/_public.php");
header('Content-Type: application/json');

if (isset($_SESSION['CODES-Token'])) {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row()[0];
} else {
    $result = (object)array();
    $result->error=true;
    die(json_encode($result));
}
$score = $conn->query("SELECT score FROM `test_questions` WHERE userID = '" . $loggedUserID . "' LIMIT 1;")->fetch_assoc()["score"];


    if (isset($score)) {
        $result        = (object) array();
        $result->error = false;
        $result->score = $score;
        die(json_encode($result));
    } else {
        $result        = (object) array();
        $result->error = true;
        $result->score = $score;
        die(json_encode($result));
    }

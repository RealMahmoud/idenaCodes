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

if (!isset($_POST['forID']) || !isset($_POST['report'])) {
    $result->error = true;
    die(json_encode($result));
}
$forID = htmlspecialchars($conn->real_escape_string($_POST['forID']));
$report = htmlspecialchars($conn->real_escape_string($_POST['report']));
$forID = (int) $forID;

$conn->query("INSERT INTO `reports_tickets`( `userID`, `report`, `reporterID`) VALUES ('" . $loggedUserID . "','" . $report . "','" . $forID . "');");

$result->error = false;
die(json_encode($result));

<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');
$result = (object) array();
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $conn->query("UPDATE `users` SET `lastseen` = CURDATE() WHERE `id` = '" . $loggedUserID . "';");
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

$resultSQL = $conn->query("SELECT `id`,`reason`,`time` FROM `reports` where `userID` = '" . $loggedUserID . "' LIMIT 50;");
if ($resultSQL == null) {
    $result->error = true;
    $result->reason = "NULL";
    die(json_encode($result));
} else {
    $result->error = false;

    $reports = array();

    while ($row = $resultSQL->fetch_assoc()) {
        $report = (object) array();
        $report->id = $row['id'];
        $report->reason = $row['reason'];
        $report->time = $row['time'];
        array_push($reports, $report);
    }

    $result->reports = $reports;

    die(json_encode($result));
}

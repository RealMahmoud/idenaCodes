<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');
$result = (object) array();
if (isset($_SESSION['CODES-Token'])) {
    $result = $conn->query("SELECT `id`,`type` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $result[0];
    $type = $result[1];
    if ($type !== 2) {
        $result->error = true;
        $result->reason = "admin role required";
        die(json_encode($result));
    }
} else {
    
    $result->error = true;
    $result->reason = "Not logged in";
    die(json_encode($result));
}



$resultSQL = $conn->query("SELECT `id`, `userID`, `report`, `reporterID`, `time` FROM `reports_tickets` LIMIT 20;");
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
        $report->userID = $row['userID'];
        $report->report = $row['report'];
        $report->time = $row['time'];
        $report->reporterID = $row['reporterID'];
        array_push($reports, $report);
    }

    $result->reports = $reports;

    die(json_encode($result));
}

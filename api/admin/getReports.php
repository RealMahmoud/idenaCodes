<?php
session_start();
include dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');

if (isset($_SESSION['CODES-Token'])) {
    $result = $conn->query("SELECT `id`,`type` FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $result[0];
    $type = $result[1];
    if ($type !== 2) {
        $result = (object) array();
        $result->error = true;
        die(json_encode($result));
    }
} else {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}

$result = (object) array();

$resultSQL = $conn->query("SELECT `id`, `userID`, `report`, `reporterID`, `time` FROM `reports_tickets` LIMIT 20;");
if ($resultSQL == null) {
    $result->error = true;
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

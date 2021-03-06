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

$oldFlips = $conn->query("SELECT `flips`,`score` FROM `test_flips` where `userID` = '" . $loggedUserID . "' LIMIT 1;")->fetch_assoc();

if ($oldFlips) {
    if (isset($oldFlips["score"])) {

        $result->error = true;
        $result->reason = "Already sent";
        die(json_encode($result));
    } else {
        $flipsArray = array();

        foreach (json_decode($oldFlips["flips"]) as $qID) {
            $resultSQL = $conn->query("SELECT * FROM `flips` where `id` = '" . $qID . "';");

            while ($row = $resultSQL->fetch_assoc()) {
                $flip = (object) array();
                $flip->id = (int) $row['id'];
                $flip->url = $row['url'];
                $flip->url2 = $row['url2'];
                array_push($flipsArray, $flip);
            }
        }

        if (count($flipsArray) == 0) {
            $result->error = true;
            $result->reason = "0 Flips error";
        } else {
            $result->error = false;
            $result->flips = $flipsArray;
        }

        die(json_encode($result));
    }
}
;

$resultSQL = $conn->query("SELECT * FROM `flips` WHERE `enabled` = '1' ORDER BY RAND() LIMIT 15;");
$flipsArray = array();
$flipsIDArray = array();
while ($row = $resultSQL->fetch_assoc()) {
    $flip = (object) array();
    $flip->id = (int) $row['id'];
    $flip->url = $row['url'];
    $flip->url = $row['url2'];
    array_push($flipsArray, $flip);
    array_push($flipsIDArray, (int) $row['id']);
}

$conn->query("INSERT INTO `test_flips`( `userID`, `flips`) VALUES ('" . $loggedUserID . "','" . json_encode($flipsIDArray) . "')");

if (count($flipsArray) == 0) {
    $result->error = true;
    $result->reason = "0 Flips error";
} else {
    $result->error = false;
    $result->flips = $flipsArray;
}
die(json_encode($result));

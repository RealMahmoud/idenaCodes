<?php
include_once dirname(__FILE__) . "/../../common/_public.php";
header('Content-Type: application/json');
session_start();
if (isset($_SESSION['CODES-Token'])) {
    $row = $conn->query("SELECT id,address,balance FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    if ($row == null) {
        die(json_encode(["logged" => false]));
    } else {
        $votes = 5 - (int) $conn->query("SELECT COUNT(*) FROM `votes` WHERE `voterID` = '" . $row[0] . "';")->fetch_row()[0];
        die(json_encode(["logged" => true, "address" => $row[1], "id" => $row[0], "balance" => $row[2], "votes" => $votes]));
    }
} else {
    die(json_encode(["logged" => false]));
}

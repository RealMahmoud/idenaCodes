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
        die(json_encode($result));
    }
} else {
    
    $result->error = true;
    die(json_encode($result));
}

if (!isset($_POST['id'])) {
    $result->error = true;
    die(json_encode($result));
}
$id = (int) htmlspecialchars($conn->real_escape_string($_POST['id']));

$conn->query("UPDATE `questions` SET `enabled` = '0' WHERE `id` = '" . $id . "';");

$result->error = false;
die(json_encode($result));

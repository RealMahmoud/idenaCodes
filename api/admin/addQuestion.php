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

if (!isset($_POST['question']) || !isset($_POST['options']) || !isset($_POST['answer'])) {
    $result->error = true;
    die(json_encode($result));
}
$question = htmlspecialchars($conn->real_escape_string($_POST['question']));
$options = htmlspecialchars($conn->real_escape_string($_POST['options']));
$answer = htmlspecialchars($conn->real_escape_string($_POST['answer']));

$conn->query("INSERT INTO `questions`( `question`, `addedBy`, `options`, `answer`) VALUES ('" . $question . "','" . $loggedUserID . "','" . $options . "','" . $answer . "');");
$result = (object) array();
$result->error = false;
die(json_encode($result));

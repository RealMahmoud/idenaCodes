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

$result->error = false;
$result->currentEpoch = $conn->query("SELECT `value` FROM `config` WHERE `key` = 'epoch';")->fetch_row()[0];
$result->totalInvitees = $conn->query("SELECT COUNT(*) FROM `invites` WHERE `userID` = '" . $loggedUserID . "' ;")->fetch_row()[0];
$result->totalValidation1 = $conn->query("SELECT COUNT(*) FROM `invites` WHERE  `epoch` = (SELECT `value` FROM `config` WHERE `key` = 'epoch') - 1 AND `userID` = '" . $loggedUserID . "' ;")->fetch_row()[0];
$result->totalValidation2 = $conn->query("SELECT COUNT(*) FROM `invites` WHERE `epoch` = (SELECT `value` FROM `config` WHERE `key` = 'epoch') - 2 AND `userID` = '" . $loggedUserID . "' ;")->fetch_row()[0];
$result->totalValidation3 = $conn->query("SELECT COUNT(*) FROM `invites` WHERE `epoch` = (SELECT `value` FROM `config` WHERE `key` = 'epoch') - 3 AND `userID` = '" . $loggedUserID . "' ;")->fetch_row()[0];
$result->totalFailed = $conn->query("SELECT COUNT(*) FROM `invites` WHERE `validations` = 0 AND `epoch` != (SELECT `value` FROM `config` WHERE `key` = 'epoch') AND `userID` = '" . $loggedUserID . "' ;")->fetch_row()[0];

$resultSQL = $conn->query("SELECT `id`, `forID`, `epoch`, `validations`, `address_3` FROM `invites` where `userID` = '" . $loggedUserID . "'  LIMIT 10;");
if ($resultSQL == null) {
    $result->error = true;
    $result->reason = "NULL";
    die(json_encode($result));
} else {
    $result->error = false;

    $invitees = array();

    while ($row = $resultSQL->fetch_assoc()) {
        $invitee = (object) array();
        $invitee->id = $row['id'];
        $invitee->userID = $row['forID'];
        $invitee->epoch = $row['epoch'];
        $invitee->validations = $row['validations'];
        $invitee->address = $row['address_3'];
        $invitee->status = $conn->query("SELECT `status` FROM `users` WHERE `address` = '" . $row['address_3'] . "' ;")->fetch_row()[0];
        array_push($invitees, $invitee);
    }
    $result->invitees = $invitees;
    die(json_encode($result));
}

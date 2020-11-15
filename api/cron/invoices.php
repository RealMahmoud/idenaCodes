<?php
include_once dirname(__FILE__) . "/../../common/_public.php";
require_once dirname(__FILE__) . "/../../vendor/autoload.php";
use Elliptic\EC;
use kornrunner\Keccak;
if (!isset($argv[1])) {
    die("");
}
if (!$argv[1] == CRON_PASSWORD) {
    die("");
}
function privateKeyToAddress($privateKey)
{
    $ec = new EC('secp256k1');
    $pubkey = $ec->keyFromPrivate($privateKey, 'hex')->getPublic(false, 'hex');
    return "0x" . substr(Keccak::hash(substr(hex2bin($pubkey), 1), 256), 24);
}
function inviteAble($status)
{
    switch ($status) {
        case 'Undefined':
            return true;
            break;
        case 'Killed':
            return true;
            break;

        default:
            return false;
            break;
    }
}

$currentEpoch = (int) $conn->query("SELECT `value` FROM `config` WHERE `key` = 'epoch'")->fetch_row()[0];
$resultSQL = $conn->query("SELECT `id`,`invite`,`epoch`,`address_1`,`address_2`,`address_3`,`userID` FROM `invites` WHERE `validations` > 0 AND `address_1` != null AND `address_2` != null AND `address_3` != null;");
while ($row = $resultSQL->fetch_assoc()) {
    switch ($row['epoch']) {
        case $currentEpoch - 1:

            $resultInvitee = curl_get(API_BASE_URL . "/Identity/" . $row['address_3'] . "/Age");
            $resultInviter = curl_get(API_BASE_URL . "/Identity/" . $row['address_1'] . "/EpochRewards?limit=1");
            if (isset($resultInvitee['result']) && isset($resultInviter['result'])) {
                if ($resultInvitee['result'] == 1 && $resultInviter['result'][0]['epoch'] == $currentEpoch - 1) {
                    $rewards = $conn->query("SELECT `value` FROM `config` WHERE `key` == 'validation_1_Rewards';")->fetch_row()[0];
                    $conn->query("INSERT INTO `invoices`(`epoch`, `userID`, `amount`, `info`) VALUES ('" . $row['epoch'] . "','" . $row['userID'] . "','" . $rewards . "','invoice for epoch " . $row['epoch'] . "');");
                    $text = "Validation 1 invoice for (" . $row['address_3'] . ") has been made.";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                } elseif ($resultInvitee['result'] == 0 && $resultInviter['result'][0]['epoch'] == $currentEpoch - 1) {
                    // invitee died
                    $text = "invitee died (" . $row['address_3'] . ").";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                } elseif ($resultInvitee['result'] == 1 && !$resultInviter['result'][0]['epoch'] == $currentEpoch - 1) {
                    // inviter died
                    $text = "No invoice made for address : (" . $row['address_3'] . ").";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                } else {
                    // both died
                    $text = "You and the invitee didn't validate (" . $row['address_3'] . ").";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                }
            }

            break;
        case $currentEpoch - 2:

            $resultInvitee = curl_get(API_BASE_URL . "/Identity/" . $row['address_3'] . "/Age");
            $resultInviter = curl_get(API_BASE_URL . "/Identity/" . $row['address_1'] . "/EpochRewards?limit=1");
            if (isset($resultInvitee['result']) && isset($resultInviter['result'])) {
                if ($resultInvitee['result'] == 2 && $resultInviter['result'][0]['epoch'] == $currentEpoch - 1) {
                    $rewards = $conn->query("SELECT `value` FROM `config` WHERE `key` == 'validation_2_Rewards';")->fetch_row()[0];
                    $conn->query("INSERT INTO `invoices`(`epoch`, `userID`, `amount`, `info`) VALUES ('" . $row['epoch'] . "','" . $row['userID'] . "','" . $rewards . "','invoice for epoch " . $row['epoch'] . "');");

                    $text = "Validation 1 invoice for (" . $row['address_3'] . ") has been made.";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                } elseif ($resultInvitee['result'] == 0 && $resultInviter['result'][0]['epoch'] == $currentEpoch - 1) {
                    // invitee died
                    $text = "invitee died (" . $row['address_3'] . ").";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                } elseif ($resultInvitee['result'] == 2 && !$resultInviter['result'][0]['epoch'] == $currentEpoch - 1) {
                    // inviter died
                    $text = "No invoice made for address : (" . $row['address_3'] . ").";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                } else {
                    // both died
                    $text = "You and the invitee didn't validate (" . $row['address_3'] . ").";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                }
            }

            break;
        case $currentEpoch - 3:

            $resultInvitee = curl_get(API_BASE_URL . "/Identity/" . $row['address_3'] . "/Age");
            $resultInviter = curl_get(API_BASE_URL . "/Identity/" . $row['address_1'] . "/EpochRewards?limit=1");
            if (isset($resultInvitee['result']) && isset($resultInviter['result'])) {
                if ($resultInvitee['result'] == 3 && $resultInviter['result'][0]['epoch'] == $currentEpoch - 1) {
                    $rewards = $conn->query("SELECT `value` FROM `config` WHERE `key` == 'validation_3_Rewards';")->fetch_row()[0];
                    $conn->query("INSERT INTO `invoices`(`epoch`, `userID`, `amount`, `info`) VALUES ('" . $row['epoch'] . "','" . $row['userID'] . "','" . $rewards . "','invoice for epoch " . $row['epoch'] . "');");

                    $text = "Validation 1 invoice for (" . $row['address_3'] . ") has been made.";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                } elseif ($resultInvitee['result'] == 0 && $resultInviter['result'][0]['epoch'] == $currentEpoch - 1) {
                    // invitee died
                    $text = "invitee died (" . $row['address_3'] . ").";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                } elseif ($resultInvitee['result'] == 3 && !$resultInviter['result'][0]['epoch'] == $currentEpoch - 1) {
                    // inviter died
                    $text = "No invoice made for address : (" . $row['address_3'] . ").";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");
                } else {
                    // both died
                    $text = "You and the invitee didn't validate (" . $row['address_3'] . ").";
                    $conn->query("INSERT INTO `history`(`text`, `userID`) VALUES ('" . $text . "','" . $userID . "');");

                }
            }

            break;
        default:
            # code...

            break;
    }
}

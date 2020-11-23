<?php
session_start();
require_once dirname(__FILE__) . "/../../common/_public.php";
require_once dirname(__FILE__) . "/../../vendor/autoload.php";
use Elliptic\EC;
use kornrunner\Keccak;
$result = (object) array();
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
function privateKeyToAddress($privateKey)
{

    $ec = new EC('secp256k1');
    $pubkey = $ec->keyFromPrivate($privateKey, 'hex')->getPublic(false, 'hex');
    return "0x" . substr(Keccak::hash(substr(hex2bin($pubkey), 1), 256), 24);
}

header('Content-Type: application/json');

if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned`,`type` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $banned = $data[1];
    $loggedUserType = $data[2];
    $conn->query("UPDATE `users` SET `lastseen` = CURDATE() WHERE `id` = '" . $loggedUserID . "';");

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
$type = $conn->query("SELECT `type` FROM `users` where `id` = '" . $loggedUserID . "' LIMIT 1 ;")->fetch_row()[0];
if ($type == 0) {
    $result->error = true;
    $result->reason = "Can't send invites";
    die(json_encode($result));
}
if (!isset($_POST['forID']) || !isset($_POST['invite'])) {
    $result->error = true;
    $result->reason = "Missing parameters";
    die(json_encode($result));
}
$forID = htmlspecialchars($conn->real_escape_string($_POST['forID']));
$invite = htmlspecialchars($conn->real_escape_string($_POST['invite']));
$forID = (int) $forID;

if (!strlen($invite) == 64) {

    $result->error = true;
    $result->reason = "invite is not valid";
    die(json_encode($result));
}

$epoch = $conn->query("SELECT `value` FROM `config` WHERE `key` = 'epoch';")->fetch_row()[0];
$address1 = $conn->query("SELECT `address` FROM `users` WHERE `id` = '" . $loggedUserID . "';")->fetch_row()[0];
$address2 = privateKeyToAddress($invite);
$inviteeRow = $conn->query("SELECT `address`,`pubkey` FROM `users` WHERE `id` = '" . $forID . "';")->fetch_row();
$address3 = $inviteeRow[0];
$payload = $inviteeRow[1];

$resultInvite = curl_get(API_BASE_URL . "/Identity/" . $address2);
$resultInvitee = curl_get(API_BASE_URL . "/Identity/" . $address3);
$resultInviteTxs = curl_get(API_BASE_URL . "/address/" . $address2 . "/txs?skip=0&limit=30");
if (isset($resultInvitee['error']['message'])) {
    $inviteAble = true;
} else {
    $inviteAble = inviteable($resultInvitee['result']['state']);
}
if (!isset($resultInvite['result']['state']) || !isset($resultInviteTxs['result'])) {

    $result->error = true;
    $result->reason = "ERROR 1";
    die(json_encode($result));
}
if (!($resultInvite['result']['state'] == 'Invite') || !$inviteAble) {

    $result->error = true;
    $result->reason = "ERROR 2";
    die(json_encode($result));
}
function canInvite()
{
    global $loggedUserType;
    global $resultInviteTxs;
    global $address1;
    if ((int)$loggedUserType == 3) {
        return true;
    } else {
        return strtolower(end($resultInviteTxs['result'])['from']) == $address1;
    }
}
if (!(end($resultInviteTxs['result'])['type'] == "InviteTx") || !canInvite()) {

    $result->error = true;
    $result->reason = "ERROR 3";
    die(json_encode($result));
}

$dna_activateInvite = json_encode(array("method" => "dna_activateInvite", "params" => array(["key" => $invite, "to" => $address3, "payload" => $payload]), "id" => 1, "key" => RPC_KEY));
$dna_activateInvite_result = curl_post(RPC_BASE_URL, $dna_activateInvite);
/*
$conn->query("INSERT INTO `invites`( `userID`, `forID`, `epoch`, `validations`, `address_1`, `address_2`, `address_3`) VALUES ('" . $loggedUserID . "','" . $forID . "','" . $epoch . "',0,'" . $address1 . "','" . $address2 . "','" . $address3 . "' );");
*/
$result->error = false;
$result->reason = $dna_activateInvite_result;
die(json_encode($result));

<?php
session_start();
require_once dirname(__FILE__) . "/../../common/_public.php";
require_once dirname(__FILE__) . "/../../vendor/autoload.php";
use Elliptic\EC;
use kornrunner\Keccak;

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
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $banned = $data[1];
    if ($banned) {
        $result = (object) array();
        $result->error = true;
        die(json_encode($result));
    }
} else {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}

if (!isset($_POST['forID']) || !isset($_POST['invite'])) {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}
$forID = htmlspecialchars($conn->real_escape_string($_POST['forID']));
$invite = htmlspecialchars($conn->real_escape_string($_POST['invite']));
$forID = (int) $forID;

if (!strlen($invite) == 64) {
    $result = (object) array();
    $result->error = true;
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

if (!isset($resultInvite['result']['state']) || !isset($resultInvitee['result']['state']) || !isset($resultInviteTxs['result'])) {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}
if (!($resultInvite['result']['state'] == 'Invite') || !inviteable($resultInvitee['result']['state'])) {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}

if (!(end($resultInviteTxs['result'])['type'] == "InviteTx") || !(strtolower(end($resultInviteTxs['result'])['from']) == $address1)) {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}

$dna_activateInvite = json_encode(array("method" => "dna_activateInvite", "params" => ["key" => $invite, "to" => $address3, "payload" => $payload], "id" => 1, "key" => RPC_KEY));
/*$dna_activateInvite_result = curl_post(RPC_BASE_URL, $dna_activateInvite);*/

$conn->query("INSERT INTO `invites`( `userID`, `forID`, `epoch`, `validations`, `address_1`, `address_2`, `address_3`) VALUES ('" . $loggedUserID . "','" . $forID . "','" . $epoch . "',0,'" . $address1 . "','" . $address2 . "','" . $address3 . "' );");
$result = (object) array();
$result->error = false;
die(json_encode($result));

<?php
include_once(dirname(__FILE__)."/../../common/_public.php");
require_once(dirname(__FILE__)."/../../vendor/autoload.php");
use Elliptic\EC;
use kornrunner\Keccak;

function pubKeyToAddress($pubkey)
{
    return "0x" . substr(Keccak::hash(substr(hex2bin($pubkey->encode("hex")), 1), 256), 24);
}

function verifySignature($message, $signature, $address)
{
    $hash   =  Keccak::hash(pack("H*", Keccak::hash(pack("H*", bin2hex($message)), 256)), 256);
    $sign   = ["r" => substr($signature, 2, 64),
               "s" => substr($signature, 66, 64)];
    $recid  = ord(hex2bin(substr($signature, 130, 2)));
    if ($recid != ($recid & 1)) {
        return false;
    }
    $ec = new EC('secp256k1');
    $pubkey = $ec->recoverPubKey($hash, $sign, $recid);
    return $address == pubKeyToAddress($pubkey);
}

function getPubKey($message, $signature)
{
    $hash   =  Keccak::hash(pack("H*", Keccak::hash(pack("H*", bin2hex($message)), 256)), 256);
    $sign   = ["r" => substr($signature, 2, 64),
             "s" => substr($signature, 66, 64)];
    $recid  = ord(hex2bin(substr($signature, 130, 2)));
    if ($recid != ($recid & 1)) {
        return false;
    }
    $ec = new EC('secp256k1');
    $pubkey = $ec->recoverPubKey($hash, $sign, $recid);
    return $pubkey->encode("hex");
}

function getstatus($address)
{
    if(strlen($address) < 20){
    return 'Undefined';
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, 'https://api.idena.org/api/identity/' . $address);
    $result = curl_exec($ch);
    curl_close($ch);
    $resultJSON = json_decode($result, true);
    if (isset($resultJSON['result']['state']))
    {
        return $resultJSON['result']['state'];
    }
    else
    {
        return 'Undefined';
    }

}
$json = file_get_contents('php://input');
$data = (array) json_decode($json);
if (!isset($data['token'])) {
    die();
};
if (!isset($data['signature'])) {
    die();
};
$dataToken = $conn->real_escape_string($data['token']);
$dataSig = $conn->real_escape_string($data['signature']);



$sql = "SELECT * FROM `auth_idena` WHERE `token` = '".$dataToken."' LIMIT 1;";
$result = $conn->query($sql);
header('Content-Type: application/json');

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $address   = $row['address'];
        $message   = $row['nonce'];
        $signature = $data['signature'];

        if (verifySignature($message, $signature, $address)) {
            $pubKey = getPubKey($message, $signature);
            $sql = "UPDATE `auth_idena` SET `sig` = '".$dataSig."', `authenticated` = 1 , `pubkey` = '".$pubKey."' WHERE `token` = '".$dataToken."' LIMIT 1;";
            $conn->query($sql);

            $sql = "Select id from users where address = '".$address."' limit 1 ;";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $sql = "UPDATE `users` SET `status` = '".getstatus($address)."' , `lastseen` = '".date("Y-m-d H:i:s",time())."' WHERE `users`.`address` = '".$address."';";
                $conn->query($sql);
            }else{
                $sql = "INSERT INTO `users`( `address`, `status`, `pubKey`) VALUES ('".$address."','".getstatus($address)."','".$pubKey."')";
                

                $conn->query($sql);
            }
            die('{"success":true,"data":{"authenticated":true}}');
        } else {
            die('{"success":true,"data":{"authenticated":false}}');
        }
    }
} else {
    die('{"success":true,"data":{"authenticated":false}}');
}

$conn->close();
?>
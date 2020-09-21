<?php
session_start();
include(dirname(__FILE__)."/../common/_public.php");
header('Content-Type: application/json');
require_once(dirname(__FILE__)."/../vendor/autoload.php");
use Elliptic\EC;
use kornrunner\Keccak;
function pubKeyToAddress($pubkey) {
    return "0x" . substr(Keccak::hash(substr(hex2bin($pubkey->encode("hex")), 1), 256), 24);
}
function check($message, $signature ,$address) {
  $hash   =  Keccak::hash( pack("H*", Keccak::hash(pack("H*", bin2hex($message)), 256))  ,256);
  $sign   = ["r" => substr($signature, 2, 64),
             "s" => substr($signature, 66, 64)];
  $recid  = ord(hex2bin(substr($signature, 130, 2)));
  if ($recid != ($recid & 1))
      return false;
  $ec = new EC('secp256k1');
  $pubkey = $ec->recoverPubKey($hash, $sign, $recid);
  return $address == pubKeyToAddress($pubkey);
}
if(!isset($_POST['nonce'])){
  die('{"result":"ERROR"}');
}
if(!isset($_POST['sig'])){
  die('{"result":"ERROR"}');
}
if(!isset($_POST['address'])){
  die('{"result":"ERROR"}');
}
if(check($_POST['nonce'], $_POST['sig'],$_POST['address']) == true){
   echo '{"result":"Passed"}';
}else{
   echo '{"result":"Failed"}';
}

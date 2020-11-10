<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');


if (!isset($argv[1])) {
    die("");
}
if (!$argv[1] == $conn->query("SELECT `value` FROM `config` WHERE `key` = 'cronPassword'")) {
    die("");
}

   $baseURL = "https://api.idena.io/api";
   $result =  curl_get($baseURL."/Epoch/Last");
   if (isset($result['result'])) {
       $result = $result['result'];
       $conn->query("UPDATE `config` SET `value` = ".$result['epoch']." WHERE `key` = 'epoch';");
       $conn->query("UPDATE `config` SET `value` = ".$result['minScoreForInvite']." WHERE `key` = 'minScoreForInvite';");
       $conn->query("UPDATE `config` SET `value` = ".$result['validationTime']." WHERE `key` = 'validationTime';");
       die("Success");
   }else{
       die("ERROR");
   }

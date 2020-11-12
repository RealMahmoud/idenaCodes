<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");


if (!isset($argv[1])) {
    die("");
}
if (!$argv[1] == CRON_PASSWORD) {
    die("");
}

   $epochArray = array(
    "method"=> "dna_epoch",
    "params"=> [],
    "id"=> 1,
    "key"=> RPC_KEY
    );
   $result =  curl_get(RPC_BASE_URL,$epochArray);
   if (isset($result['result'])) {
       $result = $result['result'];
       $conn->query("UPDATE `config` SET `value` = ".$result['epoch']." WHERE `key` = 'epoch';");
       $conn->query("UPDATE `config` SET `value` = ".$result['minScoreForInvite']." WHERE `key` = 'minScoreForInvite';");
       $conn->query("UPDATE `config` SET `value` = ".$result['validationTime']." WHERE `key` = 'validationTime';");
       die("Success");
   }else{
       die("ERROR");
   }

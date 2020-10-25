<?php
include_once(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
session_start();
$_SESSION['CODES-Token'] = GUID();
die(json_encode(["token" => $_SESSION['CODES-Token']]));

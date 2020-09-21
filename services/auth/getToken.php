<?php
include_once(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
session_start();
$_SESSION['CODES-Token'] = GUID();
 echo json_encode(["Token" => $_SESSION['CODES-Token']]);

?>
<?php
include_once(dirname(__FILE__)."/../common/_public.php");

// Take the raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = (array) json_decode($json);
$nonce = GUID();
if ($data['token'] == ''){die();};
if ($data['address'] == ''){die();};
$sql = "INSERT INTO auth (nonce,token, address)
VALUES ('".'signin-'.$nonce."', '".$data['token']."', '".$data['address']."')";
$conn->query($sql);
$conn->close();
header('Content-Type: application/json');
$result =(object)array();
$result->success=true;
$data =(object)array();
$data->nonce='signin-'.$nonce;
$result->data=$data;
echo json_encode($result);
?>



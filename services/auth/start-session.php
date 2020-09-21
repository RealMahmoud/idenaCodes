<?php
include_once(dirname(__FILE__)."/../../common/_public.php");
$json = file_get_contents('php://input');
$data = (array) json_decode($json);
$nonce = GUID();
if ($data['token'] == ''){die();};
if ($data['address'] == ''){die();};
$sql = "INSERT INTO `auth_idena` (nonce,token, address)
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



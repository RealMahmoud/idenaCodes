<?php
include_once(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
session_start();
if(isset($_SESSION['CODES-Token'])){

    $resultSQL = $conn->query("SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1'  LIMIT 1 ;");
    $row = $resultSQL->fetch_row();

if($row[0] == null){
    echo json_encode(["Logged" => False]);

}else{
    echo json_encode(["Logged" => True , "Address"=> $row[0]]);
}

}else{
 echo json_encode(["Logged" => False]);
}
?>
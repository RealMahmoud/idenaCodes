<?php
include_once(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');
session_start();
if (isset($_SESSION['CODES-Token'])) {
    $row = $conn->query("SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1'  LIMIT 1 ;")->fetch_row();
    if ($row == null) {
        die(json_encode(["Logged" => false]));
    } else {
        die(json_encode(["Logged" => true , "Address"=> $row[0]]));
    }
} else {
    die(json_encode(["Logged" => false]));
}

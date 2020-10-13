<?php
session_start();
include(dirname(__FILE__)."/../../common/_public.php");
header('Content-Type: application/json');

$id = $conn->real_escape_string($_GET['id']);
$id = htmlspecialchars($id);
$id = (int)$id;

$result = (object)array();

  $resultSQL = $conn->query("SELECT id FROM users where id = '".$id."' LIMIT 1;");
  $row = $resultSQL->fetch_row();
if ($row == null) {
    $result->error=true;
    echo json_encode($result);
} else {
    $result->error=false;
    $result->exist=true;
    echo json_encode($result);
}

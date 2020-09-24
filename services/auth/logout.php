<?php
session_start();
unset($_SESSION['CODES-Token']);
unset($_SESSION['CODES-Address']);
header('Content-Type: application/json');
echo json_encode(["Logged" => false]);

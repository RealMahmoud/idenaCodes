<?php
session_start();
unset($_SESSION['CODES-Token']);
unset($_SESSION['CODES-Address']);
header('Content-Type: application/json');
die(json_encode(["Logged" => false]));

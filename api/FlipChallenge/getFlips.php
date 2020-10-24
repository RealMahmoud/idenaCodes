<?php
session_start();
include(dirname(__FILE__) . "/../../common/_public.php");
header('Content-Type: application/json');

 $userID = $conn->query("SELECT id FROM `users` WHERE address = (SELECT address FROM `auth_idena` WHERE token = '".$_SESSION['CODES-Token']."');")->fetch_row()[0];
//only users access
$oldFlips = $conn->query("SELECT flips,score FROM `test_flips` WHERE userID = '" . $userID . "' LIMIT 1;")->fetch_assoc();

if ($oldFlips) {
    
    if (isset($oldFlips["score"])) {
        
        $result        = (object) array();
        $result->error = true;
        die(json_encode($result));
    } else {
        $flipsArray = array();
        $result         = (object) array();
        foreach (json_decode($oldFlips["flips"]) as $qID) {
            
            
            $resultSQL = $conn->query("SELECT * FROM flips WHERE id = '" . $qID . "';");
            
            while ($row = $resultSQL->fetch_assoc()) {
                
                $flip           = (object) array();
                $flip->id       = (int) $row['id'];
                $flip->flip = $row['url'];

                array_push($flipsArray, $flip);
                
                
            }
            
        }
        
        if (count($flipsArray) == 0) {
            $result->error = true;
        } else {
            $result->error     = false;
            $result->flips = $flipsArray;
        }
        
        die(json_encode($result));
    }
    
};

$result           = (object) array();
$resultSQL        = $conn->query("SELECT * FROM flips ORDER BY RAND() LIMIT 15;");
$flipsArray   = array();
$flipsIDArray = array();
while ($row = $resultSQL->fetch_assoc()) {
    
    $flip           = (object) array();
    $flip->id       = (int) $row['id'];
    $flip->url = $row['url'];
    array_push($flipsArray, $flip);
    array_push($flipsIDArray, (int) $row['id']);
    
}

$conn->query("INSERT INTO `test_flips`( `userID`, `flips`) VALUES ('" . $userID . "','" . json_encode($flipsIDArray) . "')");

if (count($flipsArray) == 0) {
    $result->error = true;
} else {
    $result->error     = false;
    $result->flips = $flipsArray;
}
echo (json_encode($result));
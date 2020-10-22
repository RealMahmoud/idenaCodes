<?php
session_start();
include(dirname(__FILE__) . "/../../common/_public.php");
header('Content-Type: application/json');

 $userID = $conn->query("SELECT id FROM `users` WHERE address = (SELECT address FROM `auth_idena` WHERE token = '".$_SESSION['CODES-Token']."');")->fetch_row()[0];

$oldQuestions = $conn->query("SELECT questions,answers FROM `test_questions` WHERE userID = '" . $userID . "' LIMIT 1;")->fetch_assoc();

if ($oldQuestions) {
    
    if (isset($oldQuestions["answers"])) {
        
        $result        = (object) array();
        $result->error = true;
        die(json_encode($result));
    } else {
        $questionsArray = array();
        $result         = (object) array();
        foreach (json_decode($oldQuestions["questions"]) as $qID) {
            
            
            $resultSQL = $conn->query("SELECT * FROM questions WHERE id = '" . $qID . "';");
            
            while ($row = $resultSQL->fetch_assoc()) {
                
                $question           = (object) array();
                $question->id       = (int) $row['id'];
                $question->question = $row['question'];
                $question->options  = json_decode($row['options']);
                array_push($questionsArray, $question);
                
                
            }
            
        }
        
        if (count($questionsArray) == 0) {
            $result->error = true;
        } else {
            $result->error     = false;
            $result->questions = $questionsArray;
        }
        
        die(json_encode($result));
    }
    
};

$result           = (object) array();
$resultSQL        = $conn->query("SELECT * FROM questions ORDER BY RAND() LIMIT 15;");
$questionsArray   = array();
$questionsIDArray = array();
while ($row = $resultSQL->fetch_assoc()) {
    
    $question           = (object) array();
    $question->id       = (int) $row['id'];
    $question->question = $row['question'];
    $question->options  = json_decode($row['options']);
    array_push($questionsArray, $question);
    array_push($questionsIDArray, (int) $row['id']);
    
}

$conn->query("INSERT INTO `test_questions`( `userID`, `questions`) VALUES ('" . $userID . "','" . json_encode($questionsIDArray) . "')");

if (count($questionsArray) == 0) {
    $result->error = true;
} else {
    $result->error     = false;
    $result->questions = $questionsArray;
}
echo (json_encode($result));
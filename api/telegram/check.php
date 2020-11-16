<?php
include_once dirname(__FILE__) . "/../../common/_public.php";
// private
header('Content-Type: application/json');
$result = (object) array();
session_start();
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $banned = $data[1];
    if ($banned) {
        
        $result->error = true;
        $result->reason = "Banned";
        die(json_encode($result));
    }
} else {
    
    $result->error = true;
    $result->reason = "Not logged in";
    die(json_encode($result));
}

if ($conn->query("SELECT `id` FROM `auth_telegram` where `userID` = (SELECT `id` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` WHERE token = '" . $_SESSION['CODES-Token'] . "'))")->fetch_row()) {
    die('Already exist');
}
$keys = array("id", "first_name", "last_name", "username", "photo_url", "auth_date");

function getClosest($search, $object)
{
    $closest = null;
    foreach ($object as $key => $xx) {
        if ($closest === null || abs($search - $closest) > abs($key - $search)) {
            $closest = $key;
        }
    }
    return $object[$closest];
}

$dates = file_get_contents("./dates.json");
$datesArr = json_decode($dates, true);

if (isset($_GET['hash'])) {
    foreach ($_GET as $key => $value) {
        if (in_array($key, $keys)) {
            $data[] = $key . "=" . $value;
        }
    }

    sort($data);
    $data = implode("\n", $data);
    $secretKey = hash('sha256', $telegramToken, true);
    $hash = hash_hmac('sha256', $data, $secretKey);
    if ((time() - $_GET['auth_date']) > 86400) {
        die($_GET['auth_date']);
    }
    if (hash_equals($hash, $_GET['hash'])) {
        $conn->query("INSERT INTO `auth_telegram`( `userID`, `tg_ID`, `tg_Username`, `time`,`tg_creationDate`) VALUES (
			(SELECT `id` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` WHERE token = '" . $_SESSION['CODES-Token'] . "')),
			'" . $_GET['id'] . "',
			'" . $_GET['username'] . "',
			'" . $_GET['auth_date'] . "',
			'" . getClosest($_GET['id'], $datesArr) . "'
		);");
        echo "Telegram data integrity check success!";
    } else {
        die('Data is not incorrect');
    }
}

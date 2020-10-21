<?php
include_once(dirname(__FILE__)."/../../common/_public.php");
// private
header('Content-Type: application/json');
session_start();

$keys = array("id", "first_name", "last_name", "username", "photo_url", "auth_date");


if (isset($_GET['hash'])) {
	foreach ($_GET as $key => $value) {
		if (in_array($key, $keys)) {
			$data[] = $key."=".$value;
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
		$conn->query("INSERT INTO `auth_telegram`( `userID`, `tg_ID`, `tg_Username`, `tg_photo_url`, `time`) VALUES (
			(SELECT id FROM `users` WHERE address = (SELECT address FROM `auth_idena` WHERE token = '".$_SESSION['CODES-Token']."')),
			'".$_GET['id']."',
			'".$_GET['username']."',
			'".$_GET['photo_url']."',
			'".$_GET['auth_date']."'
		);");
		echo "Telegram data integrity check success!";
	}else{
		die('Data is not incorrect');
	}
}
?>
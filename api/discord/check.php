<?php
include_once dirname(__FILE__) . "/../../common/_public.php";
session_start();
$result = (object) array();
if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $conn->query("UPDATE `users` SET `lastseen` = CURDATE() WHERE `id` = '" . $loggedUserID . "';");
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

if ($conn->query("SELECT `id` FROM `auth_discord` where `userID` = (SELECT `id` FROM `users` where `address` = (SELECT `address` FROM `auth_idena` WHERE token = '" . $_SESSION['CODES-Token'] . "'))")->fetch_row()) {
    header("location: /index.html");
    die('Already exist');
}

$GLOBALS['base_url'] = "https://discord.com";

if (!isset($_GET['code']) || !isset($_GET['state'])) {
    die('error');
}

$code = $_GET['code'];
$state = $_GET['state'];
$url = $GLOBALS['base_url'] . "/api/oauth2/token";
$data = array(
    "client_id" => DISCORD_CLIENT,
    "client_secret" => DISCORD_SECRET,
    "grant_type" => "authorization_code",
    "code" => $code,
    "redirect_uri" => DISCORD_CALLBACK,
);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response1 = curl_exec($curl);
curl_close($curl);
$results = json_decode($response1, true);

if (isset($results['access_token'])) {
    $url = $GLOBALS['base_url'] . "/api/users/@me";
    $headers = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $results['access_token']);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response2 = curl_exec($curl);
    curl_close($curl);
    $decodedResponse = json_decode($response2, true);

    $conn->query("INSERT INTO `auth_discord`( `userID`, `dc_creationDate`, `dc_username`, `dc_ID`) VALUES (
        '" . $loggedUserID . "',
        '" . substr(bindec(substr(decbin($decodedResponse['id']), 0, -22)) + 1420070400000, 0, -3) . "',
        '" . $decodedResponse['username'] . "#" . $decodedResponse['discriminator'] . "',
        '" . $decodedResponse['id'] . "'
    )");

    header("location: /index.html");
} else {
    die('error');
}

function check_state($state)
{
    if ($state == $_SESSION['state']) {
        return true;
    } else {
        return false;
    }
}

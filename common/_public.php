<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);


$site_name = 'Idena Codes';
$telegramToken= '';
$servername = "localhost";
$username = "";
$password = "";
$dbname = "idenacodes";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function curl_get($url)
{
    $cURLConnection = curl_init();

    curl_setopt($cURLConnection, CURLOPT_URL, $url);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($cURLConnection);
    curl_close($cURLConnection);

    return json_decode($data, true);
}

function GUID()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}


define('DISCORD_CLIENT', '');
define('DISCORD_SECRET', '');
define('DISCORD_CALLBACK', 'http://codes.localhost/api/discord/check.php');
define('DISCORD_SCOPE', 'identify');

define('TWITTER_KEY', '');
define('TWITTER_SECRET', '');
define('TWITTER_CALLBACK', 'http://codes.localhost/api/twitter/check.php');

<?php
include_once(dirname(__FILE__)."/../../common/_public.php");
session_start();
if (isset($_SESSION['CODES-Token'])) {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row()[0];
} else {
    $result = (object)array();
    $result->error=true;
    die(json_encode($result));
}

if ($conn->query("SELECT id FROM `auth_discord` where userID = (SELECT id FROM `users` WHERE address = (SELECT address FROM `auth_idena` WHERE token = '".$_SESSION['CODES-Token']."'))")->fetch_row()) {
    die('Already exist');
}



$GLOBALS['base_url'] = "https://discord.com";


if(!isset($_GET['code']) || !isset($_GET['state'])){
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
    "redirect_uri" => DISCORD_CALLBACK
    );
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    


if(isset($results['access_token'])){
    $url = $GLOBALS['base_url'] . "/api/users/@me";
    $headers = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $_SESSION['access_token']);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    $_SESSION['user'] = $results;
    $_SESSION['username'] = $results['username'];
    $_SESSION['discrim'] = $results['discriminator'];
    $_SESSION['user_id'] = $results['id'];
    $_SESSION['user_avatar'] = $results['avatar'];
    $_SESSION['user_email'] = $results['email'];
echo $results['email'];


}else{

}





    //


  
function check_state($state)
{
    if ($state == $_SESSION['state']) {
        return true;
    } else {
        return false;
    }
}



header("location ./index.php");

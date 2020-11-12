<?php
include_once(dirname(__FILE__)."/../../common/_public.php");
session_start();

if (isset($_SESSION['CODES-Token'])) {
    $data = $conn->query("SELECT `id`,`banned` FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '" . $_SESSION['CODES-Token'] . "' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row();
    $loggedUserID = $data[0];
    $banned = $data[1];
    if ($banned) {
        $result = (object) array();
        $result->error = true;
        die(json_encode($result));
    }
} else {
    $result = (object) array();
    $result->error = true;
    die(json_encode($result));
}
if ($conn->query("SELECT id FROM `auth_twitter` where userID = (SELECT id FROM `users` WHERE address = (SELECT address FROM `auth_idena` WHERE token = '".$_SESSION['CODES-Token']."'))")->fetch_row()) {
    header("location: /index.html");
    die('Already exist');
}
    require_once(dirname(__FILE__)."/../../vendor/autoload.php");
    use Abraham\TwitterOAuth\TwitterOAuth;

    $connection = new TwitterOAuth(TWITTER_KEY, TWITTER_SECRET);
        $request_token = $connection->oauth('oauth/request_token', array( 'oauth_callback' => TWITTER_CALLBACK ));
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
       
    
     

        $url = $connection->url('oauth/authorize', array( 'oauth_token' => $request_token['oauth_token'] ));
        header("location: ".$url);

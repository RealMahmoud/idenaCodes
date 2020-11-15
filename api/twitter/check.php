<?php
include_once dirname(__FILE__) . "/../../common/_public.php";
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

if ($conn->query("SELECT id FROM `auth_twitter` where userID = (SELECT id FROM `users` WHERE address = (SELECT address FROM `auth_idena` WHERE token = '" . $_SESSION['CODES-Token'] . "'))")->fetch_row()) {
    header("location: /index.html");
    die('Already exist');
}

require_once dirname(__FILE__) . "/../../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

if (isset($_GET['oauth_verifier']) && isset($_GET['oauth_token']) && isset($_SESSION['oauth_token']) && $_GET['oauth_token'] == $_SESSION['oauth_token']) {
    $connection = new TwitterOAuth(TWITTER_KEY, TWITTER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_GET['oauth_verifier']));
    $_SESSION['twitter_access_token'] = $access_token;

    $oauthToken = $_SESSION['twitter_access_token']['oauth_token'];
    $oauthTokenSecret = $_SESSION['twitter_access_token']['oauth_token_secret'];
    $connection = new TwitterOAuth(TWITTER_KEY, TWITTER_SECRET, $oauthToken, $oauthTokenSecret);
    $user = $connection->get("account/verify_credentials", ['include_email' => 'true']);

    if (property_exists($user, 'errors')) {
        $_SESSION = array();
        die('ERROR');
    } else {
        $conn->query("INSERT INTO `auth_twitter`(`userID`, `tw_creationDate`, `tw_ID`, `tw_username`) VALUES (
             '" . $loggedUserID . "',
             '" . strtotime($user->created_at) . "',
             '" . $user->id . "',
             '" . $user->screen_name . "'
            );");
        header("location: /index.html");
    }
} else {
    die('ERROR');
}

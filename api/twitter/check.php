<?php
    session_start();
    require 'config.php';
    require_once(dirname(__FILE__)."/../../vendor/autoload.php");
    use Abraham\TwitterOAuth\TwitterOAuth;

    if (isset($_SESSION['twitter_access_token']) && $_SESSION['twitter_access_token']) { // we have an access token
        $isLoggedIn = true;
    } elseif (isset($_GET['oauth_verifier']) && isset($_GET['oauth_token']) && isset($_SESSION['oauth_token']) && $_GET['oauth_token'] == $_SESSION['oauth_token']) { // coming from twitter callback url
        // setup connection to twitter with request token
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
        
        // get an access token
        $access_token = $connection->oauth("oauth/access_token", array( "oauth_verifier" => $_GET['oauth_verifier'] ));

        // save access token to the session
        $_SESSION['twitter_access_token'] = $access_token;

        // user is logged in
        $isLoggedIn = true;
    }

    if ($isLoggedIn) { // logged in
        // get token info from session
        $oauthToken = $_SESSION['twitter_access_token']['oauth_token'];
        $oauthTokenSecret = $_SESSION['twitter_access_token']['oauth_token_secret'];

        // setup connection
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauthToken, $oauthTokenSecret);

        // user twitter connection to get user info
        $user = $connection->get("account/verify_credentials", ['include_email' => 'true']);

        if (property_exists($user, 'errors')) { // errors, clear session so user has to re-authorize with our app
            $_SESSION = array();
            header('Refresh:0');
        } else { 
      $user->profile_image_url; 
 echo print_r($user, true); 
        }
    }

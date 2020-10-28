<?php
    session_start();
    require 'config.php';
    require_once(dirname(__FILE__)."/../../vendor/autoload.php");
    use Abraham\TwitterOAuth\TwitterOAuth;

    if(true){
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
        $request_token = $connection->oauth('oauth/request_token', array( 'oauth_callback' => OAUTH_CALLBACK ));
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        $isLoggedIn = false;
    }
     
    

    if ($isLoggedIn == false) { 
        $url = $connection->url('oauth/authorize', array( 'oauth_token' => $request_token['oauth_token'] )); 
         echo $url; 
         
    }

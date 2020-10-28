<?php

include_once(dirname(__FILE__)."/../../common/_public.php");
if (isset($_SESSION['CODES-Token'])) {
    $loggedUserID = $conn->query("SELECT id FROM `users` where `address` = (SELECT address FROM `auth_idena` where `token` = '".$_SESSION['CODES-Token']."' AND `authenticated` = '1' ) LIMIT 1 ;")->fetch_row()[0];
} else {
    $result = (object)array();
    $result->error=true;
    die(json_encode($result));
}

 $state = $_SESSION['state'] = bin2hex(openssl_random_pseudo_bytes(12));
 header('location '.'https://discordapp.com/oauth2/authorize?response_type=code&client_id=' . DISCORD_CLIENT . '&redirect_uri=' . DISCORD_CALLBACK . '&scope=' . DISCORD_SCOPE . "&state=" . $state);
<?php

include_once(dirname(__FILE__)."/../../common/_public.php");

 $state = $_SESSION['state'] = bin2hex(openssl_random_pseudo_bytes(12));
 header('location '.'https://discordapp.com/oauth2/authorize?response_type=code&client_id=' . DISCORD_CLIENT . '&redirect_uri=' . DISCORD_CALLBACK . '&scope=' . DISCORD_SCOPE . "&state=" . $state);
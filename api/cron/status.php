<?php
include_once dirname(__FILE__) . "/../../common/_public.php";

if (!isset($argv[1])) {
    die("");
}
if (!$argv[1] == CRON_PASSWORD) {
    die("");
}



function getstatus($address)
{
    if (strlen($address) != 42) {
        return 'Undefined';
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, 'https://api.idena.org/api/identity/' . $address);
    $result = curl_exec($ch);
    curl_close($ch);
    $resultJSON = json_decode($result, true);
    if (isset($resultJSON['result']['state'])) {
        return $resultJSON['result']['state'];
    } else {
        return 'Undefined';
    }
}


$resultSQL = $conn->query("SELECT `address` from `users`;");
while ($row = $resultSQL->fetch_assoc()) {
    $conn->query("UPDATE `users` SET `status` = '".getstatus($row[0])."' WHERE `address` = '".$row[0]."'");
}


$conn->query("UPDATE `users` SET `type` = 1 WHERE `status` = 'Human' OR `status` = 'Verified' OR `status` = 'Newbie';");
$conn->query("UPDATE `users` SET `type` = 0 WHERE `status` != 'Human' AND `status` != 'Verified' AND `status` != 'Newbie' AND `type` != 2;");
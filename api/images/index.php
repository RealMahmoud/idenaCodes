<?php

include dirname(__FILE__) . "/../../common/_public.php";
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $id = htmlspecialchars($id);
    $id = (int) $id;

    $row = $conn->query("SELECT `address` FROM `users` where `id` = '" . $id . "' LIMIT 1;")->fetch_row();

    if (isset($row[0])) {
        $imginfo = getimagesize("https://robohash.idena.io/" . $row[0]);
        header("Content-type: " . $imginfo['mime']);
        readfile("https://robohash.idena.io/" . $row[0]);
    } else {
        $rand = rand();
        $imginfo = getimagesize("https://robohash.idena.io/" . $rand);
        header("Content-type: " . $imginfo['mime']);
        readfile("https://robohash.idena.io/" . $rand);
    }

} else {
    $rand = rand();
    $imginfo = getimagesize("https://robohash.idena.io/" . $rand);
    header("Content-type: " . $imginfo['mime']);
    readfile("https://robohash.idena.io/" . $rand);
}

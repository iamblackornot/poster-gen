<?php

    $base64 = file_get_contents('base64.txt');
    $decoded=base64_decode($base64); 
    file_put_contents("qr.png", $decoded);

    echo('ok');
?>
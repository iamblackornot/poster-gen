<?php


$data = json_decode(file_get_contents('php://input'), true);
$saveFolder = "./testimages/";

if(empty($data)) {
    addToLogs("post data is empty");
    exit;
}

if(!file_exists($saveFolder)) {
    if(!mkdir($saveFolder, 0755, true)) {
        addToLogs("failed to create testimages folder");
        exit;
    }
}

try {

    $smallImage = getDataProperty($data, 'smallImage'); 
    $largeImage = getDataProperty($data, 'largeImage'); 
    $xlargeImage = getDataProperty($data, 'xlargeImage'); 

    $decoded=base64_decode($smallImage); 
    file_put_contents($saveFolder."small-image.jpg", $decoded);

    $decoded=base64_decode($largeImage); 
    file_put_contents($saveFolder."large-image.jpg", $decoded);

    $decoded=base64_decode($xlargeImage); 
    file_put_contents($saveFolder."xlarge-image.jpg", $decoded);

    echo 'ok';

} catch (Exception $e) {
    addToLogs('['.$posterid.'] Caught exception: '.$e->getMessage());
    echo 'not ok';
}

function addToLogs($text) {
    file_put_contents('logs.txt', $text.PHP_EOL, FILE_APPEND | LOCK_EX);
}

function getDataProperty($data, $name) {
    if(isset($data[$name]) && $data[$name] != "") {
        return $data[$name];
    } else {
        addToLogs("post data property [".$name."] is undefined");
        exit;
    }
}

?>
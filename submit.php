<?php

$templatesFolder = "./templates/";
$submissionFolder = "./groups/";
$posterHTML = "index.html";
$posterWAV = "nonar.wav";

if(!file_exists($submissionFolder)) {
    if(!mkdir($submissionFolder, 0755, true)) {
        addToLogs("failed to create submission folder");
        exit;
    }
}

$data = json_decode(file_get_contents('php://input'), true);

if(empty($data)) {
    addToLogs("post data is empty");
    exit;
}

try {

    $smallImage = getDataProperty($data, 'smallImage'); 
    $largeImage = getDataProperty($data, 'largeImage'); 
    $xlargeImage = getDataProperty($data, 'xlargeImage'); 
    $base64qrcode = getDataProperty($data, 'base64qrcode');

    $template = getDataProperty($data, 'template'); 
    $posterid = getDataProperty($data, 'posterid'); 
    $eventid = getDataProperty($data, 'eventid'); 
    $email = getDataProperty($data, 'email'); 
    $abstract = getDataProperty($data, 'abstract'); 
    $title = getDataProperty($data, 'title'); 
    $authors = getDataProperty($data, 'authors'); 
    $affiliates = getDataProperty($data, 'affiliates'); 
    $keywords = getDataProperty($data, 'keywords'); 

    $narrationWavUrl = $data['narrationWavUrl']; 
    $pdfUrl = getDataProperty($data, 'pdfUrl'); 

    //file_put_contents('last.txt', print_r($data, true));

    $posterFolder = $submissionFolder.$eventid."/".$posterid."/";

    if(!file_exists($posterFolder)) {
        if(!mkdir($posterFolder, 0755, true)) {
            addToLogs("failed to create poster folder [".$eventid."/".$posterid."]");
            exit;
        }
    }

    dir_copy($templatesFolder.$template."/", $posterFolder);

    rename($posterFolder.$posterHTML, $posterFolder.$posterid.".html");
    rename($posterFolder.$posterWAV, $posterFolder.$posterid.".wav");

    download($pdfUrl, $posterFolder.$posterid.".pdf");

    if(!empty($narrationWavUrl)) {

        download($narrationWavUrl, $posterFolder.$posterid.".wav");
    }

    file_put_contents($posterFolder."email.txt", $email);

    $html = file_get_contents($posterFolder.$posterid.".html");
    
    $tags = array("{posterid}", "{posterTitle}", "{posterAuthors}", "{posterAffiliates}", "{posterAbstract}", "{keywords}");
    $replace = array($posterid, $title, $authors, $affiliates, $abstract, $keywords);

    file_put_contents($posterFolder.$posterid.".html", str_replace($tags, $replace, $html));

    $decoded=base64_decode($smallImage); 
    file_put_contents($posterFolder."small-image.jpg", $decoded);

    $decoded=base64_decode($largeImage); 
    file_put_contents($posterFolder."large-image.jpg", $decoded);

    $decoded=base64_decode($xlargeImage); 
    file_put_contents($posterFolder."xlarge-image.jpg", $decoded);

    $decoded=base64_decode($base64qrcode); 
    file_put_contents($posterFolder."qr.png", $decoded);

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

function dir_copy( $source, $target ) {
    if ( is_dir( $source ) ) {
        mkdir( $target, 0755, true );
        $d = dir( $source );
        while ( FALSE !== ( $entry = $d->read() ) ) {
            if ( $entry == '.' || $entry == '..' ) {
                continue;
            }
            $Entry = $source . '/' . $entry; 
            if ( is_dir( $Entry ) ) {
                dir_copy( $Entry, $target . '/' . $entry );
                continue;
            }
            copy( $Entry, $target . '/' . $entry );
        }

        $d->close();
    }else {
        copy( $source, $target );
    }
}

function download($url, $path) {
    
    set_time_limit(60);
    $fp = fopen ($path, 'w');
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    curl_setopt($ch, CURLOPT_FILE, $fp); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    curl_exec($ch); 
    curl_close($ch);
    fclose($fp);
}

?>
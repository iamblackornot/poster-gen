<?php

$templatesFolder = "./templates/";
$posterHTML = "index.html";
$posterWAV = "nonar.wav";
$nonCopyFiles = array("small-image.jpg", "large-image.jpg", "project.mobirise");

$data = json_decode(file_get_contents('php://input'), true);

if(empty($data)) {
    addToLogs("post data is empty");
    exit;
}

try {

    $smallImage = getDataProperty($data, 'smallImage', false); 
    $largeImage = getDataProperty($data, 'largeImage', false); 
    $xlargeImage = getDataProperty($data, 'xlargeImage', false); 

    $posterid = getDataProperty($data, 'posterid'); 
    $eventid = getDataProperty($data, 'eventid', false);
    $email = getDataProperty($data, 'email'); 
    $abstract = getDataProperty($data, 'abstract'); 
    $title = getDataProperty($data, 'title'); 
    $authors = getDataProperty($data, 'authors'); 
    $affiliates = getDataProperty($data, 'affiliates'); 
    $keywords = getDataProperty($data, 'keywords'); 
    $template = getDataProperty($data, 'template');  
    $groupFolder = getDataProperty($data, 'folder'); 


    $base64qrcode = getDataProperty($data, 'base64qrcode', false);
    $narrationWavUrl = getDataProperty($data, 'narrationWavUrl', false);
    $pdfUrl = getDataProperty($data, 'pdfUrl'); 

    $posterFolder = createPosterFolder(getPosterPath($groupFolder, $eventid, $posterid));
   
    dir_copy($templatesFolder.$template."/", $posterFolder, $nonCopyFiles);

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

    if(!empty($smallImage)) {
        $decoded=base64_decode($smallImage); 
        file_put_contents($posterFolder."small-image.jpg", $decoded);
    }

    if(!empty($largeImage)) {
        $decoded=base64_decode($largeImage); 
        file_put_contents($posterFolder."large-image.jpg", $decoded);
    }

    if(!empty($xlargeImage)) {
        $decoded=base64_decode($xlargeImage); 
        file_put_contents($posterFolder."xlarge-image.jpg", $decoded);
    }

    if(!empty($base64qrcode)) {
        $decoded=base64_decode($base64qrcode); 
        file_put_contents($posterFolder."qr.png", $decoded);
    }

    echo 'ok';

} catch (Exception $e) {
    addToLogs('['.$posterid.'] Caught exception: '.$e->getMessage());
    echo 'not ok';
}

function addToLogs($text) {
    file_put_contents('logs.txt', $text.PHP_EOL, FILE_APPEND | LOCK_EX);
}

function getDataProperty($data, $name, $required = true, $defaultValue = '') {
    if(isset($data[$name]) && $data[$name] != "") {
        return $data[$name];
    } else {

        if($required) {
            addToLogs("post data property [".$name."] is undefined");
            exit;
        } else {
            return $defaultValue;
        }
    }
}

function dir_copy( $source, $target, $nonCopyFiles ) {
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

            if(in_array($entry, $nonCopyFiles)) {
                continue;
            }

            copy( $Entry, $target . '/' . $entry );
        }

        $d->close();
    }else {

        if(in_array($entry, $nonCopyFiles)) {
            return;
        }

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

function getPosterPath($groupFolder, $eventid, $posterid) {

    $posterFolder = "./".$groupFolder."/";//.$eventid."/".$posterid."/";

    if(!empty($eventid)) {
        $posterFolder = $posterFolder.$eventid."/";
    }

    $posterFolder = $posterFolder.$posterid."/";

    return $posterFolder;
}

function createPosterFolder($posterFolder) {

    if(!file_exists($posterFolder)) {
        
        createFolder($posterFolder);
        return $posterFolder;
    } else {

        $inc = 1;
        
        while($inc < 100) {

            $newFolderName = substr_replace($posterFolder, "-".$inc, -1, -1);

            if(!file_exists($newFolderName)) {
        
                createFolder($newFolderName);
                return $newFolderName;
            }

            $inc += 1;
        }
    }

    addToLogs("failed to create folder [".$posterFolder."] (while cycle didn't help)");
    exit;
}

function createFolder($folder) {

    if(!mkdir($folder, 0755, true)) {
        addToLogs("failed to create folder [".$folder."]");
        exit;
    }
}
?>
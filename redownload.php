<?php
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
    
    if (curl_errno($ch)) {
        $curlErrorMessage = "CURL Error: " . curl_error($ch);
        addToLogs($curlErrorMessage);
        echo $curlErrorMessage, PHP_EOL;
        return false;
    }

    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($responseCode >= 400) {
        $httpErrorMessage =  "HTTP Error: " . $responseCode;
        addToLogs($httpErrorMessage);
        echo $httpErrorMessage, PHP_EOL;
        return false;
    }

    curl_close($ch);
    fclose($fp);

    return true;
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

    function getPosterPath($groupFolder, $eventid, $posterid) {

        $posterFolder = getEventPath($groupFolder, $eventid).$posterid."/";
    
        return $posterFolder;
    }
    
    function getEventPath($groupFolder, $eventid) {
    
        $eventFolder = "./".$groupFolder."/";
    
        if(!empty($eventid)) {
            $eventFolder = $eventFolder.$eventid."/";
        }
    
        return $eventFolder;
    }

    $data = json_decode(file_get_contents('php://input'), true);

if(empty($data)) {

    addToLogs("post data is empty");
    exit;
}

try {

    $posterid = getDataProperty($data, 'posterid'); 
    $eventid = getDataProperty($data, 'eventid', false);
    $groupFolder = getDataProperty($data, 'folder'); 
    $pdfUrl = getDataProperty($data, 'pdfUrl'); 

    $posterFolder = getPosterPath($groupFolder, $eventid, $posterid);

    if(!is_dir($posterFolder)) {
        echo "poster folder is not found = ", $posterFolder;
        return;
    }

    // $url = "https://www.jotform.com/uploads/akotoulas/221737597624163/5770138744121654408/Poster%20Presentation%20-%20Final.pdf";
    // $dest = "./test.pdf";
    if(!download($pdfUrl, $posterFolder.$posterid.".pdf")) {

        echo "failed to download ", $pdfUrl;
        return;
    };

    echo 'ok';

} catch (Exception $e) {
    addToLogs('['.$posterid.'] Caught exception: '.$e->getMessage());
    echo 'not ok';
}

?>
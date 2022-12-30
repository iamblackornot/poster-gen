<?php

$passFilePath = ".pass";

$pass = readPassword();

if(empty($pass) || isset($_GET['page'])) {

    showEventPage();
} else if(isset($_POST['pass'])) {

    if($_POST['pass'] == $pass) {
        showEventPage();
    } else {
        showPassQuery(true);
    }
} else {

    showPassQuery(false);
}

function showEventPage() {

    redirect();
}

function getEventPageURL() {

    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "index.html";
}

function redirect()
{
    $url = getEventPageURL();

    echo '<script type="text/javascript">
    window.location = "'.$url.'"
    </script>';
}

function readPassword() {

    global $passFilePath;

    if(!file_exists($passFilePath)) {
        return '';
    }

    return file_get_contents($passFilePath);
}

function showPassQuery($wrongPass)
{
    echo 
    '<!DOCTYPE html>
    <html lang=en">
    
    <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Private poster</title>
    
      <link href="./assets/private/private.css" rel="stylesheet">
      <link href="./assets/private/bootstrap-4.4.1.css" rel="stylesheet">
    </head>
    
    <body>
      <div class="jumbotron jumbotron-fluid text-center password-screen">
        <p class="lead">This event is private. Enter password to gain access:</p>
        <div class="password-container">
        <form id="pass-form" action="./" method="post">
           <input type="password" name="pass" id="textfield">
                <input type="submit" value="ENTER" class="btn btn-info btn-search-private btn-lg" id="enter-btn">
            </form>
        </div>';

    if($wrongPass) {
        echo '<p id="incorrect-pass-notify">wrong password</p>';
    }

    echo '</div></body></html>';
}

?>


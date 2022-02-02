<?php

$posterPassFile = "admin/pass-poster.txt";

$posterid = basename(dirname(__FILE__));

$pass = readPosterPassword();

if(empty($pass)) {

    showPosterPage();
} else if(isset($_POST['pass'])) {

    if($_POST['pass'] == $pass) {
        showPosterPage();
    } else {
        showPassQuery(true);
    }
} else {

    showPassQuery(false);
}



function showPosterPage() {

    global $posterid;
    readfile($posterid.'.html');
}

function readPosterPassword() {

    global $posterPassFile;

    if(!file_exists($posterPassFile)) {
        return '';
    }

    return file_get_contents($posterPassFile);
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
        <p class="lead">This poster is private. Enter password to gain access:</p>
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


<?php

$posterPassFile = "pass-poster.txt";
$adminPassFile = "pass-admin.txt";

$posterid = basename(dirname(__FILE__));

$adminpass = readAdminPassword();

if(!isset($_POST['adminpass'])) {

    showPassQuery(false);
} else {

    if($_POST['adminpass'] != $adminpass) {

        showPassQuery(true);
    } else {

        writeChangesIfNeeded();

        echo getAdminPage($posterid, $adminpass);
    }
}

function writeChangesIfNeeded() {

    if(!isset($_POST['update']) || !isset($_POST['opened']) || !isset($_POST['posterpass'])) {
        
        return;
    }

    global $posterPassFile;

    if($_POST['opened'] === "opened" && file_exists($posterPassFile)) {

        unlink($posterPassFile);
    } else {

        file_put_contents($posterPassFile, $_POST['posterpass'], LOCK_EX);
    }
}

function getAdminPage($posterid, $adminpass) {

    //$open_select = (isset($_POST['poster']) && $_POST['poster'] === "opened") ? "checked" : "";
    //$open_select = (isset($_POST['poster']) && $_POST['poster'] === "opened") ? "checked" : "";
    $posterpass = readPosterPassword();
    $open_select = empty($posterpass) ? "checked" : "";
    //$open_select = (isset($_POST['opened']) && $_POST['opened'] === "opened") ? "checked" : "";
    $private_select = ($open_select !== "checked") ? "checked" : "";
    $required = !empty($posterpass) ? 'required="required"' : "";
    // echo $open_select.PHP_EOL;
    // echo $private_select.PHP_EOL;
  
    // exit(0);

    $admin_page = "<html>
    <head>
      <meta charset='UTF-8'>
      <meta http-equiv='X-UA-Compatible' content='IE=edge'>
      <meta name='viewport' content='width=device-width, initial-scale=1'>
      <link href='./assets/private/bootstrap-4.4.1.css' rel='stylesheet'>
      <link href='./assets/private/private.css' rel='stylesheet'>
      <title>Poster admin page</title>
    </head>
    <body>
      <div class='jumbotron jumbotron-fluid admin-page'>
      <form action='admin.php' method='post'>
        <input type='hidden' name='adminpass' value='{$adminpass}'>
        <input type='hidden' name='update' value='true'>
          <div class='select-item'>
            <h4>Poster [".$posterid."] availability</h3>
          </div>
          <div class='form-check select-item'>
            <input type='radio' id='opened' name='opened' value='opened' class='form-check-input' {$open_select}>
            <label for='opened' class='form-check-label'>Opened</label>
          </div>
          
          <div class='form-check select-item radio-center'>
            <input type='radio' id='private' name='opened' value='private' class='form-check-input' {$private_select}>
            <label for='private' class='form-check-label'>Available with password</label>
            <div style='width: 200px;'>
              <input type='text' class='form-control' id='private_input' name='posterpass' value='{$posterpass}' {$required}>
            </div>
          </div>
          
          <div class='select-item'>
            <button type='submit' class='btn btn-primary'>Submit</button>
          </div>
        </form>
      </div>
      
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src='./assets/private/jquery-3.4.1.min.js'></script>
      
      <script type='text/javascript'>
        $('#private_input').focus(function() {
          var priv = $('#private');
          priv.prop('checked', true);
        });
  
        $('#private').change(function() {
          var value = $(this).val();
          if (value === 'private') {
            $('#private_input').focus();
            $('#private_input').attr('required', true);
          }
        });

        $('#opened').change(function() {
          var value = $(this).val();
          if (value === 'opened') {
            $('#private_input').removeAttr('required');
          } 
        });
      </script>
    </body>
    </html>";
    return $admin_page;
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
      <title>Poster admin</title>
    
      <link href="./assets/private/private.css" rel="stylesheet">
      <link href="./assets/private/bootstrap-4.4.1.css" rel="stylesheet">
    </head>
    
    <body>
      <div class="jumbotron jumbotron-fluid text-center password-screen">
        <p class="lead">Enter the password from the e-mail you have recieved after submission:</p>
        <div class="password-container">
        <form id="pass-form" action="admin.php" method="post">
           <input type="password" name="adminpass" id="textfield">
                <input type="submit" value="ENTER" class="btn btn-info btn-search-private btn-lg" id="enter-btn">
            </form>
        </div>';

    if($wrongPass) {
        echo '<p id="incorrect-pass-notify">wrong password</p>';
    }

    echo '</div></body></html>';
}

function readAdminPassword() {

    global $adminPassFile;

    if(!file_exists($adminPassFile)) {
        echo 'Admin password hasn\'t been set. Please, contact support';
        exit(0);
    }

    return file_get_contents($adminPassFile);
}

function readPosterPassword() {

    global $posterPassFile;

    if(!file_exists($posterPassFile)) {
        return '';
    }

    return file_get_contents($posterPassFile);
}

?>
<?php

$posterPassFile = "pass-poster.txt";
$adminPassFile = "pass-admin.txt";

$posterid = basename(dirname(dirname(__FILE__)));

$adminpass = readAdminPassword();

if(!isset($_POST['adminpass'])) {

    showPassPrompt($posterid, false);
} else {

    if($_POST['adminpass'] != $adminpass) {

        showPassPrompt($posterid, true);
    } else {

        writeChangesIfNeeded();

        showAdminPage($posterid, $adminpass);
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

function showAdminPage($posterid, $adminpass) {
    echo getCustomPosterPresentationPage($posterid, getAdminPageCodeSection($posterid, $adminpass));
}

function getCustomPosterPresentationPage($posterid, $customCodeSection) {

    $html = file_get_contents("admin.html");
    $tags = array("{posterid}", "{customCodeSection}");
    $replace = array($posterid, $customCodeSection);

    return str_replace($tags, $replace, $html);  
}

function getAdminPageCodeSection($posterid, $adminpass) {

    $posterpass = readPosterPassword();
    $open_select = empty($posterpass) ? "checked" : "";
    $private_select = ($open_select !== "checked") ? "checked" : "";
    $required = !empty($posterpass) ? 'required="required"' : "";


    $admin_page = 
    "<section class='mbr-section article content1 cid-sSfKlcmr1k' id='content1-78'>
        <div class='container'>
            <div class='media-container-row'>
                <div class='col-md-12'>
                    <p class='mbr-text m-0 mbr-fonts-style mbr-black display-7'>The Admin panel lets you control the availability of your presentation to your viewers.
                        <br><strong>Public:</strong> Anyone with the link to your presentation can view it and interact with you.
                        <br><strong>Available with password:</strong> Only people you have shared your password with can view your presentation.&nbsp;</p>
                </div>
            </div>
        </div>
    </section>
    
    <form action='./' method='post' class='container-md mb-5' style = 'max-width: 500px;'>
        <input type='hidden' name='adminpass' value='{$adminpass}'>
        <input type='hidden' name='update' value='true'>
        <div class='row justify-content-md-center'>
            <div class='col-sm'>
                <h2 class='mbr-section-title mbr-fonts-style mbr-bold display-5'>Presentation Availability</h2>
            </div>
        </div>
        <div class='row justify-content-md-center'>
            <div class='col-sm'>
                <div class='form-check select-item'>
                    <input class='form-check-input' type='radio' id='opened' name='opened' value='opened'  {$open_select}>
                    <label class='form-check-label ml-1' for='opened'>Public</label>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-sm'>
                <div class='form-check select-item radio-center'>
                    <input class='form-check-input' type='radio' id='private' name='opened' value='private' {$private_select}>
                    <label class='form-check-label ml-1' for='private'>Available with password</label>
                </div>
            </div>
            <div class='col-sm'>
                <input class='form-control' type='text' id='private_input' name='posterpass' value='{$posterpass}' {$required}
                      style = 'min-height: 20px; margin-top: 15px;'>
            </div>
        </div>
        <div class='row'>
            <div class='col-sm'>
                <div class='select-item'>
                    <button type='submit' class='btn btn-primary btn-sm ml-0'>Save changes</button>
                </div>
            </div>
        </div>
    </form>

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
    </script>";
    return $admin_page;
}

function showPassPrompt($posterid, $wrongPass) {
  echo getCustomPosterPresentationPage($posterid, getPassPromptSection($wrongPass));
}


function getPassPromptSection($wrongPass)
{
    $code =
      '<section class="mbr-section article content1 cid-sSfKlcmr1k" id="content1-78">
          <div class="container">
              <div class="media-container-row">
                  <div class="col-md-12">
                      <p class="mbr-text m-0 mbr-fonts-style mbr-black display-7">Enter the password you received with your confirmation email to enter the Admin panel of your virtual presentation.</p>
                  </div>
              </div>
          </div>
      </section>

      <form action="./" method="post" class="container-md mb-5" style = "max-width: 500px;">
          <div class="row flex-nowrap">
              <div class="col-8">
                  <input class="form-control" type="password" name="adminpass" id="textfield"
                        style = "min-height: 36px; margin-top: 5px; min-width: 200px;">
              </div>
              <div class="col-sm">
                  <input class="btn-md btn-primary ml-0" type="submit" value="ENTER" id="enter-btn">
              </div>
          </div>'
          .($wrongPass ? '<p id="incorrect-pass-notify" style="margin-left: 2rem;">wrong password</p>' : '').
      '</form>';

      return $code;
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
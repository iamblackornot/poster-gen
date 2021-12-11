<?php

$path = './email.txt';
redirect(file_get_contents($path)); 

function redirect($mail)
{
    echo '<script type="text/javascript">
    window.location = "mailto:'.$mail.'"
    </script>';
}

?>
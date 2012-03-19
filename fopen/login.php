<?php

    require_once('login.class.php');
 
    $browserID = new BrowserID($_SERVER['HTTP_HOST'], $_POST['assertion']);

    if($browserID->verify_assertion()) {
 
       echo"Welcome, ". $browserID->getEmail();  

    } else {

       echo"Identification Failure";  
    }

?>
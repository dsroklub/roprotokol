<?php
# Validate JWT token
#include("jwt.php");

$cip=$_SERVER['REMOTE_ADDR'];
#error_log("IP address from client ". $cip);
    
if(!$skiplogin) {
    $remotepw="";
    if (isset($_SERVER['HTTP_PASSWORD'])) {
        $remotepw=$_SERVER['HTTP_PASSWORD'];
    }
    if (!($adminpw == $remotepw)) {
        error_log("login failed");
        echo '{"status":"notauthorized","error":"forkert password"}';
        exit;
    }
}
?> 

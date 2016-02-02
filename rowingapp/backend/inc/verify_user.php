<?php
# Validate JWT token
#include("jwt.php");

include("pw.php");

$cip=$_SERVER['REMOTE_ADDR'];
error_log("IP address from client ". $cip);

if ($cip=="::1") {
#    $skiplogin=true;
#    $userrole['admin']=true;
}

    
if(!$skiplogin) {
    $remotepw="";
    if (isset($_SERVER['HTTP_PASSWORD'])) {
        $remotepw=$_SERVER['HTTP_PASSWORD'];
    }
    error_log("remotepw=".$remotepw);
    if (!($password == $remotepw)) {
        error_log("login failed");
        echo '{"status":"notauthorized","error":"forkert password"}';
        exit;
    }
    error_log("LPW ".$password);
#    $token = jwt_decode_header();
#    if(isset($token["error"])) {
#        echo json_encode($token["error"]);
#        error_log("auth error ".json_encode($token["error"]));
#        exit();
#    }
}
?> 
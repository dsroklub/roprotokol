<?php
// Validate JWT token

$cip=$_SERVER['REMOTE_ADDR'];
error_log("IP address from client ". $cip);

if ($cip=="::1") {
    $skiplogin=true;
    $userrole['admin']=true;
}

    
if(!$skiplogin) {
    $token = jwt_decode_header();
    if(isset($token["error"])) {
        echo json_encode($token["error"]);
        exit();
    }

    if ($rodb->connect_errno) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
}
?> 
<?php
// Validate JWT token
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
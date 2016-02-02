<?php
set_include_path('.:..:/backend');
error_log($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);


if (defined('MYSQLI_OPT_INT_AND_FLOAT_NATIVE')) {
    $rodb->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
}

if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
 
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

?>
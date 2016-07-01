<?php
header('Content-type: application/json');
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'Off');
error_reporting(E_ALL);
    

$sqldebug=false;
if (isset($_GET["sqldebug"])) {
    $sqldebug=true;
}

require_once("db.php");
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

?>

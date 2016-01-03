<?php
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'On');
error_reporting(E_ALL);

#$skiplogin=1;
    
if(!isset($_SESSION)){
  session_start();
}
if (isset($_GET["season"])) {
    $season=$_GET["season"];
} else {
//  $season=date('Y');
    $season=2014;
}

require_once("db.php");
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}



?>
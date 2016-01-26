<?php
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'On');
error_reporting(E_ALL);

$skiplogin=false;
    
if(!isset($_SESSION)){
  session_start();
}
if (isset($_GET["season"])) {
    $season=$_GET["season"];
} else {
  $season=date('Y');
//    $season=2015;
}

require_once("db.php");
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}


function invalidate($tp) {
    $mem  = new Memcached();
    $mem->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
    $mem->addServer('127.0.0.1',11211);
    $mem->increment($tp, 1, time());

}


?>
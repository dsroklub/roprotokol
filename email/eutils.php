<?php
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'Off');
error_reporting(E_ALL);
date_default_timezone_set("Europe/Copenhagen");

define( 'ROOT_DIR', dirname(__FILE__)."/.." );
set_include_path(get_include_path() . PATH_SEPARATOR  . ROOT_DIR);

$sqldebug=false;
if (isset($_GET["sqldebug"])) {
    $sqldebug=true;
}

function mysdate($jsdate) {
    $r=preg_replace("/\.\d\d\dZ/","",$jsdate);
    return($r);
}

function invalidate($tp) {
    $mem  = new Memcached();
    $mem->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
    $mem->addServer('127.0.0.1',11211);
    $mem->increment($tp, 1, time());

}
function dbErr(&$db, $err="") {
    error_log("Database Eerror: $db->error $err");
    echo "Database Eerror: $db->error $err";
    $db->rollback();
    $db->close();
    exit(1);
}

function eventLog($entry) {
    error_log("log $entry");
    global $rodb;
    if ($stmt = $rodb->prepare("INSERT INTO event_log (event,event_time) VALUES(?,NOW())")) {
        $stmt->bind_param('s', $entry);
        $stmt->execute();
    } else {
        error_log($rodb->error);
    }
}
$res=array ("status" => "ok");

$config = parse_ini_file(dirname(__FILE__).'/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

if (defined('MYSQLI_OPT_INT_AND_FLOAT_NATIVE')) {
    $rodb->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
}
if ($rodb->connect_errno) {
    printf("Email DB connect failed: %s\n", mysqli_connect_error());
    exit();
}

function sanestring($s) {
   $s1=filter_var(str_replace(">","",str_replace("<","",$s)), FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_BACKTICK|FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_AMP);
   $r=preg_replace('/&#\d+;/',"",$s1);
   return $r;
}

if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

function saneEmail($s) {
    $sm=["ø"=>"oe","Ø"=>"Oe","æ"=>"ae","Æ"=>"Ae","å"=>"aa","Å"=>"Aa"];
    $allowedchars=".abcdefghijklmnopqrstuvwxyz01234567890=:_-#";
    $r=$s;
    foreach ($sm as $s => $p) {
        $r=str_replace($s,$p,$r);
    }
    $s=strtolower(mb_convert_encoding($r,"ascii"));
    $r="";
    for ($i=0; $i < strlen($s);$i++) {
        $c=$s[$i];
        if (is_numeric(strpos($allowedchars,$c))){
            $r.=$c;
        }
    }
    return $r;
}

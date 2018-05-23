<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'Off');
error_reporting(E_ALL);

define( 'ROOT_DIR', dirname(__FILE__) );
set_include_path(get_include_path() . PATH_SEPARATOR  . ROOT_DIR);
$skiplogin=false;

if(!isset($_SESSION)){
  session_start();
}
if (isset($_GET["season"])) {
    $season=$_GET["season"];
} else {
  $season=date('Y');
}

$sqldebug=false;
$output="json";
if (isset($_GET["sqldebug"])) {
    $sqldebug=true;
}
if (isset($_GET["output"]) and $_GET["output"]=="csv") {
    $output="csv";
}

require_once("db.php");
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
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
function dbErr(&$db, &$res, $err="") {
    $res["status"]=$db->error;
    error_log("Database error $db->error $err");
    http_response_code(500);
}

function dbfetch($db,$table, $columns=['*'], $orderby=null) {
    $s='SELECT '. implode(',',$columns) . '  FROM ' . $table;
        if ($orderby) {
            $s .= " ORDER BY ". implode(",",$orderby);
    }
    $result=$db->query($s) or die("Error in stat query: " . mysqli_error($db));
    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
        if ($first) $first=0; else echo ',';	  
        echo json_encode($row,	JSON_PRETTY_PRINT);
    }
    echo ']';
}
$error=null;

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

function process ($result,$output="json",$name="cvsfile",$captions=null) {
    if ($output=="json") {
        header('Content-type: application/json;charset=utf-8');
        echo '[';
        $rn=1;
        while ($row = $result->fetch_assoc()) {
            if ($rn>1) echo ',';
            echo json_encode($row,JSON_PRETTY_PRINT);
            $rn=$rn+1;
        }
        echo ']';
    } else if ($output=="csv") {
        header('Content-type: text/csv');
        header('Content-Disposition: filename="'.$name.'.csv"');
        if ($captions) {
            echo implode(",",$captions)."\n";
        }
        while ($row = $result->fetch_assoc()) {
            echo implode(",",$row)."\n";
        }
    }
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
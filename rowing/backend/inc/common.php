<?php
 require __DIR__.'/../vendor/autoload.php';
 use PhpOffice\PhpSpreadsheet\Spreadsheet;
 use PhpOffice\PhpSpreadsheet\Cell\DataType;
 use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 use PhpOffice\PhpSpreadsheet\Writer\Ods;
 use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

header('Content-Type: application/json; charset=utf-8');
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'Off');
error_reporting(E_ALL);
date_default_timezone_set("Europe/Copenhagen");

define( 'ROOT_DIR', dirname(__FILE__) );
set_include_path(get_include_path() . PATH_SEPARATOR  . ROOT_DIR);
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
if (isset($_GET["output"]) && ($_GET["output"]=="csv" || $_GET["output"]=="xlsx")) {
    $output=$_GET["output"];
}

require_once("db.php");
//if (!$rodb->set_charset("utf8")) {
//    printf("Error loading character set utf8: %s\n", $rodb->error);
//}

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
function roErr($err="fejl") {
    $res["status"]="error";
    $res["error"]="Fejl: ". $err;
    $res["context"]="FILE: ". $_SERVER['PHP_SELF'];
    error_log("$err, Fejl". $_SERVER['PHP_SELF']);
    http_response_code(500);
    echo json_encode($res);
    exit(1);
}

function dbErr(&$db, &$res, $err="") {
    $res["status"]="error";
    $res["error"]="DB ". $err . ": " .$db->error. " FILE: ". $_SERVER['PHP_SELF'];
    error_log("Database error: $db->error $err :". $_SERVER['PHP_SELF']);
    http_response_code(500);
    echo json_encode($res);
    $db->rollback();
    $db->close();
    exit(1);
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
        echo json_encode($row,JSON_PRETTY_PRINT);
    }
    echo ']';
}
$error=null;

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

function process ($result,$output="json",$name="csvfile",$captions=null) {
    $mariaType=[
        MYSQLI_TYPE_NEWDECIMAL=>"d",
        MYSQLI_TYPE_DECIMAL=>"d",
        MYSQLI_TYPE_LONGLONG=>"d",
        MYSQLI_TYPE_LONG=>"d",
        MYSQLI_TYPE_FLOAT=>"f",
        MYSQLI_TYPE_DOUBLE=>"f",
        MYSQLI_TYPE_VAR_STRING=>"s",
        MYSQLI_TYPE_STRING=>"s",
        MYSQLI_TYPE_DATETIME=>"t"
    ];
    $colTypes=[];
    $maxlengths=[];
    //error_log("process $output c=$captions");
    if ($captions=="_auto") {
        $captions=[];
        foreach ($result->fetch_fields() as $fi => $fl) {
            $captions[]=$fl->name;
            $colTypes[$fl->name]=$fl->type;
            $maxlengths[$fi]=$fl->max_length;
            if (!$maxlengths[$fi]) {
                $maxlengths[$fi]=$fl->length;
            }
            //echo print_r($fl,true)."\n<br>";
        }
    }
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
    } else if ($output=="tablejson") {
        header('Content-type: application/json;charset=utf-8');
        echo '{"name":'. json_encode($name) .  ','  ."\n";
        $fcaptions=[];
        foreach ($result->fetch_fields() as $fl) {
            $fcaptions[]=["name"=>$fl->name, "type" => $mariaType[$fl->type] ?? "s"];
            $captions[]=$fl->name;
            //            echo print_r($fl,true)."\n<br>";
        }
        echo '"captions":' .json_encode($fcaptions);
        echo ',"body":[';
        $rn=1;
        while ($row = $result->fetch_assoc()) {
            if ($rn>1) echo ',';
            echo json_encode(array_values($row),JSON_PRETTY_PRINT);
            $rn=$rn+1;
        }
        echo ']}';
    } else if ($output=="ods" || $output=="xlsx") {
        header('Content-Disposition: filename="'.$name.".$output".'"');
        if ($output=="ods"){
            header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
        } else {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }
        //\PhpOffice\PhpSpreadsheet\Shared\Date::setDefaultTimezone("UTC");
        header('Cache-Control: max-age=0');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle($name);
        $spreadsheet->getProperties()
            ->setCreator('DSR roprotokol')
            ->setTitle($name)
            ->setSubject($name)
            ->setDescription('DSR roprotokol rapport')
            ->setKeywords('DSR roprotokol aftaler');
        $ri=1;
        if ($captions) {
            foreach ($captions as $ci => $caption) {
                $sheet->setCellValueByColumnAndRow($ci+1,1,"$caption");
            }
            $sheet->freezePane("A2");
        }
        foreach ($maxlengths as $ci => $cw) {
            $spreadsheet->getActiveSheet()->getColumnDimensionByColumn($ci+1)->setWidth(min(80,max(5,$cw+1)));
        }
        $ri++;
        while ($row = $result->fetch_assoc()) {
            $ci=1;
            foreach ($row as $cn=>$rc) {
                if (isset($colTypes[$cn]) && isset($formatMap[$colTypes[$cn]])) {
                    if ($colTypes[$cn]==MYSQLI_TYPE_DATETIME && $output=="xlsx")  {
                        $rc=\PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($rc."Z"));
                        $dataType=DataType::TYPE_NUMERIC;
                        $sheet->getStyleByColumnAndRow($ci,$ri)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                    } else {
                        $dataType=$formatMap[$colTypes[$cn]];
                    }
                } else {
                    $dataType=DataType::TYPE_STRING;
                }
                $sheet->setCellValueExplicit([$ci++,$ri],$rc,$dataType);

            }
            $ri++;
        }
        $writer = ($output=="xlsx")?new Xlsx($spreadsheet):new Ods($spreadsheet);
        $writer->save('php://output');
    } else if ($output=="text") {
        header('Content-type: text/html');
        echo " <link rel=\"stylesheet\" href=\"/public/stat.css\">\n<table>\n";
        if ($captions) {
            echo "<tr>\n<th>";
            echo implode("</th><th>",$captions)."\n";
            echo "</th></tr>\n";
        }
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>";
            echo implode("</td><td>",$row)."\n";
            echo "</td></tr>\n";
        }
        echo "</table>\n";
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
$res=array ("status" => "ok");
$damageMaintenance=4;

function output_json(&$rl,$keyname=null) {
    if ($keyname) {
        echo "{\n";
        $first=1;
        while ($row = $rl->fetch_assoc()) {
            if ($first) $first=0; else echo ",\n";
            echo '"'.$row[$keyname].'":'.$row['json'];
        }
        echo "\n}";
    } else {
        echo '[';
        $first=1;
        while ($row = $rl->fetch_assoc()) {
            if ($first) $first=0; else echo ",\n";
            echo $row['json'];
        }
        echo ']';
    }
}

function output_rows(&$rl,$keyname=null) {
    echo '[';
    $first=1;
    while ($row = $rl->fetch_assoc()) {
        if ($first) $first=0; else echo ",\n";
        echo json_encode($row);
    }
    echo ']';
}

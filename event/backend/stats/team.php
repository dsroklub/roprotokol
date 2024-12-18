<?php
ini_set('default_charset', 'utf-8');
include("../inc/common.php");
include("../inc/utils.php");
//$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: filename="gymnastik ugestatistik.xlsx"');
header('Cache-Control: max-age=0');
ini_set('display_errors', 'On');

$debug=isset($_GET["debug"]);
$msg="d: ";

$weekdays=["Man","Tir","Ons","Tor","Fre","Lør","Søn"];

$format="xlsx";
$y=date('Y');

//$y=2023;
if (isset($_GET["format"]) && $_GET["format"]=="xlsx") {
    $format="xlsx";
}
if (isset($_GET["year"])) {
    if ($_GET["year"] <0) {
        $y=$y+(int)($_GET["year"]);
    } else {
        $y=((int)($_GET["year"]));
    }
}

header('Content-Disposition: filename="gymnastik ugestatistik '.$y.'.xlsx"');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet()->setTitle("gymnastik stats $y");
    //->freezePaneByColumnAndRow(2,2);
$sheet->getColumnDimensionByColumn(1)->setWidth(15);
$spreadsheet->getProperties()
    ->setCreator('DSR roprotokol')
    ->setTitle("gymnastik statistik")
    ->setSubject("gym stats")
    ->setDescription(' statistik DSR gymnastikhold')
    ->setKeywords('DSR gymnastik statistik');


$sheet->setCellValueExplicit([1,1],"Gymnastik $y",DataType::TYPE_STRING);
$sheet->setCellValue([1,2],"Uge nummer");
$sheet->setCellValue([1,3],"hold");
$sheet->setCellValue([1,4],"underviser");
$sheet->setCellValue([1,5],"tid");

$teamIx=[];
$teams=
"SELECT DISTINCT team_participation.team, team_participation.timeofday,GROUP_CONCAT(DISTINCT team.teacher) as teacher, IF(DAYOFWEEK(start_time)<2,7,DAYOFWEEK(start_time)-1) dayno
   FROM team_participation LEFT JOIN team ON team.name=team_participation.team AND team.timeofday=team_participation.timeofday AND team.dayofweek=team_participation.dayofweek
   WHERE YEAR(start_time)=?
   GROUP BY team_participation.team, team_participation.timeofday, dayno, team_participation.dayofweek
   ORDER by dayno,team,timeofday
";
$stmt=$rodb->prepare($teams) or dbErr($rodb,$res,"teams prep");
$stmt->bind_param("i", $y) || dbErr($rodb,$res,"teams y bind");
$stmt->execute() || dbErr($rodb,$res,"teams exe");
$teamresult=$stmt->get_result() or dbErr($rodb,$res,"Error in team name stats query: ");

$col=2;
$startRow=6;

foreach ($teamresult as $tr) {
    $teamIx[$tr["dayno"]][$tr["team"]][$tr["timeofday"]]=$col;
    $sheet->setCellValue([$col,2],$weekdays[$tr["dayno"]-1]);
    $sheet->setCellValue([$col,3],$tr["team"]);
    if (!empty($tr["teacher"])) {
        $sheet->setCellValue([$col,4],$tr["teacher"]);
    }
    $sheet->setCellValue([$col,5],$tr["timeofday"]);
    $col++;
}
$totalCol=$col;

//print_r($teamIx);
// set_include_path(get_include_path().':..');
$s="
SELECT COUNT('x') AS h, dayofweek,timeofday,team, week(start_time,3) w,IF(DAYOFWEEK(start_time)<2,7,DAYOFWEEK(start_time)-1) dayno
  FROM team_participation
  WHERE YEAR(start_time)=?
  GROUP BY w, team,dayofweek,timeofday
  ORDER BY w,dayno
";
$stmt=$rodb->prepare($s) or dbErr($rodb,$res,"prep");

$stmt->bind_param("i", $y) || dbErr($rodb,$res,"bind");
$stmt->execute() || dbErr($rodb,$res,"exe");
$weekResult=$stmt->get_result() or dbErr($rodb,$res,"Error in team stats query: ");
$maxweeks=1;
foreach ($weekResult as $r) {
    $sheet->setCellValue([1,$startRow+$r['w']],$r["w"]);
    $sheet->setCellValue([$teamIx[$r["dayno"]][$r["team"]][$r["timeofday"]],$startRow+$r['w']],$r["h"]);
    if ($r['w']>$maxweeks) {
        $maxweeks=$r['w'];
    }
}
$msg.=" mw=$maxweeks";
//$sheet->setCellValueExplicitByColumnAndRow(2,1,"Gymnastik $y",DataType::TYPE_STRING );

$lastColName=Coordinate::stringFromColumnIndex($totalCol-1);
$ugeTotalColName=Coordinate::stringFromColumnIndex($totalCol);
$accColName=Coordinate::stringFromColumnIndex($totalCol+1);
$firstRow=$startRow+1;
$sheet->setCellValue([$totalCol+1,$firstRow],"=SUM(B${firstRow}:${lastColName}${firstRow})");

foreach (range(1,$maxweeks+1) as $cw) {
    $wr=$startRow+$cw;
    $sheet->setCellValue([1,$wr],$cw);
    $sheet->setCellValue([$totalCol,$wr],"=SUM(B${wr}:${lastColName}${wr})");
}

foreach (range(1,$maxweeks) as $cw) {
    $wr=$startRow+$cw;
    $wrNext=$wr+1;
    $sheet->setCellValue([$totalCol+1,$wr+1],"=${accColName}${wr}+${ugeTotalColName}${wrNext}");
}




$sheet->setCellValue([$totalCol,2],"ugetotal");
$totalRow=$startRow+$maxweeks+2;

$sheet->setCellValue([1,$totalRow],"TOTAL");
foreach (range(2,$totalCol) as $totCol) {
    $wr=$startRow+$cw;
    $colname=Coordinate::stringFromColumnIndex($totCol);
    $sheet->setCellValue([$totCol,$totalRow],"=SUM(${colname}7:${colname}${wr})");
}



if ($debug) {
    $sheet->setCellValue([1,1],$msg);
}
$sheet->freezePane("B6");

$statoutput="xlsx";
$writer = ($statoutput=="xlsx")?new Xlsx($spreadsheet):new Ods($spreadsheet);
$writer->save('php://output');
//$writer->save('t.xlsx');

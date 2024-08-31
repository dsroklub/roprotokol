<?php
ini_set('default_charset', 'utf-8');
include("../inc/common.php");
include("../inc/utils.php");
//$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: filename="kilometerstatistik.xlsx"');
header('Cache-Control: max-age=0');
ini_set('display_errors', 'On');


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



$teamIx=[];
$teams=
    "SELECT DISTINCT team_participation.team, teacher, IF(DAYOFWEEK(start_time)<2,7,DAYOFWEEK(start_time)-1) dayno FROM team_participation LEFT JOIN team ON team.name=team_participation.team WHERE YEAR(start_time)=? ORDER by dayno,team";
$stmt=$rodb->prepare($teams) or dbErr($rodb,$res,"teams prep");
$stmt->bind_param("i", $y) || dbErr($rodb,$res,"teams y bind");
$stmt->execute() || dbErr($rodb,$res,"teams exe");
$teamresult=$stmt->get_result() or dbErr($rodb,$res,"Error in team name stats query: ");

$maxweek=1;
$col=2;
foreach ($teamresult as $tr) {
    $teamIx[$tr["dayno"]][$tr["team"]]=$col;
    $sheet->setCellValue([$col,2],$weekdays[$tr["dayno"]-1]);
    $sheet->setCellValue([$col,3],$tr["team"]);
    if (!empty($tr["teacher"])) {
        $sheet->setCellValue([$col,4],$tr["teacher"]);
    }
    $col++;
}

//print_r($teamIx);
// set_include_path(get_include_path().':..');
$s=
"SELECT COUNT('x') AS h, dayofweek,timeofday,team, week(start_time,3) w,IF(DAYOFWEEK(start_time)<2,7,DAYOFWEEK(start_time)-1) dayno
FROM team_participation
WHERE YEAR(start_time)=?
GROUP BY w, team,dayofweek
ORDER BY w,dayno
";
$stmt=$rodb->prepare($s) or dbErr($rodb,$res,"prep");

$stmt->bind_param("i", $y) || dbErr($rodb,$res,"bind");
$stmt->execute() || dbErr($rodb,$res,"exe");
$result=$stmt->get_result() or dbErr($rodb,$res,"Error in team stats query: ");




//$sheet->setCellValueExplicitByColumnAndRow(2,1,"Gymnastik $y",DataType::TYPE_STRING );
$sheet->setCellValueExplicit([1,1],"Gymnastik $y",DataType::TYPE_STRING);
$sheet->setCellValue([1,2],"Uge");
$sheet->setCellValue([1,3],"hold");
$sheet->setCellValue([1,4],"underviser");


$row=5;
foreach ($result as $r) {
    //    print_r($r);
    $u[$r["w"]][$r["dayofweek"]]["team"]=$r['h'];
    if ($maxweek<$r["w"]) {
        $maxweek=$r["w"];
    }
}

//print_r($u);

foreach (range(1,$maxweek+1) as $cw) {
    $wr=$row+$cw;
    $colname=Coordinate::stringFromColumnIndex($col+1);
    $sheet->setCellValue([1,$wr],$cw);
    $sheet->setCellValue([$col+1,$wr],"=SUM(B${wr}:${colname}${wr})");
}


$totalRow=$row+$maxweek+2;
$totalCol=$col;
$sheet->setCellValue([$totalCol,6],"ugetotal");

foreach (range(2,$col+1) as $totCol) {
    $wr=$row+$cw;
    $colname=Coordinate::stringFromColumnIndex($totCol);
    $sheet->setCellValue([$totCol,$totalRow],"=SUM(${colname}6:${colname}${wr})");
}


$sheet->setCellValue([1,$totalRow],"TOTAL");

foreach ($result as $r) {
    $sheet->setCellValue([1,$row+$r['w']],$r["w"]);
    $sheet->setCellValue([$teamIx[$r["dayno"]][$r["team"]],$row+$r['w']],$r["h"]);

}

$sheet->freezePane("B5");

$statoutput="xlsx";
$writer = ($statoutput=="xlsx")?new Xlsx($spreadsheet):new Ods($spreadsheet);
#$writer->save('php://output');
$writer->save('t.xlsx');

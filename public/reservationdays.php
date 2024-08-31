<?php
require __DIR__.'/../rowing/backend/vendor/autoload.php';
$output="xlsx";
$days=['mandag','tirsdag','onsdag','torsdag','fredag','lørdag','søndag'];
set_include_path(get_include_path().':..');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
header('Content-Disposition: filename="ugereservationer.xlsx"');
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'On');

$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

$s='SELECT Boat.Name as boat, GROUP_CONCAT(TIME_FORMAT(start_time,"%H:%i"),"-",TIME_FORMAT(end_time,"%H:%i")," ",TripType.Name SEPARATOR "/") as reservation,dayofweek
    FROM reservation,Boat,TripType,BoatType
    WHERE Boat.id=reservation.boat AND TripType.id=triptype AND BoatType.name=Boat.boat_type AND dayofweek>0
    GROUP BY boat,dayofweek
    ORDER BY Boat.Name,dayofweek,start_time';

$result=$rodb->query($s) or die("Error in reservations query: " . mysqli_error($rodb));;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet()->setTitle("ugereservationer");
$spreadsheet->getProperties()
    ->setCreator('DSR roprotokol')
    ->setTitle("Ugeskema for bådreservationer")
    ->setSubject("Ugeskema")
    ->setDescription('DSR reserversationer af både, ugeskema')
    ->setKeywords('DSR roprotokol reservationer både');
$sheet->setCellValueByColumnAndRow(1,1,"Båd");
$spreadsheet->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth(25);
foreach ($days as $di => $day) {
    $spreadsheet->getActiveSheet()->getColumnDimensionByColumn($di+2)->setWidth(50);
    $sheet->setCellValueByColumnAndRow($di+2,1,"$day");
}

$sheet->freezePane("B2");
$row = $result->fetch_assoc();
$ri=1;
 while ($row) {
     $ri++;
     $boat=$row["boat"];
     $sheet->setCellValueExplicit([1,$ri],$row["boat"],DataType::TYPE_STRING);
     for ($d = 1; $d <= 7; $d++) {
         if ($d==isset($row["dayofweek"]) and $boat==$row["boat"]) {
             $sheet->setCellValueExplicit([$d+1,$ri],$row["reservation"],DataType::TYPE_STRING);
             $row = $result->fetch_assoc();
         }
     }
 }
$writer = ($output=="xlsx")?new Xlsx($spreadsheet):new Ods($spreadsheet);
$writer->save('php://output');

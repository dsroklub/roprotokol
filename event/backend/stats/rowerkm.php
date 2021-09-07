<?php
ini_set('default_charset', 'utf-8');
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
include("inc/utils.php");
$vr=verify_right(["admin"=>null,"data"=>"stat"]);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: filename="kilometerstatistik.xlsx"');
header('Cache-Control: max-age=0');
ini_set('display_errors', 'On');
$rowers = [];
$rower_list = [];
$categories = [];
$category_list = [];
$field_list = ['medlemsNr', 'email', 'navn'];
$force_email = isset($_GET["force_email"]) ? $_GET["force_email"] : null;
$now = getdate();
$year = isset($_GET["year"]) ? (int) $_GET["year"] : $now['year'];
$only_members = !!(isset($_GET["only_members"]) && $_GET["only_members"]);
$include_trips = !!(isset($_GET["include_trips"]) && $_GET["include_trips"]);
$separate_instruction = !!(isset($_GET["separate_instruction"]) && $_GET["separate_instruction"]);
$s = 'SELECT id, Name from BoatCategory ORDER BY Name';
$result=$rodb->query($s) or die("Error in query 1: " . mysqli_error($rodb));;

while ($row = $result->fetch_assoc()) {
  array_push($field_list, $row['Name'] . '_km');
  if ($include_trips) {
    array_push($field_list, $row['Name'] . '_ture');
  }
  if ($separate_instruction) {
    array_push($field_list, $row['Name'] . '_km_uden_instruktion');
    if ($include_trips) {
      array_push($field_list, $row['Name'] . '_ture_uden_instruktion');
    }
  }
}

$email_clause = "IF(m.ShowEmail, m.Email, NULL) as email";
if ($force_email) {
  $email_clause = "m.Email as email";
}

$s="SELECT m.id, m.MemberID as medlemsNr, " . $email_clause . ", CONCAT(m.FirstName, ' ', m.LastName) as navn
    FROM Member m
    WHERE m.id IN (SELECT DISTINCT tm.member_id
                   FROM Trip t
                   JOIN TripMember tm ON (t.id = tm.TripID)
                   WHERE YEAR(OutTime) = " . $year . ")
                     " . ( $only_members ? " AND m.RemoveDate IS NULL " : "") . "
    ORDER BY m.FirstName, m.LastName";

$result=$rodb->query($s) or die("Error in query 2: " . mysqli_error($rodb));;
while ($row = $result->fetch_assoc()) {
  $rowers[$row['id']] = $row;
  array_push($rower_list, $row['id']);
}


$s = "SELECT tm.member_id as member, bc.Name as category, ROUND(SUM(t.Meter)/1000) as km, COUNT(DISTINCT t.id) as trips
      FROM TripMember tm
      JOIN Trip t ON (t.id = tm.TripID)
      JOIN Boat b ON (b.id = t.BoatID)
      JOIN BoatType bt ON (b.boat_type = bt.Name)
      JOIN BoatCategory bc ON (bt.Category = bc.id)
      WHERE YEAR(t.OutTime)= " . $year . "
      GROUP BY tm.member_id, bc.id";

$result=$rodb->query($s) or dbErr($rodb,$res,"Error in query 3: " );
while ($row = $result->fetch_assoc()) {
   $rowers[$row['member']][ $row['category'] . '_km'] = $row['km'];
   $rowers[$row['member']][ $row['category'] . '_ture'] = $row['trips'];
}

if ($separate_instruction) {
  $s = "SELECT tm.member_id as member, bc.Name as category, ROUND(SUM(t.Meter)/1000) as km, COUNT(DISTINCT t.id) as trips
        FROM TripMember tm
        JOIN Trip t ON (t.id = tm.TripID)
        JOIN Boat b ON (b.id = t.BoatID)
        JOIN BoatType bt ON (b.boat_type = bt.Name)
        JOIN BoatCategory bc ON (bt.Category = bc.id)
        WHERE YEAR(t.OutTime)=" . $year . "
        AND t.TripTypeID NOT IN (SELECT id
                                 FROM TripType
                                 WHERE Name LIKE '%instruktion%')
        GROUP BY tm.member_id, bc.id";

  $result=$rodb->query($s) or dbErr($rodb,$res,"exe");
  while ($row = $result->fetch_assoc()) {
      $rowers[$row['member']][ $row['category'] . '_km_uden_instruktion'] = $row['km'];
      $rowers[$row['member']][ $row['category'] . '_ture_uden_instruktion'] = $row['trips'];
  }
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet()->setTitle("km-statistik");
    //->freezePaneByColumnAndRow(2,2);
$sheet->getColumnDimensionByColumn(3)->setWidth(30);
$sheet->getColumnDimensionByColumn(1)->setWidth(15);
$sheet->getColumnDimensionByColumn(2)->setWidth(25);
$spreadsheet->getProperties()
    ->setCreator('DSR roprotokol')
    ->setTitle("km statistik")
    ->setSubject("km stat")
    ->setDescription('km statistik for DSR roere')
    ->setKeywords('DSR roprotokol km statistik');


foreach($field_list as $ci=>$fld) {
    $sheet->getStyleByColumnAndRow($ci+1,1)->getAlignment()->setWrapText(true);
    $sheet->setCellValueExplicitByColumnAndRow($ci+1,1,$fld,DataType::TYPE_STRING);
    if ($ci>2) {
        $sheet->getColumnDimensionByColumn($ci+1)->setWidth(12);
        $sheet->getRowDimension('1')->setRowHeight(45);
    }

}
$sheet->freezePane("B2");
foreach ($rower_list as $ri=>$rower_id) {
  foreach ($field_list as $ci=>$field) {
      if (!empty($rowers[$rower_id][$field])) {
          $sheet->setCellValueExplicitByColumnAndRow($ci+1,$ri+2,$rowers[$rower_id][$field],($ci<3)?DataType::TYPE_STRING : DataType::TYPE_NUMERIC);
      }
  }
}

$spreadsheet->getActiveSheet()->getStyle('A1:O1')->getAlignment()->setWrapText(true);
$statoutput="xlsx";
$writer = ($statoutput=="xlsx")?new Xlsx($spreadsheet):new Ods($spreadsheet);
$writer->save('php://output');

<?php
set_include_path(get_include_path().':..');
include("inc/common.php");


$rowers = [];
$rower_list = [];
$categories = [];
$category_list = [];
$field_list = ['medlemsNr', 'email', 'navn'];

$force_email = isset($_GET["force_email"]) ? $_GET["force_email"] : null;
$now = getdate();
$year = isset($_GET["year"]) ? (int) $_GET["year"] : $now['year'];
$only_members = !!(isset($_GET["only_members"]) && $_GET["only_members"]);

$s = 'SELECT id, Name from BoatCategory ORDER BY Name';
$result=$rodb->query($s) or die("Error in query 1: " . mysqli_error($rodb));;
while ($row = $result->fetch_assoc()) {
  array_push($field_list, $row['Name'] . '_km', $row['Name'] . '_km_uden_instruktion');
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

error_log($s);

$result=$rodb->query($s) or die("Error in query 2: " . mysqli_error($rodb));;
while ($row = $result->fetch_assoc()) {
  $rowers[$row['id']] = $row;
  array_push($rower_list, $row['id']);
}


$s = "SELECT tm.member_id as member, bc.Name as category, ROUND(SUM(t.Meter)/1000) as km
      FROM TripMember tm
      JOIN Trip t ON (t.id = tm.TripID)
      JOIN Boat b ON (b.id = t.BoatID)
      JOIN BoatType bt ON (b.BoatType = bt.id)
      JOIN BoatCategory bc ON (bt.Category = bc.id)
      WHERE YEAR(t.OutTime)= " . $year . "
      GROUP BY tm.member_id, bc.id";

$result=$rodb->query($s) or die("Error in query 3: " . mysqli_error($rodb));;
while ($row = $result->fetch_assoc()) {
   $FieldName = $row['category'] . '_km';
   $rowers[$row['member']][$FieldName] = $row['km'];
}

$s = "SELECT tm.member_id as member, bc.Name as category, ROUND(SUM(t.Meter)/1000) as km
      FROM TripMember tm
      JOIN Trip t ON (t.id = tm.TripID)
      JOIN Boat b ON (b.id = t.BoatID)
      JOIN BoatType bt ON (b.BoatType = bt.id)
      JOIN BoatCategory bc ON (bt.Category = bc.id)
      WHERE YEAR(t.OutTime)=" . $year . "
      AND t.TripTypeID NOT IN (SELECT id
                               FROM TripType
                               WHERE Name LIKE '%instruktion%')
      GROUP BY tm.member_id, bc.id";

$result=$rodb->query($s) or die("Error in query 4: " . mysqli_error($rodb));;
while ($row = $result->fetch_assoc()) {
   $FieldName = $row['category'] . '_km_uden_instruktion';
   $rowers[$row['member']][$FieldName] = $row['km'];
}



header('Content-type: text/csv;charset=utf-8');
header('Content-Disposition: filename="rowerkm.csv"');

echo join(";", $field_list);
echo "\n";

foreach ($rower_list as $rower_id) {
  $first = true;
  foreach ($field_list as $field) {
     if (! $first) {
        echo ";";
     }
     echo $rowers[$rower_id][$field];
     $first = false;
  }
  echo "\n";
}


?> 


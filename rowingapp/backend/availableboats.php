<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

if (isset($_GET["location"])) {
    $location=$_GET["location"];
} else {
    echo "please set location";
    exit(1);
}

$s="SELECT BoatType.id AS boattypeid, BoatType.name as boattype,Count('q') as amount, GROUP_CONCAT(Boat.name SEPARATOR ', ') AS boats ".
  " From Boat,BoatType ".
  " WHERE ".
  " Boat.BoatType=BoatType.id AND ".
  " Boat.Location=? AND " .
  " NOT EXISTs (SELECT 'x' FROM Trip WHERE InTime IS NULL AND Trip.BoatID=Boat.id) AND ".
  " NOT EXISTS (SELECT 'x' FROM Damage WHERE Damage.Boat=Boat.id and Degree>2 AND REPAIRED IS NULL) AND ".
  " Boat.Name NOT LIKE '%Lånt%' AND" .
  " Boat.Name NOT LIKE '%privat%' AND" .
  " NOT EXISTS (SELECT 'x' FROM Reservation WHERE Boat.BoatType=BoatType.id AND Reservation.Boat=Boat.id AND Reservation.Begin<=Now() AND Reservation.End>=Now()) GROUP BY BoatType";

# echo $s."<br>";
if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("s", $location);
     $stmt->execute(); 
     $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));
     echo '{';
     $first=1;
     while ($row = $result->fetch_assoc()) {
       if ($first) $first=0; else echo ',';
       echo '"'.$row["boattype"].'":'. json_encode($row,JSON_PRETTY_PRINT);
     }
     echo '}';
}
$rodb->close();
?> 

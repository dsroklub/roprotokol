<!DOCTYPE html>
<head>
<link rel="stylesheet" href="basic.css">
</head>
<body>

<?php
set_include_path(get_include_path().':..');

include("inc/common.php");
include("inc/utils.php");
header('Content-type: text/html');

$s="SELECT Boat.id as boatid, Boat.Name AS boat, OutTime as outtime, InTime as intime, ExpectedIn as expectedintime, Trip.Destination as destination, Trip.id, TripType.Name AS triptype,GROUP_CONCAT(Member.MemberID,':§§:', MemberName SEPARATOR '££') AS rowers 
   FROM TripMember LEFT JOIN Member ON Member.id = TripMember.member_id, TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.id = Trip.BoatID) ON TripType.id = Trip.TripTypeID 
   WHERE Trip.id=TripMember.TripID AND (Trip.InTime Is Null OR Trip.InTime  >= CURDATE()) GROUP BY Trip.id ORDER BY InTime,ExpectedIn";

if ($sqldebug) {
  echo $s;
  echo "\n";
}

$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
?>
<table>
<caption>Dagens ture</caption>
<tr>
<th>Båd</th>
<th>Destination</th>
<th>Ud</th>
<th>Ind</th>
<th>Forventet ind</th>
<th>Roere</th>
</tr>

<?php
 while ($row = $result->fetch_assoc()) {
     echo "<tr>";
      $row['rowers']=multifield($row['rowers']);
      echo "<td>".$row['boat']."</td>";
      echo "<td>".$row['destination']."</td>";
      echo "<td>".$row['outtime']."</td>";
      echo "<td>".$row['intime']."</td>";
      echo "<td>".$row['expectedintime']."</td>";
      echo "<td><ul>";
      foreach ( $row['rowers'] as $rower) {
          echo "<li>".$rower."</li>";
      }
      echo "</ul></td>";
      
#	  echo json_encode($row,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT);
     echo "</tr>";
}
?>
</table>
<?php

$rodb->close();
?> 
</body>
</html>
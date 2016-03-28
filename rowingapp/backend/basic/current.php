<!DOCTYPE html>
<head>
<link rel="stylesheet" href="basic.css">
      <META HTTP-EQUIV="refresh" CONTENT="180">
      <meta http-equiv="cache-control" content="max-age=10" />
      <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
      <meta http-equiv="pragma" content="no-cache" />
      </head>
      <body>

<?php
set_include_path(get_include_path().':..');

include("inc/common.php");
include("inc/utils.php");
header('Content-type: text/html');

$s="SELECT Boat.id as boatid, Boat.Name AS boat, 
   IF (DAYOFYEAR(NOW())=DAYOFYEAR(OutTime),Date_Format(OutTime,'%H:%i'), Date_Format(OutTime,'%e/%c %H:%i')) as outtime, 
   IF (DAYOFYEAR(NOW())=DAYOFYEAR(InTime),Date_Format(InTime,'%H:%i'),Date_Format(InTime,'%e/%c %H:%i')) as intime, 
   IF (DAYOFYEAR(NOW())=DAYOFYEAR(ExpectedIn),Date_Format(ExpectedIn,'%H:%i'), Date_Format(ExpectedIn,'%e/%c %H:%i')) as expectedintime,
   NOW()>ExpectedIn as late,
   Trip.Destination as destination, Trip.id, TripType.Name AS triptype,
   GROUP_CONCAT(Member.MemberID,':§§:', CONCAT(Member.FirstName,' ',Member.LastName) SEPARATOR '££') AS rowers 
   FROM TripMember LEFT JOIN Member ON Member.id = TripMember.member_id, TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.id = Trip.BoatID) ON TripType.id = Trip.TripTypeID 
   WHERE Trip.id=TripMember.TripID AND (Trip.InTime Is Null OR Trip.InTime  >= CURDATE()) 
   GROUP BY Trip.id 
   ORDER BY InTime,ExpectedIn";

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
<th>Forv.</th>
<th>Roere</th>
</tr>

<?php
 while ($row = $result->fetch_assoc()) {
     if ($row['intime']) {
         echo "<tr class=tripisin>";
     } else if ($row['late']) {
         echo "<tr  class=late>";
     } else {
         echo "<tr  class=notin>";
     }
     $row['rowers']=multifield($row['rowers']);
     echo "<td>".$row['boat']."</td>";
     echo "<td>".$row['destination']."</td>";
     echo "<td>".$row['outtime']."</td>";
     echo "<td>".$row['intime']."</td>";
     echo "<td>".$row['expectedintime']."</td>";
     echo "<td>";
      $fr=true;
      foreach ( $row['rowers'] as $rower) {
          if (!$fr) echo ", ";
          echo $rower;
      $fr=false;
      }
      echo "</td>";
      echo "</tr>";
}
?>
</table>
<?php

$rodb->close();
?> 
</body>
</html>
<!DOCTYPE html lang="da-DK">
<html>
  <head>
    <link rel="stylesheet" href="basic.css">
    <meta charset="utf-8">
  </head>
  <body>
<?php
$member=$_GET["member"];
$res=array ("status" => "ok");
include("../rowing/backend/inc/common.php");
include("utils.php");
header('Content-type: text/html');

$isPublic=false;

if ($stmt = $rodb->prepare("SELECT member_setting.is_public,member_setting.show_status, CONCAT(Member.FirstName,' ',Member.LastName) as name
                            FROM member_setting,Member WHERE member=Member.id AND Member.MemberId=?")) {
    $stmt->bind_param("s", $member);
    $stmt->execute();
    $result= $stmt->get_result() or die("Error in user query: " . mysqli_error($rodb));
    if ($row = $result->fetch_assoc()) {
        if ($row['is_public']==1) {
            $is_public=true;
            $name=$row['name'];
            $show_status=$row['show_status'];
        }
    } else {
        echo 'ukendt medlem';             
    }
} else {
    console_log( "user error");
    die ($rodb->error);
}
if ($is_public) {
     echo "<h1>Roside for $name ($member)</h2>";

     if ($show_status) {
         echo "<h2>Mine sidste 10 DSR aktiviteter (roture og gymnastik)</h2>";
         
         echo "Her kan man se om jeg er på vandet og holde øje med hvornår jeg går i land igen";
         
         $s="
  (SELECT Boat.id as boatid, 
      Boat.Name AS boat, 
      DATE_FORMAT(OutTime,'%Y-%m-%d %H:%i') as outtime, 
      DATE_FORMAT(ExpectedIn,'%Y-%m-%d %H:%i') as expectedintime, 
      DATE_FORMAT(InTime,'%Y-%m-%d %H:%i') as intime, 
      Trip.Destination as destination, 
      Trip.id as tripid, 
      TripType.Name AS triptype
      FROM Member,TripMember,TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.id = Trip.BoatID) ON TripType.id = Trip.TripTypeID 
      WHERE Trip.id=TripMember.TripID AND TripMember.member_id=Member.id AND Member.MemberId=?)
UNION
  (SELECT 'Gymnastik' as boatid,
      '' as boat,
      DATE_FORMAT(start_time,'%Y-%m-%d %H:%i') as outtime, 
      '' as expectedintime,
      '' as intime,
      '' as destination,
      0 as tripid,
      team as triptype
      FROM Member,team_participation
      WHERE team_participation.member_id=Member.id AND Member.MemberId=?)
  ORDER BY outtime DESC 
  LIMIT 10";
//         echo "SQL= $s";
         if ($stmt = $rodb->prepare($s)) {
             $stmt->bind_param("ss", $member,$member);
             $stmt->execute();
             $result= $stmt->get_result() or die("Error in user query: " . mysqli_error($rodb));
             echo "<table>";
             echo "<tr>
               <th>destination</th><th>Turtype</th><th>Båd</th><th>Ud</th><th>Ind</th>
              </tr>";
             while ($row = $result->fetch_assoc()) {
                 echo "<tr>";
                 echo "<td>". $row['destination'].  "</td>";
                 echo "<td>". $row['triptype'].  "</td>";
                 echo "<td>". $row['boat'].  "</td>";
                 echo "<td>". $row['outtime']."</td>";
                 if (empty($row['intime']) and !empty($row['boat'])) {
                     echo '<td class="onwater">'. $row['expectedintime'].  "</td>";
                     echo '<td class="onwater">På vandet</td>';
                 } else {
                     echo "<td>". $row['intime'].  "</td>";
                 }
                 echo "</tr>";
             }
             echo "</table>";
         } else {
             error_log("user p error $rodb->error");
             die("Error in user boat query: " . mysqli_error($rodb));
         }
     }
     
     echo "<h2>Mine næste tre aktiviteter i DSR</h2>";     
     echo "Kommer snart";

} else {
    echo "<br>Lukket side";
}

?>     
  </body>
</html>

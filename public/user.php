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

$is_public=false;

if ($stmt = $rodb->prepare("SELECT member_setting.is_public,member_setting.show_status, CONCAT(Member.FirstName,' ',Member.LastName) as name
                            FROM member_setting,Member WHERE member=Member.id AND Member.MemberId=?")) {
    $stmt->bind_param("s", $member);
    $stmt->execute() || dbErr($rodb,$res,"user stat exe");
    $result= $stmt->get_result() or die("Error in user query: " . mysqli_error($rodb));
    if ($row = $result->fetch_assoc()) {
        if ($row['is_public']==1) {
            $is_public=true;
            $name=$row['name'];
            $show_status=$row['show_status'];
        }
    } else {
        echo 'ukendt medlem<br>eller $member har ikke tilvalgt "offentlig profil"';
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
      CAST(Meter/1000 AS DECIMAL(10,2)) as km,
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
      '' as km,
      team as triptype
      FROM Member,team_participation
      WHERE team_participation.member_id=Member.id AND Member.MemberId=?)
  ORDER BY outtime DESC
  LIMIT 10";
//         echo "SQL= $s";
        $stmt = $rodb->prepare($s) or dbErr($roDb,$res,"user activities");
        $stmt->bind_param("ss", $member,$member);
        $stmt->execute();
         $result= $stmt->get_result() or die("Error in user query: " . mysqli_error($rodb));
         echo "<table>";
         echo "<tr>
               <th>destination</th><th>Turtype</th><th>Båd</th><th>Ud</th><th>Ind</th><th>km</th>
              </tr>";
         while ($row = $result->fetch_assoc()) {
             echo "<tr>";
             echo "<td>". $row['destination'].  "</td>";
             echo "<td>". $row['triptype'].  "</td>";
             echo "<td>". $row['boat'].  "</td>";
             echo "<td>". $row['outtime']."</td>";
             if (empty($row['intime']) and !empty($row['boat'])) {
                 echo '<td class="onwater">På vandet</td>';
             } else {
                 echo "<td>". $row['intime'].  "</td>";
             }
                 echo '<td class="nr">'. $row['km']."</td>";
                 echo "</tr>";
         }
             echo "</table>";
             // rank
             $ranksql="SELECT 1+COUNT('x')  as rank,rs.summer FROM (
    SELECT CAST(Sum(Meter) AS UNSIGNED) AS distance,Member.MemberID as id, Member.FirstName as firstname, Member.LastName as lastname,
    CAST(SUM(IF(season.summer_start<OutTime AND season.summer_end>OutTime,Meter,0)) AS UNSIGNED) AS summer
    FROM Member,season,BoatType,Trip,TripMember,Boat
    WHERE
      Trip.id = TripMember.TripID AND
      Member.id = TripMember.member_id AND
      Boat.id = Trip.BoatID AND
      BoatType.Name = Boat.boat_type AND
      season.season=Year(OutTime) AND
      ((Year(OutTime))=YEAR(NOW())) AND BoatType.Category=2
      GROUP BY Member.id,Member.MemberID, firstname, lastname
    ORDER BY summer desc
    ) as s,
     (SELECT CAST(SUM(IF(season.summer_start<OutTime AND season.summer_end>OutTime,Meter,0)) AS UNSIGNED) AS summer FROM season,BoatType,Trip,TripMember,Boat,Member
              WHERE
              Trip.id = TripMember.TripID AND
              Member.id = TripMember.member_id AND
              Member.MemberID = ? AND
              Boat.id = Trip.BoatID AND
              BoatType.Name = Boat.boat_type AND
              season.season=Year(OutTime) AND
              ((Year(OutTime))=YEAR(NOW())) AND BoatType.Category=2
) as rs
WHERE s.summer >rs.summer
";
             $rankstmt = $rodb->prepare($ranksql) or dbErr($roDb,$res,"user rankactivities");
             $rankstmt->bind_param("s", $member);
             $rankstmt->execute() || dbErr($rodb,$res,"user rant exe");
             $result= $rankstmt->get_result() or die("Error in user rank query: " . mysqli_error($rodb));
             if ($rankrow = $result->fetch_assoc()) {
                 echo "<h2>$name har roet ". number_format($rankrow["summer"]/1000,1). " km og er nummer ". $rankrow["rank"] ." i rostatistikken (sommer)</h2>";
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

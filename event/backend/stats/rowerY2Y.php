<?php
set_include_path(get_include_path().':..');
include("../inc/common.php");
$format=$_GET["output"] ?? "text";

$year=date('Y');
if (isset($_GET["year"])) {
    $year=(int)$_GET["year"];
}
$s="
SELECT Member.MemberID, CONCAT(FirstName,' ',LastName) as Navn,Sum(Meter)/1000 as km
  FROM Member,season s1,season s2,BoatType,Trip,TripMember,Boat
    WHERE
      Member.id=TripMember.member_id AND
      Trip.id=TripMember.TripID AND
      Boat.id=Trip.BoatID AND
      BoatType.Name=Boat.boat_type AND
      BoatType.boat_class !='motor' AND
      OutTime>s1.summer_end AND
      OutTime<s2.summer_end AND
      s1.season=?-1 AND
      s2.season=?
    GROUP BY Member.id,Member.FirstName,Member.LastName
    Order By km DESC
";
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"rowerY2Y $year");
$stmt->bind_param("ii",$year,$year);
$stmt->execute() ||  dbErr($rodb,$res,"rowerY2", "$q");
$result= $stmt->get_result();
process($result,$format,"std str ".($year-1)." til std str $year","_auto");
$stmt->close();
$rodb->close();

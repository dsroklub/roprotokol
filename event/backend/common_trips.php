<?php
include("inc/common.php");
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
    if (!empty($_GET["otherrower"])) {
        $otherrowerid=$_GET["otherrower"];
        if ($cuser==$otherrowerid) {
            echo "[]\n";
            exit(0);
        }
    } else {
        echo "please set rower";
        exit(0);
    }

    $s="SELECT JSON_OBJECT(
       'id',Trip.id,
       'comment',Trip.Comment,
       'triptype', TripType.Name,
       'boat',Boat.Name,
       'distance',Trip.Meter,
       'destination',Trip.Destination,
       'intime',DATE_FORMAT(Trip.InTime,'%Y-%m-%dT%T'),
       'outtime',DATE_FORMAT(Trip.OutTime,'%Y-%m-%dT%T'),
       'expectedintime', DATE_FORMAT(Trip.ExpectedIn,'%Y-%m-%dT%T'),
       'rowers',JSON_ARRAYAGG(JSON_OBJECT('member_id', Member.MemberID, 'name', CONCAT(Member.FirstName,' ',Member.LastName)) ORDER BY Seat)
) AS json
   FROM
       Trip, Boat, TripType, TripMember LEFT JOIN Member ON Member.id = TripMember.member_id
   WHERE
      Boat.id=Trip.BoatID AND Trip.id=TripMember.TripID AND TripType.id = Trip.TripTypeID  AND
      Trip.id IN (SELECT tm1.TripId FROM Member m1, Member m2, TripMember tm1, TripMember tm2 WHERE tm1.member_id=m1.id AND tm2.member_id=m2.id AND m1.MemberID=? AND m2.MemberID=? AND tm1.TripId=tm2.TripID)
   GROUP BY Trip.id
   ORDER BY Trip.id DESC
";
$stmt=$rodb->prepare($s) or dbErr($rodb,$res,"common trips");
$stmt->bind_param('ss', $cuser,$otherrowerid) or dbErr($rodb,$res,"common trips bind");
$stmt->execute()  or dbErr($rodb,$res,"common trips exe");
$result= $stmt->get_result() or dbErr($rodb,$res,"common trips res");
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ",\n";
	  echo $row['json'];
}
echo ']';
$rodb->close();
}

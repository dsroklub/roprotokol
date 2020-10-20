<?php
include("../../../rowing/backend/inc/common.php");
// include("inc/utils.php");
$format=$_GET["format"] ?? "json";
$season=date('Y',time());
if (isset($_GET["season"])) {
    $season=$_GET["season"];
}
$sql=
    "SELECT TripType.Name AS triptype, Count(Trip.id) AS trip_count, Sum(Meter) AS distance, Sum(Meter)/Count(Trip.id) as average
     FROM Trip, TripMember,TripType,Member
     WHERE
       Member.id=TripMember.member_id AND TripMember.TripID=Trip.id AND Trip.TripTypeID=TripType.id AND (? OR YEAR(Trip.OutTime)=?) AND Member.MemberID=?
     GROUP BY TripType.Name
     ORDER BY distance DESC";
# echo $sql;
$stmt = $rodb->prepare($sql) or dbErr($rodb,$res,"rowertrips agg");
$qseason=(($season=="all")?"0":$season);
$useseason=($season=="all")?1:0;
$stmt->bind_param("iss", $useseason,$qseason,$cuser);
$stmt->execute() or dbErr($rodb,$res,"rowertrips exe");;
$result= $stmt->get_result() or dbErr($rodb,$res,"stat query: ");
process($result,$format,"Sæson $season $cuser","_auto");
$rodb->close();

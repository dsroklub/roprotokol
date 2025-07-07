<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
$limit = (int) ($_GET["limit"] ?? 10);
if ($limit < 0) {
    $limit = 0;
} elseif ($limit > 150) {
    $limit = 150;
}
$s='SELECT Concat(Member.FirstName," ",Member.LastName) as roer,Member.MemberID as medlemsnummer,COUNT(distinct tmo.member_id) as rokammerater
FROM Member, Trip,TripMember tm, TripMember tmo
WHERE tm.TripID=Trip.id AND tm.member_id=Member.id AND tmo.TripID=Trip.id AND Member.id!=tmo.member_id
AND YEAR(Trip.OutTime)=YEAR(NOW())
GROUP By Member.id
ORDER BY rokammerater DESC
LIMIT ?';
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"maxmates");
$stmt->bind_param("i",$limit);
$stmt->execute() || dbErr($rodb,$res,"maxmates Exe");
$result= $stmt->get_result();
process($result,"xlsx","maxmates","_auto");

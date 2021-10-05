<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
$forum=$_GET["q"] ?? null;

$s="SELECT t1.FirstName as r1,t2.FirstName as r2,t3.FirstName as r3
FROM Member t1,Member t2, Member t3, forum_subscription f1,forum_subscription f2,forum_subscription f3
WHERE
 t1.id=f1.member AND t2.id=f2.member AND t3.id=f3.member AND
 f1.forum=? AND f2.forum=f1.forum AND f3.forum=f1.forum AND
 1*t2.MemberID<1*t3.MemberID AND
 1*t1.MemberID<1*t2.MemberID AND
 NOT EXISTS (SELECT 'x' FROM TripMember tm1,TripMember tm2 WHERE tm1.member_id=t1.id AND tm2.member_id=t2.id AND tm1.TripID=tm2.TripID) AND
 NOT EXISTS (SELECT 'x' FROM TripMember tm1,TripMember tm2 WHERE tm1.member_id=t3.id AND tm2.member_id=t2.id AND tm1.TripID=tm2.TripID) AND
 NOT EXISTS (SELECT 'x' FROM TripMember tm1,TripMember tm2 WHERE tm1.member_id=t1.id AND tm2.member_id=t3.id AND tm1.TripID=tm2.TripID) AND
 EXISTS (SELECT 'x' FROM MemberRights ml WHERE MemberRight='longdistance' AND ml.member_id=t1.id OR ml.member_id=t2.id OR ml.member_id=t3.id)
";
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"set new rowers prep ");
$stmt->bind_param('s',$forum) || dbErr($rodb,$res,"set new rowers (bind)");
$stmt->execute() or dbErr($rodb,$res,"set new boat ");
$result=$stmt->get_result() or dbErr($rodb,$res,"set new rowers");

//$output='xlsx';
$output='json';
process($result,$output,"instruktørstatistik","_auto");

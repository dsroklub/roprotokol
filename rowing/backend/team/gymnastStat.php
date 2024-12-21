<?php
include("../inc/common.php");
include("../inc/utils.php");

if (! isset($_GET["memberid"])) {
    dbErr($rodb,$res,"missing memberid arg");
}

$mid=$_GET["memberid"];

$s='
SELECT team,MemberId, count("x") AS n
  FROM team_participation,Member
  WHERE Member.id=member_id AND YEAR(start_time)=YEAR(NOW()) AND MemberID=? GROUP BY team ORDER BY n DESC';

error_log($s);
$stmt=$rodb->prepare($s) or dbErr($rodb,$res,"gymnastStat prep");
$stmt->bind_param("s", $mid) || dbErr($rodb,$res,"gymns mid bind");
$stmt->execute() || dbErr($rodb,$res,"gyms exe");
$gymnastresult=$stmt->get_result() or dbErr($rodb,$res,"Error in gymns query: ");

process($gymnastresult);

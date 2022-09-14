<?php
if (isset($_GET["quarter"])) {
    $quarter=$_GET["quarter"];
} else {
    echo "please set quarter";
    exit(1);
}
$format="csv";
if (isset($_GET["format"]) && $_GET["format"]=="xlsx") {
    $format="xlsx";
}

set_include_path(get_include_path().':..');
include("../inc/common.php");
//header("Pragma: no-cache");

$s=
 'SELECT team.name AS Hold, team_participation.dayofweek as ugedag,team_participation.timeofday as holdstarttid,classdate as holddato,team.description holdbeskrivelse,
    CONCAT(FirstName," ",LastName) AS membername, Member.MemberID as Medlemsnr,KommuneKode,CprNo
  FROM Member, team_participation
  LEFT JOIN team on team_participation.team=team.name
        AND team_participation.dayofweek=team.dayofweek
        AND team_participation.timeofday=team.timeofday
  WHERE  Member.id=team_participation.member_id  AND
        QUARTER(classdate)=? AND
        ((YEAR(classdate)=YEAR(NOW()) AND QUARTER(NOW())>=?) OR (YEAR(classdate)=YEAR(NOW())-1 AND QUARTER(NOW())<?))
ORDER BY team.name,team_participation.timeofday
  ';
//echo $s;
$stmt=$rodb->prepare($s) or dbErr($rodb,$res,"prep");
$stmt->bind_param("iii", $quarter,$quarter,$quarter) || dbErr($rodb,$res,"bind");
$stmt->execute() || dbErr($rodb,$res,"exe");
$result=$stmt->get_result() or dbErr($rodb,$res,"Error in quarterly query: ");
process($result,$format,"gymnastikQ$quarter","_auto");

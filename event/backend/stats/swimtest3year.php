<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
include("inc/utils.php");
$vr=verify_right(["admin"=>null,"data"=>"stat"]);

$s="SELECT CONCAT(FirstName,' ',LastName) as roer,MemberId as medlemsnummer, IFNULL(MAX(DATE(MemberRights.Acquired)),'') as svømmeprøve,
IF(DATEDIFF(DATE(NOW()),MAX(DATE(MemberRights.Acquired)))<3*365,'OK','-') AS 'indenfor 3 år'
FROM Member JOIN MemberRights on  MemberRights.member_id=Member.id AND MemberRights.MemberRight='longdistance_swim'
WHERE
  RemoveDate IS NULL
Group By Member.id
ORDER BY FirstName,LastName
";
$result=$rodb->query($s) or dbErr($rodb,$res,"Error in swim300 query: " );
$output='xlsx';
process($result,$output,"langturs svømmeprøver","_auto");

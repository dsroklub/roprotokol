<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");

$s="SELECT CONCAT(FirstName,' ',LastName) as instruktør,MemberId as medlemsnummer, IFNULL(MAX(DATE(MemberRights.Acquired)),'') as entringsøvelse,
IF(DATEDIFF(DATE(NOW()),MAX(DATE(MemberRights.Acquired)))<3*365,'OK','-') AS 'indenfor 3 år'
FROM Member LEFT JOIN MemberRights on  MemberRights.member_id=Member.id AND MemberRights.MemberRight='entringsøvelse'
WHERE
  Member.id IN (SELECT member_id FROM MemberRights mr WHERE mr.MemberRight='instructor' AND mr.argument='row') AND
  RemoveDate IS NULL
Group By Member.id
ORDER BY FirstName,LastName
";
$result=$rodb->query($s) or dbErr($rodb,$res,"Error in instruktorstat query: " );
$output='xlsx';
process($result,$output,"inrig instr. entringsøvelser","_auto");

<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");
$now = getdate();
$year = isset($_GET["year"]) ? (int) $_GET["year"] : $now['year'];
$s="SELECT Concat(Member.FirstName,' ',Member.LastName) as roer, Member.MemberID as medlemNr,showname as rettighed,argument as ekstra,Acquired as tildelt,Concat(m.FirstName,' ',m.LastName) as tildeler
    FROM Member,MemberRightType,MemberRights LEFT JOIN Member m ON m.id=MemberRights.created_by
    WHERE
       member_id=Member.id AND YEAR(Acquired)=" . $year . " AND MemberRightType.member_right=MemberRights.MemberRight AND NOT (MemberRightType.arg <> MemberRights.argument)
    ORDER BY MemberRight,tildelt,roer";

$result=$rodb->query($s) or dbErr($rodb,$res,"q");
process($result,"xlsx","årets_nye_rettigheder","_auto");

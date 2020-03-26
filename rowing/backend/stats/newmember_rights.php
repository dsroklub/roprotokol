<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");
header('Content-type: text/csv');
header('Content-Disposition: filename="nyeRettigheder.csv"');
$s='
SELECT Concat(Member.FirstName," ",Member.LastName) as navn,Member.MemberID as medlemsNr,Member.JoinDate as indmeldt,showname as rettighed,argument as extra,Acquired as tildelt,Concat(m.FirstName," ",m.LastName) as tildeler
    FROM Member,MemberRightType,MemberRights LEFT JOIN Member m ON m.id=MemberRights.created_by
    WHERE member_id=Member.id AND MemberRightType.member_right=MemberRights.MemberRight AND NOT (MemberRightType.arg <> MemberRights.argument) AND
      (YEAR(Member.JoinDate) = YEAR(NOW()) OR (YEAR(Member.JoinDate) = YEAR(NOW())-1 AND MONTH(Member.JoinDate)>9))
    ORDER BY navn,MemberRight,tildelt
';

$result=$rodb->query($s) or die("Error in ld query: " . mysqli_error($rodb));
process($result,"xlsx","nye_roeres_rettigheder","_auto");

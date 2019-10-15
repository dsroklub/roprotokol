<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");
header('Content-type: text/csv');
header('Content-Disposition: filename="nyeRettigheder.csv"');
$s='
SELECT Concat(Member.FirstName," ",Member.LastName) as name, Member.MemberID,showname,argument,Acquired,Concat(m.FirstName," ",m.LastName) as tildeler
    FROM Member,MemberRightType,MemberRights LEFT JOIN Member m ON m.id=MemberRights.created_by
    WHERE member_id=Member.id AND YEAR(Acquired)=YEAR(NOW()) AND MemberRightType.member_right=MemberRights.MemberRight AND NOT (MemberRightType.arg <> MemberRights.argument) 
    ORDER BY MemberRight,Acquired,name
';

$result=$rodb->query($s) or die("Error in ld query: " . mysqli_error($rodb));
process($result,"csv","årets_nye_rettigheder",["navn","medlemsnnummer","rettighed","ekstra","tildelt","tildeler"]);

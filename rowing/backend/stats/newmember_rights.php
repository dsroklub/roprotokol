<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");
header('Content-type: text/csv');
header('Content-Disposition: filename="nyeRettigheder.csv"');
$s='
SELECT Concat(Member.FirstName," ",Member.LastName) as name,Member.MemberID,Member.JoinDate,right_name.showname,argument,Acquired,Concat(m.FirstName," ",m.LastName) as tildeler
    FROM Member,right_name,MemberRights LEFT JOIN Member m ON m.id=MemberRights.created_by
    WHERE member_id=Member.id AND right_name.member_right=MemberRights.MemberRight AND
      (YEAR(Member.JoinDate) = YEAR(NOW()) OR (YEAR(Member.JoinDate) = YEAR(NOW())-1 AND MONTH(Member.JoinDate)>9))
    ORDER BY name,MemberRight,Acquired
';

$result=$rodb->query($s) or die("Error in ld query: " . mysqli_error($rodb));
process($result,"csv","roere",["navn","medlemsnnummer","indmeldt","rettighed","extra","tildelt","tildeler"]);

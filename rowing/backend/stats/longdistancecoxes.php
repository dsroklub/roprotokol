<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
include("inc/backheader.php");
header('Content-type: text/csv');
header('Content-Disposition: filename="langtursstyrmaend.csv"');
$s='SELECT Concat(FirstName," ",LastName) as roer, MemberID as medlemNr,MemberRight as rettighed, Acquired as tildelt
    FROM Member,MemberRights
    WHERE
       MemberRights.member_id=Member.id AND (MemberRight="longdistance" OR MemberRight="longdistancetheory") AND RemoveDate IS NULL
    ORDER BY MemberRight,roer';

$result=$rodb->query($s) or dbErr($rodb,$res,"langtursstyrmaend");
process($result,"xlsx","langtursstyrmænd","_auto");

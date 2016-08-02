<?php
set_include_path(get_include_path().':..');
include("inc/common.php");
header('Content-type: text/plain; charset=utf-8');
header('Content-type: text/csv');
header('Content-Disposition: filename="member_rights.csv"');

$cols=array();

$sc="SELECT DISTINCT CONCAT(MemberRight,' ',argument) as mr from MemberRights ORDER BY mr";

$result=$rodb->query($sc) or die("Error in column query: " . mysqli_error($rodb));;
 while ($row = $result->fetch_assoc()) {
     array_push($cols,$row["mr"]);
 }

$s='SELECT Concat(FirstName," ",LastName) as name, MemberID,CONCAT (MemberRight," ",argument) as mr, Acquired
    From Member,MemberRights 
    WHERE member_id=Member.id
    ORDER BY member_id,mr';

if ($sqldebug) {
 echo $s;
 echo "\n\n";
}

$currentid=-1;
$rower="";


echo "Navn,medlemsnummer,".join(",",$cols)."\n";
$result=$rodb->query($s) or die("Error in ld query: " . mysqli_error($rodb));;
 while ($row = $result->fetch_assoc()) {
     $mid=$row["MemberID"];
     if ($mid != $currentid) {
         if ($currentid>0) {
             echo "\n".$rower.",".join(",",$rr);
         }

         foreach ($cols as $cl) {
             $rr[$cl]="  ";
         }         
         $rower=$row["name"].",".$row["MemberID"];
         $currentid=$mid;

     }
     $rr[$row["mr"]]=$row["Acquired"];
 }
?> 

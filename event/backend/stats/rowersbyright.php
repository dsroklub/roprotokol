<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
assert(isset($_GET["right"]));
$right=$_GET["right"];
$subtype=$_GET["subtype"]??"";
$s =empty($subtype)?
   "SELECT CONCAT(Member.FirstName,' ',Member.LastName) AS navn, MemberId as medlemsnummer, Acquired as tildelt, argument as subtype
    FROM Member, MemberRights
    WHERE Member.id=MemberRights.member_id AND RemoveDate IS NULL AND MemberRight=? "
   :
   "SELECT CONCAT(Member.FirstName,' ',Member.LastName) AS navn, MemberId as medlemsnummer, Acquired as tildelt
    FROM Member, MemberRights
    WHERE Member.id=MemberRights.member_id AND RemoveDate IS NULL AND MemberRight=? AND argument=?"
   ;
if ($sqldebug) {
    echo $s;
}
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"RBR");
if (empty($subtype)) {
    $stmt->bind_param("s",$right);
} else {
    $stmt->bind_param("ss",$right,$subtype);
}
$stmt->execute() || dbErr($rodb,$res,"RBR exe");
$result= $stmt->get_result();
$output='xlsx';
if ($_GET["format"]=="json") $output='json';
if ($_GET["format"]=="csv") $output='csv';
process($result,$output,"Ret_$right$subtype","_auto");
$stmt->close();
$rodb->close();

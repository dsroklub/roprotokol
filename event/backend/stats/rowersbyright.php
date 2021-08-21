<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
include("inc/utils.php");
$vr=verify_right("admin");

assert(isset($_GET["right"]));
$right=$_GET["right"];
$subtype=$_GET["subtype"]??"";

$s =empty($subtype)?
   "SELECT CONCAT(Member.FirstName,' ',Member.LastName) AS navn, Acquired as tildelt, argument as subtype
    FROM Member, MemberRights
    WHERE Member.id=MemberRights.member_id AND RemoveDate IS NULL AND MemberRight=? "
   :
   "SELECT CONCAT(Member.FirstName,' ',Member.LastName) AS navn, Acquired as tildelt
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
process($result,"xlsx","Ret_$right$subtype","_auto");
$stmt->close();
$rodb->close();

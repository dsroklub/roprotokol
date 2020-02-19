<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

assert(isset($_GET["right"]));
$right=$_GET["right"];
$subtype=$_GET["subtype"]??"";

$s =empty($subtype)?
   "SELECT CONCAT(Member.FirstName,' ',Member.LastName) AS rower, Acquired as tildelt, argument as subtype
    FROM Member, MemberRights
    WHERE Member.id=MemberRights.member_id AND RemoveDate IS NULL AND MemberRight=? "
   :
   "SELECT CONCAT(Member.FirstName,' ',Member.LastName) AS rower, Acquired as tildelt
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
process($result,"csv","Ret_$right$subtype",array("$right","pr"));
$stmt->close();
$rodb->close();

<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

assert(isset($_GET["right"]) && isset($_GET["subtype"]));
$right=$_GET["right"];
$subtype=$_GET["subtype"];

$s="SELECT CONCAT(Member.FirstName,' ',Member.LastName) AS rower, Acquired as tildelt
    FROM Member, MemberRights
    WHERE Member.id=MemberRights.member_id and MemberRight=? AND argument=?";

if ($sqldebug) {
    echo $s;
}

if ($stmt = $rodb->prepare($s)) {
    error_log("RBR 4 r=$right, s=$subtype");
    $stmt->bind_param("ss",$right,$subtype);
     $stmt->execute();
     $result= $stmt->get_result();
     process($result,"csv","roere",array("roer","pr"));
     $stmt->close();
} else {
    error_log($rodb->error);
}
$rodb->close();

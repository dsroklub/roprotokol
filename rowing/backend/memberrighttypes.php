<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');
$s="SELECT member_right,arg,description,showname,predicate,validity From  MemberRightType ORDER by description,arg";
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"member rt bind");
$stmt->execute() || dbErr($rodb,$res,"member rt exe");
$result= $stmt->get_result() or dbErr($rodb,$res,"member rt res");
process($result);
$rodb->close();

<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");

$q="none";
if (isset($_GET["q"])) {
    $q=$_GET["q"];
}

$seasonclause="YEAR(start_time)=YEAR(NOW()) OR (YEAR(start_time)=YEAR(NOW())-1 AND MONTH(start_time)>6)";
switch ($q) {
case "all":
    $s="SELECT CONCAT(FirstName,' ',Lastname) as name,MemberID AS medlemsnummer,DATE_FORMAT(start_time,'%Y-%m-%d') AS start_time,hours AS timer, work AS arbejde FROM worklog,Member WHERE Member.id=worklog.member_id ORDER BY name,start_time";
    break;
case "day":
    $s="SELECT DATE_FORMAT(start_time,'%Y-%m-%d') as dag,SUM(hours) AS timer, work AS arbejde FROM worklog WHERE $seasonclause GROUP by dag";
    break;
case "boat":
    $s="SELECT boat as båd,SUM(hours) as timer, work AS arbejde FROM worklog WHERE $seasonclause GROUP by boat";
    break;
default:
    $res=["status" => "error", "error"=>"invalid query' .$q"];
    echo json_encode($res);
    exit(0);
}

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"mystats $q");
$stmt->bind_param("s",$cuser);
$stmt->execute() ||  dbErr($rodb,$res,"mystats Exe $q");
$result= $stmt->get_result();
//output_rows($result);
process($result,"csv",$q,"_auto");
$stmt->close();
$rodb->close();

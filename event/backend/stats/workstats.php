<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");

$q="none";
if (isset($_GET["q"])) {
    $q=$_GET["q"];
}

$captions="_auto";
$seasonclause="YEAR(start_time)=YEAR(NOW()) OR (YEAR(start_time)=YEAR(NOW())-1 AND MONTH(start_time)>6)";
switch ($q) {
case "all":
    $s="SELECT CONCAT(FirstName,' ',Lastname) as name,MemberID AS medlemsnummer,DATE_FORMAT(start_time,'%Y-%m-%d') AS start_time,hours AS timer, work AS arbejde FROM worklog,Member WHERE Member.id=worklog.member_id ORDER BY name,start_time";
    break;
case "day":
    $s="SELECT DATE_FORMAT(start_time,'%Y-%m-%d') as dag, SUM(hours) AS timer FROM worklog WHERE $seasonclause GROUP by dag";
    break;
case "boat":
    $s="SELECT boat as båd,SUM(hours) as timer FROM worklog WHERE $seasonclause GROUP by boat";
    break;
case "resterende":
    $s="
SELECT CONCAT(Member.FirstName,' ',Member.LastName) as roer,Member.MemberId as medlemsnummer,requirement as krævet,h as lagt, requirement-h as mangler 
FROM Member,worker,(SELECT member_id,SUM(hours) as h from worklog GROUP BY worklog.member_id) as w
    WHERE Member.id=w.member_id AND worker.member_id=Member.id;
";
    break;
case "overview":
    $captions=["","timer"];
    $s="
SELECT 'total',SUM(requirement) AS 'timer' FROM worker WHERE assigner='vedligehold' UNION
      SELECT 'udført',SUM(hours) as 'timer'  FROM worklog UNION
      SELECT 'resterende',SUM(GREATEST(0,requirement-h)) as tilbage  FROM worker,(SELECT member_id,SUM(hours) as h from worklog GROUP BY worklog.member_id) as w  WHERE worker.member_id=w.member_id UNION
      SELECT 'overskud',SUM(GREATEST(0,h-requirement)) as tilbage  FROM worker,(SELECT member_id,SUM(hours) as h from worklog GROUP BY worklog.member_id) as w  WHERE worker.member_id=w.member_id 
      "
;
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
process($result,"csv",$q,$captions);
$stmt->close();
$rodb->close();

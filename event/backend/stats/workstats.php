<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
require_once("utils.php");
verify_right("admin","vedligehold");

$q=$_GET["q"] ?? "none";
$format=$_GET["format"] ?? "json";

$captions="_auto";
$seasonclause="YEAR(start_time)=YEAR(NOW()) OR (YEAR(start_time)=YEAR(NOW())-1 AND MONTH(start_time)>6)";
$report_name="Rapport";
switch ($q) {
case "all":
    $report_name="alt arbejde";
    $s="SELECT CONCAT(FirstName,' ',Lastname) as name,MemberID AS medlemsnummer,DATE_FORMAT(start_time,'%Y-%m-%d %H:%i') AS fra, DATE_FORMAT(end_time,'%H:%i') AS til, hours AS timer, work AS arbejde FROM worklog,Member WHERE Member.id=worklog.member_id ORDER BY name,start_time";
    break;
case "day":
    $report_name="arbejde per dag";
    $s="SELECT DATE_FORMAT(start_time,'%Y-%m-%d') as dag, ROUND(SUM(hours),1) AS timer FROM worklog WHERE $seasonclause GROUP by dag ORDER BY dag";
    break;
case "boat":
    $report_name="arbejde per båd";
    $s="SELECT boat as båd,ROUND(SUM(hours),1) as timer FROM worklog WHERE $seasonclause GROUP by boat ORDER BY boat";
    break;
case "resterende":
    $report_name="resterende arbejde";
    $s="
SELECT CONCAT(Member.FirstName,' ',Member.LastName) as roer,workertype as bådtype,Member.MemberId as medlemsnummer,requirement as krævet,ROUND(h,1) as lagt, ROUND(requirement-h,1) as mangler 
FROM Member,worker,(SELECT member_id,SUM(hours) as h from worklog GROUP BY worklog.member_id) as w
    WHERE Member.id=w.member_id AND worker.member_id=Member.id;
";
    break;
case "overview":
    $report_name="oversig over arbejde";
    $captions=["","timer"];
    $s="
SELECT 'total',SUM(requirement) AS 'timer' FROM worker WHERE assigner='vedligehold' UNION
      SELECT 'udført',SUM(hours) as 'timer'  FROM worklog UNION
SELECT 'resterende',ROUND(SUM(GREATEST(0,requirement-IFNULL(h,0))),1) as tilbage  FROM worker LEFT JOIN (SELECT member_id,SUM(hours) as h from worklog GROUP BY worklog.member_id) as w ON worker.member_id=w.member_id UNION
      SELECT 'overskud',ROUND(SUM(GREATEST(0.0,h-requirement)),1) as tilbage  FROM worker,(SELECT member_id,SUM(hours) as h from worklog GROUP BY worklog.member_id) as w  WHERE worker.member_id=w.member_id 
      "
;
    break;

default:
    $res=["status" => "error", "error"=>"invalid query: " . $q];
    echo json_encode($res);
    exit(0);
}

error_log("Q $s");
$result = $rodb->query($s) or dbErr($rodb,$res,"workstats $q");
switch ($format) {
case "json":
    process($result,"csv",$q,$captions);
    break;
case "html":
    process($result,"text",$q,$captions);
    break;
case "tablejson":
    process($result,"tablejson",$report_name,$captions);
    break;
default:
    $res=["status" => "error", "error"=>"invalid format: " . $format];
    echo json_encode($res);
    exit(0);
}
$rodb->close();

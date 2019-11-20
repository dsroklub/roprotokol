<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
require_once("utils.php");
$q=$_GET["q"] ?? "none";
$format=$_GET["format"] ?? "csv";

$captions="_auto";
$seasonclause="YEAR(start_time)=YEAR(NOW()) OR (YEAR(start_time)=YEAR(NOW())-1 AND MONTH(start_time)>6)";
$report_name="Rapport";
switch ($q) {
case "rank":
    $report_name="overskudsroere";
    $s="
SELECT CONCAT(Member.FirstName,' ',Member.LastName) as roer,workertype as bådtype,Member.MemberId as medlNr,requirement as krævet,ROUND(h,1) as lagt, ROUND(h-requirement,1) as overskud, IF((requirement-h)<0,'***','') as status
FROM Member,worker,(SELECT member_id,IFNULL(SUM(hours),0) as h from worklog GROUP BY worklog.member_id) as w
    WHERE Member.id=w.member_id AND worker.member_id=Member.id AND requirement>0 HAVING overskud>0 ORDER by overskud DESC
";
    break;
default:
    $res=["status" => "error", "error"=>"invalid query: " . $q];
    echo json_encode($res);
    exit(0);
}

$result = $rodb->query($s) or dbErr($rodb,$res,"workstats $q");

if ($sumq){
    $sumresult = $rodb->query($sumq) or dbErr($rodb,$res,"workstats sum $q");
    $sum=$sumresult->fetch_assoc()["sum"];
    error_log("Q SUM $sum");
}

switch ($format) {
case "csv":
    process($result,"csv",$q,$captions);
    break;
case "json":
    process($result,"json",$q,$captions);
    break;
case "html":
    process($result,"text",$q,$captions);
    break;
case "tablejson":
    process($result,"tablejson",$report_name . "  ". $sum??"",$captions);
    break;
default:
    $res=["status" => "error", "error"=>"invalid format: " . $format];
    echo json_encode($res);
    exit(0);
}
$rodb->close();

<?php
include("../inc/common.php");
require_once("../inc/utils.php");
$vr=verify_right(["admin"=>["vedligehold"],"data"=>["stat"]]);
$sumq=null;
$sum=null;
$q=$_GET["q"] ?? "none";
$a=$_GET["a"] ?? "";
$format=$_GET["format"] ?? "csv";
$namesuf="";


if (isset($_GET["last_year"])) {
    $workyear--;
    $workseason =" ((YEAR(start_time)=$workyear AND MONTH(start_time)<10) OR (YEAR(start_time)=$workyear-1 AND MONTH(start_time)>9)) ";
}

$namesuf=($workyear-1) . "-" . ($workyear);

$captions="_auto";
$report_name="Rapport $q";
switch ($q) {
case "all":
    $report_name="alt arbejde $namesuf";
    $s="SELECT CONCAT(FirstName,' ',Lastname) as name,MemberID AS medlemsnummer,DATE_FORMAT(start_time,'%Y-%m-%d %H:%i') AS fra, DATE_FORMAT(end_time,'%H:%i') AS til, hours AS timer, work AS arbejde
 FROM worklog,Member WHERE Member.id=worklog.member_id AND $workseason
 ORDER BY name,fra";
    $sumq="SELECT SUM(hours) as sum FROM worklog,Member WHERE Member.id=worklog.member_id AND $workseason";
    break;
case "day":
    $report_name="arbejde per dag $namesuf";
    $s="SELECT DATE_FORMAT(start_time,'%Y-%m-%d') as dag, ROUND(SUM(hours),1) AS timer FROM worklog WHERE $workseason GROUP by dag ORDER BY dag";
    break;
case "boat":
    $report_name="arbejde per båd $namesuf";
    $s="SELECT boat as båd,ROUND(SUM(hours),1) as timer FROM worklog WHERE $workseason GROUP by boat ORDER BY boat";
    break;
case "weeks":
    $report_name="ugefordeling $namesuf";
    $s="SELECT WEEK(start_time) as uge, SUM(hours) as timer, GROUP_CONCAT(DISTINCT boat)  as både FROM worklog WHERE $workseason GROUP BY uge ORDER BY uge";
    break;
case "nonstarters":
    $report_name="ikke startet arbejde $namesuf";
    $f=$_GET["a"] ?? null;
    $workertypeC="";
    if ($f) {
        $workertype=$f? "'".mysqli_real_escape_string($rodb,$f)."'" :"workertype";
        $workertypeC=" AND workertype=$workertype";
    }
    $s="
SELECT DISTINCT CONCAT(Member.FirstName,' ',Member.LastName) as roer,workertype as vedligeholdstype,Member.MemberId as medlemsnummer,requirement as krævet,ROUND(IFNULL(h,0),1) as lagt, ROUND(requirement-IFNULL(h,0),1) as mangler
FROM Member,worker LEFT JOIN (SELECT member_id,SUM(hours) as h from worklog WHERE $workseason GROUP BY worklog.member_id) as w ON worker.member_id=w.member_id
   WHERE Member.RemoveDate IS NULL AND worker.member_id=Member.id AND worker.season=$workyear ${workertypeC}
   ORDER BY LAGT ASC,mangler DESC,workertype;
";
    //    echo "f=$f, $s\n";
    break;
case "lesswork":
    $pct=1.0*($_GET["a"] ?? 50);
    $s="
SELECT CONCAT(Member.FirstName,' ',Member.LastName) as roer,Email as email,workertype as bådtype,Member.MemberId as medlemsnummer,requirement as krævet,ROUND(IFNULL(h,0),1) as lagt, ROUND(requirement-IFNULL(h,0),1) as mangler,100*IFNULL(h,0)/requirement AS pct
FROM Member,worker LEFT JOIN (SELECT member_id,SUM(hours) as h from worklog WHERE $workseason GROUP BY worklog.member_id) as w ON worker.member_id=w.member_id
    WHERE  worker.member_id=Member.id AND worker.season=$workyear AND
  100*IFNULL(h,0)/requirement < $pct
ORDER BY lagt DESC;
";
    break;

case "nonmembers":
    $report_name="Udmeldte";
    $f=$_GET["a"] ?? null;
    $workertypeC="";
    if ($f) {
        $workertype=$f? "'".mysqli_real_escape_string($rodb,$f)."'" :"workertype";
        $workertypeC=" AND workertype=$workertype";
    }
    $s="
SELECT CONCAT(Member.FirstName,' ',Member.LastName) as roer,DATE_FORMAT(RemoveDate,'%d/%m %Y') as udmeldt,workertype as bådtype,Member.MemberId as medlemsnummer,requirement as krævet,ROUND(IFNULL(h,0),1) as lagt, ROUND(requirement-IFNULL(h,0),1) as mangler
FROM Member,worker LEFT JOIN
(SELECT member_id,SUM(hours) as h from worklog WHERE $workseason GROUP BY worklog.member_id) as w ON worker.member_id=w.member_id
    WHERE Member.RemoveDate IS NOT NULL AND worker.member_id=Member.id AND worker.season=$workyear ${workertypeC} ORDER BY udmeldt, roer,lagt ASC,mangler DESC,workertype;
";
    echo "f=$f, $s\n";
    break;
case "rank":
    $report_name="timer tilbage for roere $a";
    $limit="";
    if ($a) {
        $limit=" HAVING mangler>".intval($a)." ";
    }
    $s="
SELECT CONCAT(Member.FirstName,' ',Member.LastName) as roer,workertype as bådtype,Member.MemberId as medlemsnummer,requirement as krævet,ROUND(IFNULL(h,0),1) as lagt, ROUND(requirement-IFNULL(h,0),1) as mangler
FROM Member,worker LEFT JOIN (SELECT member_id,SUM(hours) as h from worklog WHERE $workseason GROUP BY worklog.member_id) as w ON worker.member_id=w.member_id
    WHERE worker.member_id=Member.id AND worker.season=$workyear $limit ORDER by mangler DESC;
";
    break;
case "resterende":
    $report_name="gjort arbejde $namesuf";
    $s="
SELECT CONCAT(Member.FirstName,' ',Member.LastName) as roer,Email as email,workertype as bådtype,Member.MemberId as medlemsnummer,requirement as krævet,ROUND(IFNULL(h,0),1) as lagt, ROUND(requirement-IFNULL(h,0),1) as mangler
    FROM Member,worker LEFT JOIN (SELECT member_id,SUM(hours) as h from worklog WHERE $workseason GROUP BY worklog.member_id) as w ON worker.member_id=w.member_id
    WHERE  worker.member_id=Member.id AND worker.season=$workyear
    ORDER BY lagt DESC;
";
    break;
case "overview":
    $report_name="oversigt over arbejde";
    $captions="_auto";
    $s="
SELECT 'total standerstrygning',SUM(requirement) AS 'timer' FROM worker WHERE worker.season=$workyear AND assigner='vedligehold' OR description='vintervedligehold'
  UNION
SELECT 'aktuel total',SUM(requirement) AS 'timer' FROM worker,Member WHERE description='vintervedligehold' AND Member.id = worker.member_id AND worker.season=$workyear AND Member.RemoveDate IS NULL
  UNION
SELECT 'udført',SUM(hours) as 'timer'  FROM worklog WHERE $workseason
  UNION
SELECT 'resterende aktuelt',ROUND(SUM(GREATEST(0,requirement-IFNULL(h,0))),1) as tilbage  FROM Member,worker LEFT JOIN (SELECT member_id,SUM(hours) as h from worklog WHERE $workseason GROUP BY worklog.member_id) as w ON worker.member_id=w.member_id
  WHERE Member.id=worker.member_id AND Member.RemoveDate IS NULL AND worker.season=$workyear
  UNION
SELECT 'overskud',ROUND(SUM(GREATEST(0.0,h-requirement)),1) as tilbage  FROM worker,(SELECT member_id,SUM(hours) as h from worklog WHERE $workseason GROUP BY worklog.member_id) as w  WHERE worker.member_id=w.member_id AND worker.season=$workyear
      "
;
    break;
default:
    $res=["status" => "error", "error"=>"invalid query: " . $q];
    echo json_encode($res);
    exit(0);
}

if (isset($_GET["sqldebug"])) {
    echo $s;
    exit(0);
  }

$result = $rodb->query($s) or dbErr($rodb,$res,"workstats $q");

if ($sumq){
    $sumresult = $rodb->query($sumq) or dbErr($rodb,$res,"workstats sum $q");
    $sum=$sumresult->fetch_assoc()["sum"];
}

switch ($format) {
    case "csv":
    case "json":
    case "ods":
    case "xlsx":
        process($result,$format,$report_name,$captions);
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

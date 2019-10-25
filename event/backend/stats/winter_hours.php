<?php
include("../../../rowing/backend/inc/common.php");
include("utils.php");


header('Content-type: text/csv;charset=utf-8');
header('Content-Disposition: filename="boatrowerkm.csv"');

$hours = $rodb->query("SELECT forum,CONCAT(FirstName,' ',Lastname) as name,DATE_FORMAT(start_time,'%Y-%m-%d') as start_time,hours,work FROM worklog,Member WHERE Member.id=worklog.member_id ORDER BY forum,name,start_time") or dbErr($rodb,$res,"vinter_hours Q");
process($hours,"csv","roerstatistik",["Hold","Navn","Dag","Timer","Gjort"]);

<?php
include("../../../rowing/backend/inc/common.php");
include("utils.php");


header('Content-type: text/csv;charset=utf-8');
header('Content-Disposition: filename="boatrowerkm.csv"');

$hours = $rodb->query("SELECT forum,CONCAT(FirstName,' ',Lastname) as name,DATE_FORMAT(workdate,'%Y-%m-%d'),hours,work FROM worklog,Member WHERE Member.id=worklog.member_id ORDER BY forum,name,workdate") or dbErr($rodb,$res,"vinter_hours Q");
    
process($hours,"csv","roerstatistik",["Hold","Navn","Dag","Timer","Gjort"]);

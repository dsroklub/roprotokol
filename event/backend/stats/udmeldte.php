<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
$s="
WITH qs (f,t,qt)
AS (VALUES (0,0,'<1 kv'),(1,1,'1 kv'),(2,2,'halvt år'),(3,3,'3 kv'),(4,4,'1 år'),(5,8,'2 år'),(9,12,'3 år'),(13,16,'4 år'),(17,20,'5 år'),(21,999,'stadig medlem'))
SELECT YEAR(JoinDate) indmeldt,qt 'kvartaler i DSR',COUNT('x') antal
FROM Member,qs
WHERE JoinDate IS NOT NULL AND YEAR(JoinDate) > 0 AND
IFNULL(TIMESTAMPDIFF(Quarter,JoinDate,RemoveDate),999) >= f AND
IFNULL(TIMESTAMPDIFF(Quarter,JoinDate,RemoveDate),999) <= t AND
YEAR(JoinDate)>2000
GROUP BY indmeldt,f
ORDER by indmeldt,f
";
$result=$rodb->query($s) or dbErr($rodb,$res,"Error in racerkaniner query: " );
$output='xlsx';
process($result,$output,"udmeldte","_auto");

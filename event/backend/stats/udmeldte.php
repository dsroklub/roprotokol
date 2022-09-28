<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>[null],"data"=>["stat"]]);
$s="
WITH qs (f,t,qt)
AS (VALUES
(0,0,'0-3 måneder'),
(1,1,'3-6 måneder'),
(2,3,'6-12 måneder'),
(4,5,'12-18 måneder'),
(6,7,'18 måneder - 2 år'),
(8,11,'2 år - 3 år'),
(12,15,'3 år - 4 år'),
(21,999,'stadig medlem'))
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

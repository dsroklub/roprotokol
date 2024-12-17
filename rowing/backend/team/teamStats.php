<?php
include("../inc/common.php");
include("../inc/utils.php");
header('Content-type: application/json;charset=utf-8');


$stats=$rodb->query("
  SELECT hold as jsonkey,JSON_ARRAYAGG(JSON_OBJECT('rank',CAST(rank AS UNSIGNED),'navn',navn,'gange',gange)) as json FROM
    (SELECT ROW_NUMBER() OVER ( ORDER BY gange DESC) AS rank,CONCAT(FirstName,' ',LastName) AS navn, COUNT('x') AS gange, 'alle' AS hold
      FROM team_participation,Member
      WHERE Member.id=team_participation.member_id AND YEAR(start_time)=YEAR(NOW())
      GROUP BY member_id
    UNION
      SELECT ROW_NUMBER() OVER (PARTITION BY team ORDER BY gange DESC) AS rank, CONCAT(FirstName,' ',LastName) AS navn, count('x') AS gange, team AS hold
      FROM team_participation,Member
      WHERE Member.id=team_participation.member_id AND YEAR(start_time)=YEAR(NOW())
      GROUP BY member_id,team
    ) AS si GROUP BY hold
");
process($stats,"rawjson","json",null,"rank");
$rodb->close();

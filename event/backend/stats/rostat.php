<?php
include("../inc/common.php");
include("../inc/utils.php");
$ranksql="
SELECT Member.id as member_id,CONCAT(Member.FirstName,' ',Member.LastName) as name,m.rank,m.year_rank,Member.Email as email,m.summer,m.distance,g.gymrank,g.gymcount,m.rowingtrips
FROM
Member LEFT JOIN
(SELECT
  member_id,
  ROW_NUMBER() OVER ( ORDER BY summer DESC) rank,
  ROW_NUMBER() OVER ( ORDER BY distance DESC) year_rank,
  distance,summer,rowingtrips
   FROM
   (SELECT
    COUNT('x') as rowingtrips,
    CAST(Sum(Meter) AS UNSIGNED) AS distance,
    CAST(SUM(IF(season.summer_start<OutTime AND season.summer_end>OutTime,Meter,0)) AS UNSIGNED) AS summer,
    rm.id as member_id, rm.MemberID
    FROM season,Trip,TripMember,Member rm
    WHERE
      rm.id = TripMember.member_id AND
      Trip.id = TripMember.TripID AND
      season.season=Year(OutTime) AND
      Year(OutTime)=YEAR(NOW())
     GROUP BY rm.MemberID
  )  as im
) as m ON m.member_id=Member.id LEFT JOIN
(
  SELECT COUNT('x') AS gymcount,gm.id as member_id,
  ROW_NUMBER() OVER ( ORDER BY gymcount DESC) gymrank
  FROM team_participation,Member gm
    WHERE  YEAR(start_time)=YEAR(NOW()) AND
       gm.id=team_participation.member_id
     GROUP BY gm.id
  ) as g ON g.member_id=Member.id,
    member_setting
WHERE member_setting.member=m.member_id AND member_setting.show_activities
ORDER by rank ASC";

$rostat=$rodb->query($ranksql) or dbErr($rodb,$res,"ro event stats");
process($rostat);


$rodb->close();

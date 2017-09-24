<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$s="
SELECT event.status,event.id as event_id,event.open,owner_member.MemberId AS owner, 
  CONCAT(owner_member.FirstName,' ',owner_member.LastName) as owner_name,
  event.name,event.destination, BoatCategory.Name as boat_category, 
  DATE_FORMAT(start_time,'%Y-%m-%dT%T') as start_time,DATE_FORMAT(end_time,'%Y-%m-%dT%T') as end_time, distance, 
  TripType.Name as trip_type, max_participants, location, comment,
  GROUP_CONCAT(CONCAT(em.FirstName,' ',em.LastName),':§§:',em.MemberId,':§§:', IFNULL(mc.iscox,0), ':§§:', event_member.role, ':§§:',DATE_FORMAT(event_member.enter_time,'%Y-%m-%dT%T') SEPARATOR '££') AS participants
  FROM 
    Member owner_member, 
     Member em ,
          event 
          LEFT JOIN BoatCategory on BoatCategory.id=event.boat_category LEFT JOIN TripType ON TripType.id=event.trip_type 
          LEFT JOIN event_member ON event_member.event=event.id 
             LEFT JOIN (SELECT member_id, 1 as iscox from MemberRights WHERE MemberRight='cox') as mc ON mc.member_id=event_member.member
   WHERE owner_member.id=event.owner AND event.end_time >= NOW()
      AND  em.id=event_member.member
      GROUP BY owner,start_time,event_id
";

$result=$rodb->query($s);

if ($result) {
    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
        if ($first) $first=0; else echo ',';	  
        $row['participants']=multifield_array($row['participants'],["name","member_id","is_cox","role","enter_time"]);
        echo json_encode($row,JSON_PRETTY_PRINT);
    }
    echo ']';
} else {
    dbErr($rodb,$res);
    echo json_encode($res,JSON_PRETTY_PRINT);
}

?> 

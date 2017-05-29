<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$s="
SELECT event.id as event_id,event.open,owner_member.MemberId AS owner, event.name, BoatCategory.Name as boat_category, 
      DATE_FORMAT(start_time,'%Y-%m-%dT%T') as start_time,DATE_FORMAT(end_time,'%Y-%m-%dT%T') as end_time, distance, 
      TripType.Name as trip_type, max_participants, location, comment,
       GROUP_CONCAT(CONCAT(em.FirstName,' ',em.LastName),':§§:',em.MemberId,':§§:', event_member.role, ':§§:',DATE_FORMAT(event_member.enter_time,'%Y-%m-%dT%T') SEPARATOR '££') AS participants
       FROM Member owner_member, event 
          LEFT JOIN BoatCategory on BoatCategory.id=event.boat_category LEFT JOIN TripType ON TripType.id=event.trip_type 
          LEFT JOIN event_member ON event_member.event=event.id LEFT JOIN Member em ON em.id=event_member.member
   WHERE owner_member.id=event.owner AND start_time >= NOW()
  GROUP BY owner,start_time,event_id
";

$result=$rodb->query($s);

if ($result) {
    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
        if ($first) $first=0; else echo ',';	  
        $row['participants']=multifield_array($row['participants'],["name","member_id","role","enter_time"]);
        echo json_encode($row,JSON_PRETTY_PRINT);
    }
    echo ']';
} else {
    dbErr($rodb,$res);
    echo json_encode($res,JSON_PRETTY_PRINT);
}

?> 

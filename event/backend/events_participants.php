<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
$s="SELECT JSON_MERGE(
    JSON_OBJECT(
     'boats',boats,
     'status',event.status,
     'event_id', event.id,
     'open',event.open,
     'event_category',event.category,
     'owner',owner_member.MemberId,
     'name',event.name,
     'destination',event.destination, 
     'start_time',DATE_FORMAT(start_time,'%Y-%m-%dT%T'),
     'end_time',DATE_FORMAT(end_time,'%Y-%m-%dT%T'),
     'boat_category',BoatCategory.Name,
     'trip_type',TripType.Name, 
     'max_participants',max_participants,
     'location',event.location,
     'comment',event.comment,
     'distance',distance,
     'owner_name',CONCAT(owner_member.FirstName,' ',owner_member.LastName)
),
   CONCAT(
    '{', JSON_QUOTE('fora'),': [',
       GROUP_CONCAT(JSON_OBJECT(
       'forum',event_forum.forum
       )
     ),
   ']}'),
   CONCAT(
    '{', JSON_QUOTE('participants'),': [',
       GROUP_CONCAT(JSON_OBJECT(
       'name',CONCAT(em.FirstName,' ',em.LastName),
       'member_id',em.MemberId,
       'is_cox', IFNULL(mc.iscox,0), 
       'is_long_cox',IFNULL(mlc.islongcox,0),
       'role', event_member.role,
       'enter_time',DATE_FORMAT(event_member.enter_time,'%Y-%m-%dT%T')
       )
     ),
   ']}')
   ) AS json
  FROM 
    Member owner_member, 
          event 
          LEFT JOIN BoatCategory on BoatCategory.id=event.boat_category 
          LEFT JOIN TripType ON TripType.id=event.trip_type 
          LEFT JOIN event_member ON event_member.event=event.id 
             LEFT JOIN (SELECT member_id, 1 as iscox from MemberRights WHERE MemberRight='cox') as mc ON mc.member_id=event_member.member
             LEFT JOIN (SELECT member_id, 1 as islongcox from MemberRights WHERE MemberRight='longdistance') as mlc ON mlc.member_id=event_member.member
       LEFT JOIN Member em ON em.id=event_member.member

          LEFT JOIN event_forum ON event_forum.event=event.id 

   WHERE owner_member.id=event.owner AND event.end_time >= NOW()
      GROUP BY owner,start_time,event.id
";

$result=$rodb->query($s);
if ($result) {
    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
        if ($first) $first=0; else echo ",\n";	  
  	    echo $row['json'];
    }
    echo ']';
} else {
    dbErr($rodb,$res);
    echo json_encode($res,JSON_PRETTY_PRINT);
}

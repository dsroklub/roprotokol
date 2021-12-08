<?php
include("inc/common.php");
include("inc/utils.php");
$s="SELECT JSON_OBJECT(
  'owner', Member.MemberId,
  'owner_name', CONCAT(Member.FirstName,' ',Member.LastName),
  'boat_category',BoatCategory.Name,
  'start_time', start_time,
  'end_time',end_time,
  'distance', distance,
  'destination',destination,
  'trip_type',TripType.Name,
  'max_participants',max_participants,
  'location', location,
  'category',category,
  'preferred_intensity',preferred_intensity,
  'comment',comment,
  'open',event.open,
  'status',event.status
   ) as json
    FROM Member, (event LEFT JOIN BoatCategory on BoatCategory.id=event.boat_category) LEFT JOIN TripType ON TripType.id=event.trip_type
    WHERE Member.id=event.owner AND start_time >= NOW()";
$result=$rodb->query($s) or dbErr($rodb,$res,"events");
output_json($result);

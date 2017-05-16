<?php
include("../rowing/backend/inc/common.php");
include("utils.php");
require_once '../phplib/vendor/autoload.php';
$secret=$config['secret'];
$token=$_GET['token'];
$user=$_GET['user'];
error_log("secret= $secret user=$user token=$token");

$s="
SELECT owner.MemberId AS owner, event.name, BoatCategory.Name as boat_category,start_time,end_time,
  distance, TripType.Name as trip_type, max_participants, location, lat, lon, category, preferred_intensity, comment
  FROM authentication, Member, Member owner,
    (event LEFT JOIN BoatCategory on BoatCategory.id=event.boat_category) 
    LEFT JOIN TripType ON TripType.id=event.trip_type LEFT JOIN Locations ON Locations.name=event.location
WHERE 
   start_time >= NOW() AND
  authentication.member_id = Member.id AND 
  sha1(CONCAT(authentication.password,?))=? AND
  Member.MemberId=? AND owner.id=event.owner ";

if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("sss",
    $secret,$token,$user
);
    if (!$stmt->execute()) {
        error_log("OOOP ".$rodb->error);
        http_response_code(500);
    }
    $result= $stmt->get_result();
    if ($result) {
        $vCalendar = new \Eluceo\iCal\Component\Calendar('aftaler.danskestudentersroklub.dk');
        $vCalendar->setName("DSR Roaftaler");
        $vCalendar->setDescription("Roaftaler i Danske Studenters Roklub");
        error_log("memberical");
        while ($row = $result->fetch_assoc()) {
            $vEvent = new \Eluceo\iCal\Component\Event();
            $vEvent->setCategories(['DSR']);
            $endtime=$row['end_time'];
            if (empty($endtime)) {
                $endtime=new \DateTime($row['start_time']);
                date_modify($endtime, '+2 hour');
            } else {
                $endtime=new \DateTime($row['end_time']);
            }
            $description=$row['owner'] . ": " .  $row['comment'];
            if (!empty($row['boat_category'])) {
                $description .= "\nDer ros i " . $row['boat_category'];
            }
            $vEvent
                ->setDtStart(new \DateTime($row['start_time']))
                ->setDtEnd($endtime)
                ->setNoTime(false)
                ->setDescription($description);
            if (!empty($row['lat']) and !empty($row['lon'])) {
                $llgeo=$row['lat'].';'.$row['lon'];
                $l=$row['location'];
                $vEvent->setSummary("DSR roaftale");
                $vEvent->setSequence(2);
                $vEvent->setLocation($l, 'til roning klar', $llgeo);
            }    
            $vCalendar->addComponent($vEvent);    
        }
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="roaftaler.ics"');
        echo $vCalendar->render();
    } else {
        error_log("ical errro ".mysqli_error($rodb));
        http_response_code(500);
    }
} else {
        error_log("ical prep errro ".mysqli_error($rodb));
        http_response_code(500);
}

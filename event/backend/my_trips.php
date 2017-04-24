<?php
set_include_path(get_include_path().':..');
include("../../rowing/backend/inc/common.php");
header('Content-type: text/csv');
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'On');

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];

$s="SELECT OutTime as tid,Boat.name as boat,Trip.Meter/1000 as km ,Trip.Destination as destination
    FROM Trip,TripMember,Member,Boat
    WHERE TripMember.TripID=Trip.id AND Member.id=TripMember.member_id AND Member.MemberID=? 
       AND Boat.id=Trip.BoatId
    ORDER BY OutTime DESC";

#echo $s;
if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("s",$cuser);
     $stmt->execute(); 
     $result= $stmt->get_result();     
     while ($row = $result->fetch_assoc()) {
         echo $row['tid'];
         echo ','. $row['boat'];
         echo ','. number_format ($row['km'],1) . ',';
         echo ','. $row['destination'];
         echo "\r\n";
     }
     $stmt->close(); 
} else {
    $error=$rodb->error;
    error_log("Error $error");
} 
} else {
    echo "No user";
}

$rodb->close();
?> 

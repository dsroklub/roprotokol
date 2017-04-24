<?php
set_include_path(get_include_path().':..');
include("../../rowing/backend/inc/common.php");

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}


$s="SELECT Week(OutTime) as week, YEAR(OutTime) AS year,CAST(SUM(Meter) AS UNSIGNED) as distance 
    FROM Trip,TripMember,Member,Boat
    WHERE TripMember.TripID=Trip.id AND Member.id=TripMember.member_id AND Member.MemberID=? 
       AND Boat.id=Trip.BoatId
GROUP BY year,week ORDER BY year,week";

#echo $s;
if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("s",$rowerid);
     $stmt->execute(); 
     $result= $stmt->get_result();
     echo '[';
     $rn=1;
     while ($row = $result->fetch_assoc()) {
         if ($rn>1) echo ',';
         echo json_encode($row);
         $rn=$rn+1;
     }
     echo ']';     
     $stmt->close(); 
 } 
$rodb->close();
?> 

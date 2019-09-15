<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$boatclause="";
if (isset($_GET["rower"])) {
    $rowerid=$_GET["rower"];
} else {
    echo "please set rower";
    exit(0);
}


$s="SELECT Week(OutTime) AS week, YEAR(OutTime) AS year,CAST(SUM(Meter) AS UNSIGNED) as distance From Trip,TripMember,Member WHERE TripMember.TripID=Trip.id AND Member.id=TripMember.member_id AND Member.MemberID=?
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

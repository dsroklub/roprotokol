<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

if (isset($_GET["rower"])) {
    $rowerid=$_GET["rower"];
} else {
    echo "please set rower";
    exit(0);
}
$q="none";
if (isset($_GET["q"])) {
    $q=$_GET["q"];
}

if ($q=="mates") {
    $s="SELECT CONCAT(them.FirstName,' ',them.LastName) as mate, SUM(Meter) as dist 
    FROM Member me, Member them,Trip,TripMember tm, TripMember ttm 
    WHERE me.MemberID=? AND tm.TripID=Trip.id AND tm.member_id=me.id AND them.id=ttm.member_id and ttm.TripID=Trip.id AND me.id!=them.id 
    GROUP By mate 
    ORDER BY dist DESC 
    LIMIT 10";
} else {
    echo "invalid query ".$q;
    exit(0);
}

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

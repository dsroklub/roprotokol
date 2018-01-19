<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

if (isset($_GET["boat"])) {
    $boat=$_GET["boat"];
} else {
    echo "please set boat";
    exit(0);
}
$q="none";
if (isset($_GET["q"])) {
    $q=$_GET["q"];
}

if ($q=="rowers") {
    $s="SELECT CONCAT(Member.FirstName,' ',Member.LastName) as rowername, SUM(Meter) as dist 
    FROM Member, Boat,Trip,TripMember tm
    WHERE Boat.id=? AND tm.member_id=Member.id AND Trip.id=tm.TripID AND Trip.BoatID=Boat.id
    GROUP By Member.id 
    ORDER BY dist DESC 
    LIMIT 200";
} else if ($q=="triptypes") {
    $s="SELECT TripType.Name AS triptype, COUNT(Trip.id) as numtrips
    FROM Trip,TripType
    WHERE Trip.BoatID=? AND TripType.id=Trip.TripTypeID
    GROUP By TripType.id
    ORDER BY numtrips DESC 
    LIMIT 20";    
} else {
    echo "invalid query ".$q;
    exit(0);
}

if ($sqldebug) {
    echo $s;
}

if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("i",$boat);
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

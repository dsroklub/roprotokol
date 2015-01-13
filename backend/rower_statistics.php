<?php
include("inc/common.php");
header('Content-type: application/json');

if (isset($_GET["season"])) {
    $season=$_GET["season"];
} else {
  $season=date('Y');
}

$boatclause="";
if (isset($_GET["boattype"])) {
    $boattype=$_GET["boattype"];
    if ($boattype=="any") {
        $boatclause="";
    } elseif ($boattype=="kayak") {
        $boatclause="AND ((BoatType.Category)=1) ";
    } elseif ($boattype=="rowboat") {
        $boatclause="AND ((BoatType.Category)=2)";
    } else {
        error_log('unknown boattype: '.$boattype);
        echo "unknown boattype: ".$boattype;
        exit(0);
    }
}
// echo "boats:". $boatclause."\n<br>";
//$season='2013';
    $s="SELECT CAST(Sum(Meter) AS UNSIGNED) AS distance ,Member.MemberID as id, Member.FirstName as firstname, Member.LastName as lastname 
    FROM BoatType,Trip,TripMember,Boat,Member 
    WHERE 
      Trip.TripID = TripMember.TripID AND
      Member.MemberID = TripMember.MemberID AND
      Boat.id = Trip.BoatID AND     
      BoatType.id = Boat.BoatType AND
      (((Year(OutTime))=".$season.") " . $boatclause .")".
    " GROUP BY Member.MemberID 
    ORDER BY distance desc";


// echo $s;
if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result();
     echo '[';
     $rn=1;
     while ($row = $result->fetch_assoc()) {
         if ($rn>1) echo ',';
         $row['rank']=$rn;
         echo json_encode($row);
         $rn=$rn+1;
     }
     echo ']';     
     $stmt->close(); 
 } 
$rodb->close();
?> 

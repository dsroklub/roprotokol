<?php
include("inc/common.php");

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
    $s="SELECT CAST(Sum(Meter) AS UNSIGNED) AS distance,Member.MemberID as id, GROUP_CONCAT(distinct mr.argument SEPARATOR ',') as wrenches, Member.FirstName as firstname, Member.LastName as lastname, 
    CAST(SUM(IF(season.summer_start<OutTime AND season.summer_end>OutTime,Meter,0)) AS UNSIGNED) AS summer 
    FROM season,BoatType,Trip,TripMember,Boat,
        Member LEFT JOIN MemberRights mr ON Member.id=mr.member_id and mr.MemberRight='wrench'
    WHERE 
      Trip.id = TripMember.TripID AND
      Member.id = TripMember.member_id AND
      Boat.id = Trip.BoatID AND     
      BoatType.id = Boat.BoatType AND
      season.season=Year(OutTime) AND 
      (((Year(OutTime))=?) " . $boatclause .")".
    " GROUP BY Member.MemberID, mr.MemberRight, firstname, lastname 
    ORDER BY distance desc";


if ($sqldebug) echo $s;
if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("s",$season);
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
} else {
  error_log("Could not get rower statistics: " . $rodb->error);  
} 
$rodb->close();
?> 
